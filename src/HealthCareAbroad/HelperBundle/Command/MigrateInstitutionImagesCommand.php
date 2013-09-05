<?php

namespace HealthCareAbroad\HelperBundle\Command;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Gaufrette\Filesystem;

use HealthCareAbroad\MediaBundle\Entity\Media;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMediaService;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class MigrateInstitutionImagesCommand extends ContainerAwareCommand
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
     * @var InstitutionMediaService
     */
    private $institutionMediaService;
    
    /**
     * @var Filesystem
     */
    private $fileSystem;
    
    /**
     * @var array
     */
    private $logoSizes;
    
    /**
     * @var array
     */
    private $featuredMediaSizes;
    
    /**
     * @var array
     */
    private $gallerySizes;
    
    protected function configure()
    {
        $this->setName('script:migrateInstitutionImages')->setDescription('Migrate images to new sizes');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        
        $this->institutionMediaService = $this->getContainer()->get('services.institution.media');
        
        $this->fileSystem = $this->institutionMediaService->getFilesystem();
        
        $this->logoSizes = $this->institutionMediaService->getSizesByType(InstitutionMediaService::LOGO_TYPE_IMAGE);
        
        $this->featuredMediaSizes = $this->institutionMediaService->getSizesByType(InstitutionMediaService::FEATURED_TYPE_IMAGE);
        
        $this->gallerySizes = $this->institutionMediaService->getSizesByType(InstitutionMediaService::GALLERY_TYPE_IMAGE);
        
        // loop through all institutions
        $this->doctrine = $this->getContainer()->get('doctrine');
        $institutions = $this->doctrine->getRepository('InstitutionBundle:Institution')->findAll();
        foreach ($institutions as $_institution) {
            $this->output->writeln('Migrating images of institution #'.$_institution->getId());
            $this->output->write("    ");
            
            // migrate logo
            $this->migrateLogo($_institution);
            $this->output->write("    ");
            
            // migrate banner
            $this->migrateFeaturedMedia($_institution);
            $this->output->write("    ");
            
            // migrate gallery
            $this->migrateGallery($_institution);
            $this->output->write("    ");
            
            // migrate clinic logos
            foreach ($_institution->getInstitutionMedicalCenters() as $imc) {
                $this->migrateClinicLogo($imc);
                $this->output->write("    ");
            }
            
            
            $this->output->writeln('All Done.');
        }
        $this->output->writeln('END OF SCRIPT');
    }
    
    private function migrateClinicLogo(InstitutionMedicalCenter $imc)
    {
        $this->output->write("LOGO of clinic {$imc->getId()}: ");
        $oldDirectory = $this->getWebRootDirectory().'/'.$imc->getInstitution()->getId();
        if ($media = $imc->getLogo()) {
            
            $mediaFile = $oldDirectory.'/'.$media->getName();
            if (\file_exists($mediaFile)){
                $this->doMove($imc->getInstitution(), $media, $this->logoSizes);
                $this->output->write('[OK]');
            }
            else {
                $this->output->write('[NOT FOUND]');
            }
            $this->output->writeln("");
        }
        else {
            $this->output->writeln("NO LOGO");
        }
        
    }
    
    private function migrateLogo(Institution $institution)
    {
        $this->output->write('LOGO: ');
        $oldDirectory = $this->getWebRootDirectory().'/'.$institution->getId();
        
        if ($logo = $institution->getLogo()) {
            
            $oldLogoFile = $oldDirectory.'/'.$institution->getLogo()->getName();
            if (\file_exists($oldLogoFile)){
                $this->doMove($institution, $logo, $this->logoSizes);
                $this->output->write('[OK]');
            }
            else {
                $this->output->write('[NOT FOUND]');
            }
        }
        else {
            $this->output->write('[NONE]');
        }
        $this->output->writeln("");
    }
    
    private function migrateFeaturedMedia(Institution $institution)
    {
        $this->output->write('FEATURED MEDIA: ');
        $oldDirectory = $this->getWebRootDirectory().'/'.$institution->getId();
        if ($media = $institution->getFeaturedMedia()) {
            $oldMediaFile = $oldDirectory.'/'.$media->getName();
            if (\file_exists($oldMediaFile)) {
                $this->doMove($institution, $media, $this->featuredMediaSizes);
                $this->output->write('[OK]');
            }
            else {
                $this->output->write('[NOT FOUND]');
            }
            
        }
        else {
            $this->output->write('[NONE]');
        }
        $this->output->writeln("");
    }
    
    private function migrateGallery(Institution $institution)
    {
        $this->output->write('GALLERY: ');
        $oldDirectory = $this->getWebRootDirectory().'/'.$institution->getId();
        $gallery = array();//$institution->getGallery();
        if ($gallery) {
            foreach ($gallery->getMedia() as $media) {
                $oldMediaFile = $oldDirectory.'/'.$media->getName();
                if (\file_exists($oldMediaFile)) {
                    $this->doMove($institution, $media, $this->gallerySizes);
                    $this->output->write('.');
                }
                else {
                    $this->output->write('!');
                }
            }
            $this->output->writeln("[OK]");
        }
        else {
            $this->output->writeln("[NONE]");
        }
        
    }
    
    private function doMove(Institution $institution, Media $media, $sizes)
    {
        // point file system to new path
        $this->fileSystem->rename($institution->getId().'/'.$media->getName(), $media->getName());
        
        // do resize
        $this->institutionMediaService->resize($media, $sizes);
    }
    
    
    
    private function getWebRootDirectory()
    {
        return \realpath(__DIR__.'/../../../../web/media/institutions');
    }
}