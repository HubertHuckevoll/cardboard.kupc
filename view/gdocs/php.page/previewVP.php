<?php
namespace view\gdocs\page;
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

    $cont .= '<style>'.$pa['styles'].'</style>';
    $i = 0;
    foreach ($pa['paginatedText'] as $page)
    {
      $cont .= '<h2>'.$pa['pagesInfo'][$i].'</h2>';
      $cont .= '<div style="margin-bottom: 15px;">'.$page.'</div>';
      $i++;
    }

    echo $cont;
  }
}

?>
