<?php

namespace HealthCareAbroad\HelperBundle\Command;

use HealthCareAbroad\DoctorBundle\Services\DoctorMediaService;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class MigrateDoctorImagesCommand extends ContainerAwareCommand
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
     * @var DoctorMediaService
     */
    private $mediaService;
    
    protected function configure()
    {
        $this->setName('script:migrateDoctorImages')->setDescription('Migrate images to new sizes');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->mediaService = $this->getContainer()->get('services.doctor.media');
        $this->doctrine = $this->getContainer()->get('doctrine');
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('d, m')
            ->from('DoctorBundle:Doctor', 'd')
            ->innerJoin('d.media', 'm');
            
        $doctors = $qb->getQuery()->getResult();
        $doctorDirectory = $this->getWebRootDirectory();
        
        $logoSizes =  $this->mediaService->getSizesByType(DoctorMediaService::LOGO_TYPE_IMAGE);
        
        foreach ($doctors as $doctor) {
            $this->output->writeln("Migrate images of Doctor #{$doctor->getId()}");
            $this->output->write("    ");
            if ($media = $doctor->getMedia()) {
                $doctorFile = $doctorDirectory.'/'.$media->getName();
                
                if (\file_exists($doctorFile)) {
                    // resize the image
                    $this->mediaService->resize($media, $logoSizes);
                    $this->output->writeln('Ok');
                }
                else {
                    $this->output->writeln('Not found');
                }
            }
            else {
                $this->output->writeln('No media');
            }
        }
        
        $this->output->writeln('All done');
    }
    
    private function getWebRootDirectory()
    {
        return \realpath(__DIR__.'/../../../../web/media/doctors');
    }
}