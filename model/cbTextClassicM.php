<?php

/**
 * the model for the text of classic articles
 * ___________________________________________________________________
 */
class cbTextClassicM
{
  public $articleBox = '';
  public $articleName = '';
  public $articleBoxPath = '';
  
  public $paragraphSep = "\n\n";
  public $text = '';
  
  /**
   * Konstruktor
   * _________________________________________________________________
   */
  public function __construct($articleBox, $articleName)
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

    $this->articleName = $articleName;
    if ($this->articleName == '')
    {
      throw new Exception(__CLASS__.': Der Name des angeforderten Artikels war leer.');
    }

    $this->articleFile = $this->articleBoxPath.$this->articleName.'.txt';
  }
  
  /**
   * Artikel -Text einholen
   * _________________________________________________________________
   */
  public function load()
  {
    if (file_exists($this->articleFile))
    {
      $text = @file_get_contents($this->articleFile);
      
      if ($text !== false)
      {
        $this->text = $text;
        $this->text = str_replace("\r", '', $this->text);
        $this->text = trim($this->text);

        return $this->text;
      }
    }
    throw new Exception(__CLASS__.': Artikeldatei für "'.$this->articleName.'" ('.$this->articleFile.') existiert nicht.');
  }
  
  /**
   * getter for raw article text
   * ________________________________________________________________
   */
  public function getArticleText()
  {
    return $this->text;
  }
  
  /**
   * Parse raw text and extract date, headline and the actual text body
   * Fill in date by filemtime if not provided by the text
   * ___________________________________________________________________
   */
  function getDateHeadlineBody()
  {
    $erg = array();
    $datePattern = "/^([0-9]{2,4}[\-\.\/][0-9]{2,4}[\-\.\/][0-9]{2,4}):(.*)/";

    if ($this->text != '')
    {
      $textChunks = explode($this->paragraphSep, $this->text);
      $firstLine = $textChunks[0];

      if ((preg_match($datePattern, $firstLine, $treffer)) && (count($treffer) != 0))
      {
        $date = $treffer[1];
        $date = strtotime($date);
        $erg['articleDate'] = $date;
        
        $headline = trim($treffer[2]);
        $erg['articleHeadline'] = $headline;
      }
      else
      {
        $date = filemtime($this->articleFile);
        $erg['articleDate'] = $date;
        $erg['articleHeadline'] = $firstLine;
      }
  
      array_splice($textChunks, 0, 1);
      $erg['articleBody'] = implode($this->paragraphSep, $textChunks);

      return $erg;
    }
    
    return false;
  }
  

  /**
   * Text aktualisieren
   * _________________________________________________________________
   */
  public function updateArticleText($text)
  {
    $text = str_replace("\r\n", "\n", $text);
    
    $erg = @file_put_contents($this->articleFile, $text);

    if ($erg === false)
    {
      throw new Exception(__CLASS__.': Artikel "'.$this->articleName.'" konnte nicht aktualisiert werden.');
    }
  }
}

?>
