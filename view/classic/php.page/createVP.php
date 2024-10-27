<?php
namespace view\classic\page;
class createVP extends kupcClassicVP
{
  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct($ep, $hook, $linker = null)
  {
    parent::__construct($ep, $hook, $linker);
    $this->active = 'create';
  }

  /**
   * Artikel anlegen
   * _______________________________________________________________
   */
  protected function mainContent()
  {
    $cont  = '';

    $cont .= '<div class="prefBox">'.
               '<div class="caption">Name für neuen Text eingeben</div>'.
               '<div class="prefLine">'.
                 '<form name="newForm" id="newForm" enctype="multipart/form-data" action="'.$this->linker->href($this->ep, array('hook'=>'manageClassic', 'op'=>'newEnd')).'" method="POST">'.
                    '<input type="text" name="newArticleName" id="newArticleName" value="'.$this->getData('articleName').'">&nbsp;'.
                    '<a class="cButton b_new" href="javascript:document.newForm.submit();">Anlegen</a>'.
                 '</form>'.
               '</div>'.
             '</div>';

    return $cont;
  }

}


?>
