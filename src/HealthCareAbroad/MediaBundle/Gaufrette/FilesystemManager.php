<?php
namespace HealthCareAbroad\MediaBundle\Gaufrette;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use HealthCareAbroad\MediaBundle\Generator\Path\PathGeneratorInterface;
use HealthCareAbroad\MediaBundle\Gaufrette\Adapter\LocalAdapter;
use Gaufrette\Filesystem;

class FilesystemManager
{
    private $baseUploadRootDir;
    private $uploadRootDir;
    private $pathGenerator;
    private $pathDiscriminator;
    private $pathDiscriminators;
    

    public function __construct(PathGeneratorInterface $pathGenerator, $baseUploadRootDir, $pathDiscriminators)
    {
        $this->pathGenerator = $pathGenerator;
        $this->baseUploadRootDir = $baseUploadRootDir;
        $this->pathDiscriminators = $pathDiscriminators;
    }

    public function get($object, $adapterType = 'local')
    {        
        $this->pathDiscriminator = $this->getPathDiscriminator($object);

        $this->uploadRootDir = $this->pathGenerator->generatePath($this->baseUploadRootDir, $this->pathDiscriminator);

        switch ($adapterType) {
            default:
                $adapter = new LocalAdapter($this->uploadRootDir, true);
        }

        return new Filesystem($adapter);
    }

//     public function getDoctor($adapterType = 'local')
//     {
//         $this->uploadRootDir = $this->pathGenerator->generatePath($this->baseUploadRootDir, 'doctors');
    
//         switch ($adapterType) {
//             default:
//                 $adapter = new LocalAdapter($this->uploadRootDir, true);
//         }

//         return new Filesystem($adapter);
//     }

    /**
     * Convenience functions
     *
     * @return string
     */
    public function getUploadRootDir()
    {
        return $this->uploadRootDir;
    }

    public function getWebRootPath()
    {
        //TODO: use path generator
        return '/media/';
    }
    
    public function getWebPath()
    {
        //TODO: use path generator
        return '/media/' . $this->pathDiscriminator;
    }
    
    private function getPathDiscriminator($object)
    {
        $namespace = get_class($object);
        $namespaceArr = explode('\\', $namespace);
        $class = array_pop($namespaceArr);
        
        try {
            $path = $this->pathDiscriminators[lcfirst($class)];            
        } catch(\Exception $e) {
            $message = "Invalid path discriminator key given $class. Valid keys are [" . implode(', ', array_keys($this->pathDiscriminators)) . "]";
            throw new NotFoundHttpException($message, null, $e->getCode());
        }

        if($object) {
            // TODO - Temporary Fix for advertisement path. Implementation should be change!
            if($class == 'AdvertisementDenormalizedProperty' || $class == 'Advertisement') {
                $object = $object->getInstitution();
            }

            $path = str_replace("{objectId}", $object->getId(), $path);            
        }

        return $path;
    }
}