<?php
namespace HealthCareAbroad\AdminBundle\Services;
use Doctrine\Bundle\DoctrineBundle\Registry;
use HealthCareAbroad\TreatmentBundle\Entity\Specialization;
use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;
use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

/**
 * Migration tool for data in treatment bundle
 * 
 * id: services.admin.treatmentMigrationTool
 * 
 * @author allejochrisvelarde
 *
 */
class TreatmentMigrationToolService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;
    }
    
    /**
     * Migrate and convert $fromSpecialization as a new Sub Specialization of $toSpecialization
     * 
     * Operation is really risky!
     * 
     * @param Specialization $fromSpecialization
     * @param Specialization $toSpecialization
     * @author allejochrisvelarde
     */
    public function migrateSpecializationToAnotherSpecialization(Specialization $fromSpecialization, Specialization $toSpecialization)
    {
        /***
         * Steps for migration
         * 1. Move all institution_specializations of $fromSpecialization to $toSpecialization. Considerations:
         *      a. Check if institution_medical_center_id - $toSpecialization.id combination already exists
         *      b. If institution_medical_center_id - $toSpecialization.id combination exists, update institution_treatments of this IMC with institution specialization belonging to $fromSpecialization
         * 2. Move all treatments of fromSpecialization to toSpecialization. Considerations:
         *      a. Link these treatments to the converted subspecialization [the old fromSpecialization]
         */
        
        $connection = $this->doctrine->getConnection();
        $em = $this->doctrine->getManager();
        
        //----- step 1
        $sql  = "UPDATE IGNORE `institution_specializations` inst_sp SET inst_sp.specialization_id = :toSpecializationId WHERE inst_sp.specialization_id = :fromSpecializationId";
        $statement = $connection->prepare($sql);
        $statement->bindValue('fromSpecializationId', $fromSpecialization->getId());
        $statement->bindValue('toSpecializationId', $toSpecialization->getId());
        $statement->execute();
        
        // what would be left are those medical centers that also have $toSpecialization,
        // so we will just update the institution treatments and point it to the institution specialization with specialization = $toSpecialization
        
        // get remaining imcs with $fromSpecialization 
        $sql = "SELECT imc.*, inst_sp.id as fromInstitutionSpecializationId
                FROM `institution_medical_centers` imc INNER JOIN `institution_specializations` inst_sp ON inst_sp.`institution_medical_center_id` = imc.`id`
                WHERE inst_sp.`specialization_id` = :fromSpecializationId";
        $statement = $connection->prepare($sql);
        $statement->bindValue('fromSpecializationId', $fromSpecialization->getId());
        $statement->execute();
        $imcs = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($imcs as $imc){
            // get the institiution_specialization with $toSpecialization of this imc
            $sql = "SELECT `id` 
                FROM `institution_specializations` inst_sp 
                WHERE inst_sp.`specialization_id` = :toSpecializationId 
                AND inst_sp.`institution_medical_center_id` = :imcId 
                LIMIT 1"; 
            $statement = $connection->prepare($sql);
            $statement->bindValue('toSpecializationId', $toSpecialization->getId());
            $statement->bindValue('imcId', $imc['id']);
            $statement->execute();
            
            $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($data)){
                $toInstitutionSpecializationId = $data[0]['id'];
                
                // update instituion_treatments of this imc linked to $fromSpecialization
                $sql = "UPDATE `institution_treatments` inst_t 
                        SET inst_t.institution_specialization_id = :toInstitutionSpecializationId
                        WHERE inst_t.institution_specialization_id = :fromInstitutionSpecializationId";
                
                $statement = $connection->prepare($sql);
                $statement->bindValue('toInstitutionSpecializationId', $toInstitutionSpecializationId);
                $statement->bindValue('fromInstitutionSpecializationId', $imc['fromInstitutionSpecializationId']);
                $statement->execute();
            }
            
        }
        
        // delete institution specializations linked to $fromSpecialization that were ignored in the above operation
        $qb = $em->createQueryBuilder();
        $qb->delete('InstitutionBundle:InstitutionSpecialization', 'inst_sp')
            ->where('inst_sp.specialization = :fromSpecialization')
            ->setParameter('fromSpecialization', $fromSpecialization);
        $qb->getQuery()->execute();
        
        //----- end of step 1
        
        //----- step 2
        
        // create sub specialization based on $fromSpecialization
        $subSpecialization = new SubSpecialization();
        $subSpecialization->setName($fromSpecialization->getName());
        $subSpecialization->setDescription($fromSpecialization->getDescription());
        $subSpecialization->setSpecialization($toSpecialization); // set to $toSpecialization
        $subSpecialization->setStatus($fromSpecialization->getStatus());
        $em->persist($subSpecialization);
        
        // we check first for duplicate $toSpecialization-treatment name combination
        
        
        
        foreach ($fromSpecialization->getTreatments() as $treatment) {
        	//if ($treatment instanceof Treatment){}
            
            // detach current su
            foreach ($treatment->getSubSpecializations() as $treatmentSub){
                $treatment->removeSubSpecialization($treatmentSub);   
            }
            
            // add this treatment to the converted sub specialization
            $treatment->addSubSpecialization($subSpecialization);
            
            // set specialization to $toSpecialization
            $treatment->setSpecialization($toSpecialization);
        	
        	$em->persist($treatment);
        }
        
        foreach ($fromSpecialization->getSubSpecializations() as $oldSub) {
        	$em->remove($oldSub);
        }
        
        // delete fromSpecialization
        $em->remove($fromSpecialization);
        
        $em->flush();
        
        //----- end of step 2
    }
}