<?php

class cbBuilderClassicM
{
  public $articleBox = '';
  public $cbbr = null;

  /**
   * Konstruktor
   * _________________________________________________________________
   */
  function __construct($articleBox, $cbbr = null)
  {
    $this->articleBox = $articleBox;
    $this->cbbr = $cbbr;
  }

  /**
   * fetch articles and build them
   * _________________________________________________________________
   */
  function load()
  {
    try
    {
      $bb = new cbBoxBuilderClassicM($this->articleBox);
      $docInfos = $bb->load();

      foreach($docInfos as $docInfo)
      {
        $artImp = new cbArticleBuilderClassicM($this->articleBox, $docInfo['articleName'], $this->cbbr);
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
