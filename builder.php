<?php

class builder
{
  public $boxes = null;
  public $gbbr = null;
  public $cbbr = null;
  
  /**
   * base class for builder shell script
   * _________________________________________________________________
   */
  public function __construct($cbbr = null, $gbbr = null)
  {
    // Read in boxes.json
    try
    {
      $this->boxes = new cbBoxesM();
      $this->boxes->load();
      
      $this->cbbr = ($cbbr == null) ? new cbTagRendererClassicM() : $cbbr;
      $this->gbbr = ($gbbr == null) ? new cbTagRendererGDocsM()   : $gbbr;
    }
    catch(Exception $e)
    {
      echo $e->getMessage()."\r\n";
    }
  }
  
  /**
   *  what => all, gdocs, classic
   *  which => name of the box. if omitted, build all
   * _________________________________________________________________
   */
  public function run($what = 'all', $filterOp = null, $filterData = null)
  {
    $boxes = $this->boxes->filter($filterOp, $filterData);
    try
    {
      foreach($boxes as $box)
      {
        if ((($what == 'classic') || ($what == 'all')) && ($box['type'] == 'classic'))
        {
          $d = new cbBuilderClassicM($box['box'], $this->cbbr);
          $d->load();
        }
        
        if ((($what == 'gdocs') || ($what == 'all')) && ($box['type'] == 'gdocs'))
        {
          $d = new cbBuilderGDocsM($box['box'], $this->gbbr);
          $d->load();
        }
      }
    }
    catch(Exception $e)
    {
      echo $e->getMessage()."\r\n";
    }
  }
}

?>
