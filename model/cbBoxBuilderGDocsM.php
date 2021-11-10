<?php

class cbBoxBuilderGDocsM
{
  public $articleBox = null;
  public $textPath = null;
  public $textIdxF = null;
  public $builtBoxPath = null;
  public $builtBoxIdxF = null;

  /**
   * Konstruktor
   * _________________________________________________________________
   */
  function __construct($articleBox)
  {
    if ($articleBox == '')
    {
      throw new Exception(__CLASS__.': Der Box-Name war leer.');
    }

    $this->articleBox = $articleBox;
    
    $this->textPath = getPathFS(CB_DATA_ROOT.$this->articleBox.CB_DATA_TEXT);
    $this->textIdxF = $this->textPath.'index.json';
    
    $this->builtBoxPath = getPathFS(CB_DATA_ROOT.$this->articleBox.CB_BUILT);
    $this->builtBoxIdxF = $this->builtBoxPath.$this->articleBox.'.index.json';
  }

  /**
   * Laden und verarbeiten
   * _________________________________________________________________
   */
  function load()
  {
    $articles = array();

    try
    {
      $crud = new cbTextCrudGDocsM($this->articleBox);
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
