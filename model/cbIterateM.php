<?php

/**
 * Tool class
 * iterates over all folders in CB_DATA_ROOT and all files/folders
 * within.
 * helpful for conversion jobs
 * do not use in production.
 * __________________________________________________________________
 */
class cbIterateM
{
  public function __construct()
  {
    $this->root = getPathFS(CB_DATA_ROOT);
    $this->root = rtrim($this->root, "/");
  }
  
  /**
   * iterates over all folders and files recursively
   * takes callback function that is applied to each item
   * the callback function is passed a structure with meta info for
   * each item.
   * ________________________________________________________________
   */
  public function iterate($dir, $onIterate)
  {
    $result = array();
    $items = scandir($dir);
    
    foreach ($items as $item)
    {
      if (
          ($item != '.') &&
          ($item != '..')
         )
      {
        $path = $dir.DIRECTORY_SEPARATOR.$item;
        
        $result['item'] = $item; // current file / folder
        
        $result['parentFolder'] = substr($dir, strlen($this->root)); // parent dir
        $result['rootedParentFolder'] = $dir; // rooted parent dir

        $result['pathToItem'] = substr($path, strlen($this->root)); // path to current file / folder
        $result['rootedPathToItem'] = $path; // rooted path to current file / folder

        if (is_dir($path))
        {
          $result['isdir'] = true;
          $onIterate($result);
          $this->iterate($path, $onIterate);
        }
        elseif (is_file($path))
        {
          $result['isdir'] = false;
          $onIterate($result);
        }
      }
    }
  }
  
  /**
   * start walking
   * ________________________________________________________________
   */
  public function start($onIterate)
  {
    $this->iterate($this->root, $onIterate);
  }

}


?>