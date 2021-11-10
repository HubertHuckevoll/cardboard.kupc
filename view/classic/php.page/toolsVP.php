<?php
namespace view\classic\page;
class toolsVP extends kupcClassicVP
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
   * Tools: Rename & Delete
   * _______________________________________________________________
   */
  protected function mainContent()
  {
    if (!$this->data['internal'])
    {
      $cont .= '<div class="prefBox">'.
                  '<div class="caption">Umbenennen</div>'.
                  '<form class="prefLine" name="renameForm" id="renameForm" enctype="multipart/form-data" action="'.$this->linker->href($this->ep, array('hook'=>'manageClassic', 'op'=>'rename', 'article'=>$this->getData('article'))).'" method="POST">'.
                     '<input type="text" name="newname" id="newname" value="'.$this->getData('article').'"></input>&nbsp;'.
                     '<a class="cButton" href="javascript:document.renameForm.submit();">Umbenennen</a>'.
                  '</form>'.
               '</div>';

      $cont .= '<div class="prefBox">'.
                 '<div class="caption">Verwaltung</div>'.
                 '<div class="prefLine">'.
                    '<a class="cButton" href="'.$this->linker->href($this->ep, array('hook'=>'manageClassic', 'op'=>'remove', 'article'=>$this->getData('article'))).'">"'.$this->getData('article').'" l&ouml;schen (ohne Rückfrage)</a>'.
                 '</div>'.
               '</div>';
    }
    else
    {
      $cont  = $this->internalNote();
    }

    return $cont;
  }
}

?>
