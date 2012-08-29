<?php
namespace HealthCareAbroad\MediaBundle\Gaufrette\Adapter;

use Gaufrette\Adapter\Local;

/**
 * TODO: This is just a temporary workaround for the missing implementation of
 * listDirectory. Most likely this will be present in the Local class in the future.
 * 
 * @author harold
 *
 */
class LocalAdapter extends Local
{
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
}