<?php

class cbTextCrudClassicM
{
  protected $articleBox = '';
  protected $articleBoxPath = '';
  
  protected $articles = array();

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

    $this->articleBoxPath = getPathFS(CB_DATA_ROOT.$this->articleBox.CB_DATA_TEXT);
    if (!file_exists($this->articleBoxPath))
    {
      @mkdir($this->articleBoxPath, 0777, true); //create directories recursively
    }

  }
  
  /**
   * fetch Articles and sort them in descending order by date
   * _________________________________________________________________
   */
  public function load()
  {
    $articleList = array();
    $data = array();
    
    try
    {
      $articleNames = scanpath($this->articleBoxPath);
      
      foreach($articleNames as $articleName)
      {
        $data['articleBox']  = $this->articleBox;
        $articleName         = getFileBasename($articleName);
        $data['articleName'] = $articleName;
        $artDate             = $this->extractDateFromArticleTxt($articleName);
        $data['date']        = $artDate;
    
        $articleList[$artDate.'-'.md5($articleName)] = $data;
      }
      
      $this->articles = $articleList;
    }
    catch (Exception $e)
    {
      $this->articles = array();
    }
  }
  
  /**
   * fetch date
   * ___________________________________________________________________
   */
  function extractDateFromArticleTxt($articleName)
  {
    $tM = new cbTextClassicM($this->articleBox, $articleName);
    $tM->load();
    $dhb = $tM->getDateHeadlineBody();
    
    return $dhb['articleDate'];
  }
  
  /**
   * return articles
   * _________________________________________________________________
   */
  public function getArticles()
  {
    krsort($this->articles);
    return $this->articles;
  }

  /**
   * return articles
   * _________________________________________________________________
   */
  public function getArticle($article)
  {
    $art = $this->articles[$this->getKeyForName($article)];
    if ($art !== false)
    {
      return $art;
    }
    else
    {
      throw new Exception(__CLASS__.': Der angeforderte Artikel konnte nicht gefunden werden.');
    }
  }

  /**
   * neuen Artikel anlegen
   * _________________________________________________________________
   */
  public function newArticle($newArticleName)
  {
    $ret = NULL;

    if ($newArticleName != '')
    {
      $file = $this->getArticleFileName($newArticleName);
      
      if (!file_exists($file))
      {
        $d = time();
        file_put_contents($file, $d.': '.$newArticleName);
        
        $entry = array(
                  'articleBox' => $this->articleBox,
                  'articleName' => $newArticleName,
                  'date' => $d
                 );
    
        $this->articles[$entry['date'].'-'.md5($entry['articleName'])] = $entry;
      }
      else
      {
        throw new Exception(__CLASS__.': Ein Artikel dieses Namens existiert bereits.');
      }
    }
    else
    {
      throw new Exception(__CLASS__.': Der übergebene Artikelname war leer.');
    }
  }
  
  /**
   * Artikel löschen
   * _________________________________________________________________
   */
  public function deleteArticle($article)
  {
    $file = $this->getArticleFileName($article);

    if (file_exists($file))
    {
      if (@unlink($file))
      {
        $key = $this->getKeyForName($article);
        if ($key !== false)
        {
          unset($this->articles[$key]);
        }
      }
      else
      {
        throw new Exception(__CLASS__.': Artikel-Datei ("'.$file.'") konnte nicht gelöscht werden.');
      }
    }
  }

  /**
   * Artikel umbenennen
   * _________________________________________________________________
   */
  public function renameArticle($article, $newname)
  {
    if (file_exists($this->getArticleFileName($article)))
    {
      $erg = @rename($this->getArticleFileName($article), $this->getArticleFileName($newname));
      if ($erg === true)
      {
        $key = $this->getKeyForName($article);
        if ($key !== false)
        {
          $entry = $this->getArticle($article);
          unset($this->articles[$key]);
          
          $entry['articleName'] = $newname;
          $this->articles[$entry['date'].'-'.md5($entry['articleName'])] = $entry;
        }
      }
      else
      {
        throw new Exception(__CLASS__.': Umbenennen fehlgeschlagen.');
      }
    }
  }

  /**
   * Pfad für Artikel zurückgeben
   * _________________________________________________________________
   */
  public function getArticleFileName($article)
  {
    return $this->articleBoxPath.$article.'.txt';
  }
  
  /**
   * getKeyForName
   * _________________________________________________________________
   */
  public function getKeyForName($articleName)
  {
    foreach ($this->articles as $artKey => $article)
    {
      if ($article['articleName'] == $articleName)
      {
        return $artKey;
      }
    }
    return false;
  }
}

?>
