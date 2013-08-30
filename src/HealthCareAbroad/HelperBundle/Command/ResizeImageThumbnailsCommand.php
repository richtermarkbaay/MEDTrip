<?php
/**
 * 
 * @author adelbertsilla
 * NOTE: Please read the configuration's arguments and options to see the usage.
 * 
 */

namespace HealthCareAbroad\HelperBundle\Command;

use Doctrine\ORM\AbstractQuery;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ResizeImageThumbnailsCommand extends ContainerAwareCommand
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var OutputInterface
     */
    private $output;
    
    protected function configure()
    {
        $this->setName('script:resizeImageThumbnails')->setDescription('Resize Entity image given the Entity name and field name.');

        $this->addArgument('entity', InputArgument::REQUIRED, 'name of entity with bundle name prefix. Ex: InstitutionBundle:Institution');
        $this->addArgument('property', InputArgument::REQUIRED, 'field name. Value MUST be in camelCase format');
        $this->addArgument('width', InputArgument::REQUIRED, 'Image Width');
        $this->addArgument('height', InputArgument::REQUIRED, 'Image Height');

        $this->addOption('crop-image', null, InputOption::VALUE_NONE, 'Crop Image.');
        $this->addOption('entity-id', null, InputOption::VALUE_REQUIRED, 'Entity ID.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->doctrine = $this->getContainer()->get('doctrine');

        $entity = $input->getArgument('entity');
        $property = $input->getArgument('property');
        $width = $input->getArgument('width');
        $height = $input->getArgument('height');
        $size = array($width . 'x' . $height);
        $cropImage = $input->getOption('crop-image');

        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('a')->from($entity, 'a')->where("a.$property IS NOT NULL");

        if($entityId = $input->getOption('entity-id')) {
            $qb->andWhere('a.id = :id')->setParameter('id', $entityId);
        }

        $objects = $qb->getQuery()->getResult();

        $entityArr = explode(':', $entity);
        if($entityArr[1] == 'InstitutionMedicalCenter') {
            $entityArr[1] = 'institution';
        }

        $mediaServiceName = 'services.'.lcfirst($entityArr[1]).'.media';        
        $mediaService =   $this->getContainer()->get($mediaServiceName);        
        $propertyGetMethod = 'get' . ucfirst($property);

        foreach($objects as $each) {
            $media = $each->{$propertyGetMethod}();
            
            if($media && $mediaService->getFilesystem()->has($media->getName())) {
                $this->output->write('Resizing image: ' . $media->getName() . ' (id: ' . $media->getId() . ') ... ');
                $mediaService->resize($media, $size, $cropImage);
                
                $this->output->writeln('DONE');                
            }
        }

        $this->output->writeln('END OF SCRIPT');
    }
}