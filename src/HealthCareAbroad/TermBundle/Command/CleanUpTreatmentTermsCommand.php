<?php

namespace HealthCareAbroad\TermBundle\Command;

use Doctrine\ORM\Query\Expr\Join;

use HealthCareAbroad\TermBundle\Entity\Term;

use Doctrine\Common\Persistence\ObjectManager;

use HealthCareAbroad\TermBundle\Repository\TermRepository;

use HealthCareAbroad\TermBundle\Entity\TermDocument;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Doctrine\DBAL\Connection;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CleanUpTreatmentTermsCommand extends ContainerAwareCommand
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var OutputInterface
     */
    private $output;
    
    /**
     * @var TermRepository
     */
    private $termRepository;
    
    /**
     * @var ObjectManager
     */
    private $om;
    
    protected function configure()
    {
        $this->setName('term:cleanUpTreatmentTerms')->setDescription('Cleanup terms for treatments');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->om = $this->doctrine->getManager();
        $this->connection = $this->doctrine->getConnection();
        $this->output = $output;
        
        $this->termRepository = $this->doctrine->getRepository('TermBundle:Term');
        
        // reset internal flag to 0 for terms linked to a treatment
        $this->resetInternalFlagOfTermsWithTreatmentTypeDocuments();
        
        // get all treatments
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('t1')->from('TreatmentBundle:Treatment', 't1')->orderBy('t1.name', 'ASC');
        $treatments = $qb->getQuery()->getResult();
        foreach ($treatments as $treatment) {
            $this->cleanupTerms($treatment);
        }
        
        $this->om->flush();
    }
    
    private function resetInternalFlagOfTermsWithTreatmentTypeDocuments()
    {
        $updateSql = "UPDATE `terms` t2 INNER JOIN `term_documents` td ON td.term_id = t2.id AND td.type = 3 SET t2.internal = 0";
        $statement = $this->connection->prepare($updateSql);
        $statement->execute();
    }
    
    private function cleanupTerms(Treatment $treatment)
    {
        // find terms for this treatment
        $qb = $this->om->createQueryBuilder();
        $qb->select('t2')
            ->from('TermBundle:Term', 't2')
            ->innerJoin('t2.termDocuments', 'td', Join::WITH, 'td.type = :treatmentType')
                ->setParameter('treatmentType', TermDocument::TYPE_TREATMENT)
            ->where('td.documentId = :treatmentId')
                ->setParameter('treatmentId', $treatment->getId())
        ;
        
        $terms = $qb->getQuery()->getResult();
        
        
        $this->output->writeln('Cleaning terms for treatment #'.$treatment->getId().' ['.$treatment->getName().']');
        if (\count($terms)) {
            $hasInternal = false;
            foreach ($terms as $term) {
                $this->output->write($this->indent(). $term->getName()." [#{$term->getId()}]".$this->indent());
                if (!$term->getInternal()){
                    // if this has not been flagged as internal yet, flag it
                    $term->setInternal(\strtolower($term->getName())==\strtolower($treatment->getName()));
                }
                
                if (!$hasInternal) {
                    $hasInternal = $term->getInternal();
                }
                
                $this->om->persist($term);
                
                $this->output->writeln('[OK]');
            }
            
            if (!$hasInternal) {
                 
                $term = $this->createTermFromTreatment($treatment);
                $this->om->persist($term);
                $this->output->writeln($this->indent().'Added internal term');
            }
        }
        // no  terms for this treatment
        else {
            
            $this->output->write($this->indent()."Found no terms: ");
            
            $term = $this->createTermFromTreatment($treatment);
            
            $this->om->persist($term);
            
            $this->output->writeln('[OK]');
        }
    }
    
    /**
     * 
     * @param Treatment $treatment
     * @return \HealthCareAbroad\TermBundle\Entity\Term
     */
    private function createTermFromTreatment(Treatment $treatment)
    {
        // check first if this term already exists
        $term = $this->termRepository->findOneByName($treatment->getName());
        if (!$term) {
            // term does not exist
            $term = new Term();
            $term->setName($treatment->getName());
        }
        
        // create new term document
        $termDocument = new TermDocument();
        $termDocument->setDocumentId($treatment->getId());
        $termDocument->setTerm($term);
        $termDocument->setType(TermDocument::TYPE_TREATMENT);
        $term->setInternal(true);
        $term->addTermDocument($termDocument);
        
        return $term;
    }
    
    private function indent()
    {
        return '    ';
    }
}