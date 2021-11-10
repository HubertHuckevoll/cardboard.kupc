<?php

/**
 * the model for GDocs texts - basically reading
 * the provided HTML file.
 * Could be enhanced to provide HTML file writing
 * and replace the bbTag based classic articles completely
 * __________________________________________________________________
 */
class cbTextGDocsM
{
  public $articleBox = '';
  public $articleName = '';
  public $articleBoxPath = '';
  
  public $text = '';
  /**
   * Konstruktor
   * _________________________________________________________________
   */
  public function __construct($articleBox, $articleName)
  {
    if (
        ($articleBox == '') ||
        (!file_exists(getPathFS(CB_DATA_ROOT.$articleBox)))
       )
    {
      throw new Exception(__CLASS__.': Der Name der angeforderten Artikel-Box war leer oder die Box existiert nicht.');
    }

    $this->articleBox = $articleBox;
    $this->articleName = $articleName;
    $this->articleBoxPath = getPathFS(CB_DATA_ROOT.$this->articleBox.CB_DATA_TEXT);
    $this->articleFile = $this->articleBoxPath.$this->articleName.'.html';
  }
  
  /**
   * implementing a load function saves us a lot of headaches,
   * as we're forced to call load before being able to do anything.
   * this way we ensure the text is loaded and subsequent calls can
   * make use of the cached data.
   * ________________________________________________________________
   */
  public function load()
  {
    if (file_exists($this->articleFile))
    {
      $data = @file_get_contents($this->articleFile);
      
      if ($data !== false)
      {
        $this->text = $data;
        return $this->text;
      }
    }
    throw new Exception(__CLASS__.': Artikel "'.$this->articleName.'" ('.$this->articleFile.') konnte nicht geladen werden.');
  }

  /**
   * get text
   *__________________________________________________________________
   */
  public function getArticleText()
  {
    return $this->text;
  }
}

?>
