<?php

class cbBoxBuilderClassicM
{
  public $articleBox = '';

  public $builtBoxPath = '';
  public $builtBoxIdxF = '';
  
  /**
   * Konstruktor
   * _________________________________________________________________
   */
  public function __construct($articleBox)
  {
    $this->articleBox = $articleBox;
    if ($this->articleBox == '')
    {
      throw new Exception(__CLASS__.': Der Name der angeforderten Artikel-Box war leer.');
    }

    $this->builtBoxPath = getPathFS(CB_DATA_ROOT.$articleBox.CB_BUILT);
    $this->builtBoxIdxF = $this->builtBoxPath.$this->articleBox.'.index.json';
  }

  /**
   * fetch Articles and sort them in descending order by date
   * _________________________________________________________________
   */
  public function load()
  {
    try
    {
      $crud = new cbTextCrudClassicM($this->articleBox);
      $crud->load();
      
      $articleList = $crud->getArticles();
      
      $this->cleanFiles($articleList);
      
      jsonM::save($this->builtBoxIdxF, $articleList);
      
      return $articleList;
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }
  
  
  /**
   * cleanup old built files
   * __________________________________________________________________
   */
  public function cleanFiles($articles)
  {
    $found = false;
    $bFiles = scanpath($this->builtBoxPath);
    
    foreach($bFiles as $bFile)
    {
      if (
           (strpos($bFile, '.index.json') === false)
         )
      {
        $found = false;
        foreach($articles as $article)
        {
          if (getFileBasename($bFile) == $article['articleName'])
          {
            $found = true;
            break;
          }
        }
        
        if (!$found)
        {
          @unlink($this->builtBoxPath.$bFile);
        }
      }
    }
  }

}

?>
