<?php

class cbTextCrudGDocsM
{
  public $articleBox = '';

  public $textPath = null;
  public $textIdxF = null;

  public $articles = array();

  /**
   * Konstruktor
   * _________________________________________________________________
   */
  public function __construct($articleBox)
  {
    if ($articleBox == '')
    {
      throw new Exception(__CLASS__.': Der Box-Name war leer.');
    }

    $this->articleBox = $articleBox;
    $this->textPath = getPathFS(CB_DATA_ROOT.$this->articleBox.CB_DATA_TEXT);
    if (!file_exists($this->textPath))
    {
      @mkdir($this->textPath, 0777, true); //create directories recursively
    }
    $this->textIdxF = $this->textPath.'index.json';
  }

  /**
   * Laden
   * _________________________________________________________________
   */
  public function load()
  {
    $jsonObj = false;

    try
    {
      $jsonObj = jsonM::load($this->textIdxF);
      $this->articles = $jsonObj;
    }
    catch (Exception $e)
    {
      $this->articles = array();
    }
  }

  /**
   * API function
   * _________________________________________________________________
   */
  public function getArticles()
  {
    krsort($this->articles);
    return $this->articles;
  }
  
  /**
   * get certain item, just meta data
   *__________________________________________________________________
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
   * get certain item text
   *__________________________________________________________________
   */
  public function getArticleText($article)
  {
    $fname = $this->textPath.$article.'.html';
    if (file_exists($fname))
    {
      $data = @file_get_contents($fname);
      return $data;
    }
    throw new Exception(__CLASS__.': Der angeforderte Artikel konnte nicht gefunden werden.');
  }

  /**
   * add entry
   * _________________________________________________________________
   */
  public function newArticle($name, $date, $gurl)
  {
    if (($name != '') && ($date != '') && ($gurl != ''))
    {
      if ($this->getKeyForName($name) == false)
      {
        $fname = $this->textPath.$name.'.html';
        $html = file_get_contents($gurl);
        if ($html !== false)
        {
          if (file_put_contents($fname, $html) !== false)
          {
            $entry = array(
                      'articleBox' => $this->articleBox,
                      'articleName' => $name,
                      'date' => $date,
                      'gurl' => $gurl
                     );
        
            $this->articles[$entry['date'].'-'.md5($entry['articleName'])] = $entry;
            $this->save();
            
            return $entry;
          }
          else
          {
            throw new Exception(__CLASS__.': Konnte den heruntergeladenen GDocs-Artikel nicht schreiben.');
          }
        }
        else
        {
          throw new Exception(__CLASS__.': Konnte den GDocs-Artikel nicht herunterladen.');
        }
      }
      else
      {
        throw new Exception(__CLASS__.': Ein Artikel mit dem angegebenen Namen existiert bereits.');
      }
    }
    else
    {
      throw new Exception(__CLASS__.': Es wurde kein (gültiger) Artikelname, kein Datum oder keine GDocs-URL angegeben.');
    }
  }

  /**
   * Aktualisieren: umbenennen, neu downloaden, datum ändern....
   * this is a little a brute force solution: delete the
   * article completely and re-add it as new.
   * but it should be rock-solid, because of all the error handling
   * in the called functions...
   * _________________________________________________________________
   */
  public function updateArticle($article, $newArticleName, $date, $gurl)
  {
    try
    {
      $this->deleteArticle($article);
      return $this->newArticle($newArticleName, $date, $gurl);
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }

  /**
   * Delete
   * _________________________________________________________________
   */
  public function deleteArticle($article)
  {
    try
    {
      $fname = $this->textPath.$article.'.html';
  
      if (file_exists($fname))
      {
        if (@unlink($fname))
        {
          $key = $this->getKeyForName($article);
          if ($key !== false)
          {
            unset($this->articles[$key]);
            $this->save();
          }
        }
        else
        {
          throw new Exception(__CLASS__.': Konnte Datei nicht entfernen ('.$fname.').');
        }
      }
      else
      {
        $key = $this->getKeyForName($article);
        if ($key !== false)
        {
          unset($this->articles[$key]);
          $this->save();
        }
      }
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }
  
  /**
   * save
   * _________________________________________________________________
   */
  public function save()
  {
    try
    {
      jsonM::save($this->textIdxF, $this->articles);
    }
    catch (Exception $e)
    {
      throw $e;
    }
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
