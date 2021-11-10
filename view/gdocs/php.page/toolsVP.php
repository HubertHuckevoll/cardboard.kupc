<?php
namespace view\gdocs\page;
class toolsVP extends kupcGDocsVP
{
  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct($ep = '', $hook, $linker = null)
  {
    parent::__construct($ep, $hook, $linker);
    $this->active = 'tools';
  }

  /**
   * Tools
   * _______________________________________________________________
   */
  protected function mainContent()
  {
    $cont  = '';

    $cont .= '<div class="prefBox">'.
               '<div class="caption">Artikel aktualisieren</div>'.
               '<div class="prefLine">'.
                 '<form name="newForm" id="newForm" enctype="multipart/form-data" action="'.$this->linker->href($this->ep, array('hook'=>'manageGDocs', 'op'=>'update')).'" method="POST">'.
                    '<input type="hidden" name="articleName" id="articleName" value="'.$this->getData('articleName').'">'.
                    '<label for="newArticleName">Lokaler Name<br><input type="text" name="newArticleName" id="newArticleName" value="'.$this->getData('articleName').'"></label><br><br>'.
                    '<label for="date">Datum (Jahr-Monat-Tag Uhrzeit)<br><input type="text" name="date" id="date" value="'.$this->fDate($this->getData('date'), "%F %H:%I:%S").'"></label><br><br>'.
                    '<label for="gurl">Google URL<br><input type="text" name="gurl" id="gurl" size="128" value="'.$this->getData('gurl').'"></label><br><br>'.
                    '<a class="cButton b_new" href="javascript:document.newForm.submit();">Aktualisieren</a>'.
                 '</form>'.
               '</div>'.
             '</div>';

    $cont .= '<div class="prefBox">'.
               '<div class="caption">Löschen</div>'.
               '<div class="prefLine">'.
                 '<a class="cButton b_delete" href="'.$this->linker->href($this->ep, array('hook'=>'manageGDocs', 'op'=>'remove', 'article'=>$this->getData('article'))).'">Artikel Löschen (ohne Rückfrage)</a>'.
               '</div>'.
             '</div>';

    return $cont;
  }
}

?>
