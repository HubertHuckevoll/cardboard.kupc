<?php
namespace view\classic\page;
class redirectVP extends kupcClassicVP
{
  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct($ep = '', $hook, $linker = null)
  {
    parent::__construct($ep, $hook, $linker);
    $this->active = 'redirect';
  }

  /**
   * global Tools
   * _______________________________________________________________
   */
  protected function mainContent()
  {
    $cont = '';

    $cont .= '<div class="prefBox">'.
              '<div class="caption">Fortfahren...</div>'.
                '<div class="prefLine">'.
                  '<p>'.$this->data['status'].'</p>'.
                  '<a class="cButton" href="'.$this->linker->href($this->ep, $this->data['href']).'">Fortfahren</a>'.
                '</div>'.
            '</div>';

    return $cont;
  }

}

?>
