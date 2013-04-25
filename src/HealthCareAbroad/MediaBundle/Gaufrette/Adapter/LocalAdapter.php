<?php
namespace HealthCareAbroad\MediaBundle\Gaufrette\Adapter;

use Gaufrette\Adapter\Local;


class LocalAdapter extends Local
{
    protected $url;
    
    function __construct($mediaDirectory, $directory, $create)
    {
        parent::__construct($directory, $create);

        list($dummy, $subdir) = explode("/$mediaDirectory/", $directory, 2);
        
        $this->url = "/$mediaDirectory/$subdir";
    }

	/**
	 * Lists files from the specified directory.
	 *
	 * @param string $directory The path of the directory to list from
	 *
	 * @return array An array of keys and dirs
	 */
	public function listDirectory($directory = '')
	{
		$directory = preg_replace('/^[\/]*([^\/].*)$/', '/$1', $directory);
		$files = $dirs = array();
	
		if (is_dir($this->directory.$directory)) {
			$iterator = new \DirectoryIterator($this->directory.$directory);
	
			foreach ($iterator as $fileinfo) {
				if ($fileinfo->isFile()) {
					$files[] = $fileinfo->getFilename();
				} elseif ($fileinfo->isDir() && !$fileinfo->isDot()) {
					$dirs[] = $fileinfo->getFilename();
				}
			}
		}
	
		return array(
				'keys' => $files,
				'dirs' => $dirs
		);
	}

	public function getDirectory()
	{
	    return $this->directory;
	}

	function getUrl()
	{
	    return $this->url;
	}
}