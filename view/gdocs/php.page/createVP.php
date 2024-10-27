<?php
namespace view\gdocs\page;
class createVP extends kupcGDocsVP
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
    $cont .= '<div class="prefBox">'.
               '<div class="caption">Neuen Text anlegen</div>'.
               '<div class="prefLine">'.
                 '<form name="newForm" id="newForm" enctype="multipart/form-data" action="'.$this->linker->href($this->ep, array('hook'=>'manageGDocs', 'op'=>'newEnd')).'" method="POST">'.
                    '<label for="articleName">Lokaler Name<br><input type="text" name="articleName" id="articleName" value="'.$this->getData('articleName').'"></label><br><br>'.
                    '<label for="date">Datum (Jahr-Monat-Tag Uhrzeit)<br><input type="text" name="date" id="date" value="'.$this->fDate($this->getData('date'), "%F %H:%I:%S").'"></label><br><br>'.
                    '<label for="gurl">Google URL<br><input type="text" name="gurl" id="gurl" size="128" value="'.$this->getData('gurl').'"></label><br><br>'.
                    '<a class="cButton b_new" href="javascript:document.newForm.submit();">Anlegen</a>'.
                 '</form>'.
               '</div>'.
             '</div>';

    return $cont;
  }

}


?>
