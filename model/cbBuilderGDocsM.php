<?php

class cbBuilderGDocsM
{
  public $articleBox = null;
  public $gbbr = null;

  /**
   * Konstruktor
   * _________________________________________________________________
   */
  public function __construct($articleBox, $gbbr = null)
  {
    $this->articleBox = $articleBox;
    $this->gbbr = $gbbr;
  }

  /**
   * Load
   * _________________________________________________________________
   */
  public function load()
  {
    try
    {
      $artBoxImp = new cbBoxBuilderGDocsM($this->articleBox);
      $docInfos = $artBoxImp->load();
      
      foreach($docInfos as $docInfo)
      {
        $artImp = new cbArticleBuilderGDocsM($this->articleBox, $docInfo, $this->gbbr);
        $artContent = $artImp->load();
      }
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }
}

?>
