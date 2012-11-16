<?php
namespace HealthCareAbroad\MediaBundle\Gaufrette;

use HealthCareAbroad\MediaBundle\Generator\Path\PathGeneratorInterface;
use HealthCareAbroad\MediaBundle\Gaufrette\Adapter\LocalAdapter;
use Gaufrette\Filesystem;

class FilesystemManager
{
    private $baseUploadRootDir;
    private $uploadRootDir;
    private $pathGenerator;

    public function __construct(PathGeneratorInterface $pathGenerator, $baseUploadRootDir)
    {
        $this->pathGenerator = $pathGenerator;
        $this->baseUploadRootDir = $baseUploadRootDir;
    }

    public function get($institutionId, $adapterType = 'local')
    {
        $this->uploadRootDir = $this->pathGenerator->generatePath($this->baseUploadRootDir, $institutionId);
var_dump($this->uploadRootDir); exit;
        switch ($adapterType) {
            default:
                $adapter = new LocalAdapter($this->uploadRootDir, true);
        }

        return new Filesystem($adapter);
    }

    /**
     * Convenience functions
     *
     * @return string
     */
    public function getUploadRootDir()
    {
        return $this->uploadRootDir;
    }

    public function getAdsUploadRootDir()
    {
        return $this->uploadRootDir . "/ads";
    }

    public function getWebRootPath()
    {
        //TODO: use path generator
        return '/media/';
    }
}