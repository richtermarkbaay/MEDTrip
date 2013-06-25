<?php

namespace HealthCareAbroad\HelperBundle\Command;

use HealthCareAbroad\TreatmentBundle\Services\SpecializationMediaService;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class MigrateSpecializationImagesCommand extends ContainerAwareCommand
{
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var OutputInterface
     */
    private $output;
    
    /**
     * @var SpecializationMediaService
     */
    private $mediaService;
    
    protected function configure()
    {
        $this->setName('script:migrateSpecializationImages')->setDescription('Migrate specialization images to new sizes');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->mediaService = $this->getContainer()->get('services.specialization.media');
        $this->doctrine = $this->getContainer()->get('doctrine');
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('s, m')
            ->from('TreatmentBundle:Specialization', 's')
            ->innerJoin('s.media', 'm');
        $specializations = $qb->getQuery()->getResult();
        $directory = $this->getWebRootDirectory();
        $logoSizes =  $this->mediaService->getSizesByType(SpecializationMediaService::LOGO_TYPE_IMAGE);
        foreach ($specializations as $specialization) {
            $media = $specialization->getMedia();
            $mediaFile = $directory.'/'.$media->getName();
            
            $this->output->writeln($mediaFile);
            if (\file_exists($mediaFile)) {
                $this->mediaService->resize($media, $logoSizes);
                $this->output->write('OK');
            }
            else {
                $this->output->write('NOT FOUND');
            }
        }
    }
    
    private function getWebRootDirectory()
    {
        return \realpath(__DIR__.'/../../../../web/media/specializations');
    }
    
}