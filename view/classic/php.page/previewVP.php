<?php
namespace view\classic\page;
class previewVP extends \cb\view\page\cbPageVP
{
  /**
   * Preview - Ajax
   * _______________________________________________________________
   */
  public function drawAjax()
  {
    $cont = '';
    $pa = $this->data['article'];

    foreach ($pa['paginatedText'] as $page)
    {
      foreach($page as $para)
      {
        $cont .= '<div style="margin-bottom: 15px;">'.$para.'</div>';
      }
    }

    echo $cont;
  }
}


?>
