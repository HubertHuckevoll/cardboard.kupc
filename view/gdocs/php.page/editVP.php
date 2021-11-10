<?php
namespace view\gdocs\page;
class editV extends kupcGDocsVP
{
  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct($ep = '', $hook, $linker = null)
  {
    parent::__construct($ep, $hook, $linker);
    $this->active = 'edit';
  }

  /**
   * Edit
   * _______________________________________________________________
   */
  protected function mainContent()
  {
    $article = $this->data['article'];
    $text = $this->data['text'];

    $cont .= '<span id="saveButton" style="display: none" data-article="'.$article.'"></span>'; // FIXME

    $cont .= '<div id="textInfoBoxWrapper">';

      $cont .= '<textarea readonly name="data" id="data">'.$text.'</textarea>';

      $cont .= '<div id="infoBox">';
        $cont .= '<div id="preview"></div>';
      $cont .= '</div>'; // infoBox

    $cont .= '</div>';

    return $cont;
  }

}

?>
