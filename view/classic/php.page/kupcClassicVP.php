<?php
namespace view\classic\page;
class kupcClassicVP extends \view\common\page\kupcBaseVP
{

  /**
   * Linke Toolbar Buttons
   * _______________________________________________________________
   */
  protected function mainToolbarLeftButtons()
  {
    $cont = '';
    $cont .= parent::mainToolbarLeftButtons();
    $cont .= '<a class="cButton b_new" href="'.$this->linker->href($this->ep, array('hook' => 'manageClassic', 'op' => 'newStart')).'">Neu</a>&nbsp;';

    return $cont;
  }

  /**
   * Linke Tabs
   * _______________________________________________________________
   */
  protected function mainToolbarLeftTabs()
  {
    $cont = '';
    $article = $this->data['article'];
    $sClass = ($this->active == 'tools') ? 'activeTab' : 'tab';

    $cont .= parent::mainToolbarLeftTabs();
    $cont .= '<a class="'.$sClass.' b_tools" href="'.$this->linker->href($this->ep, array('hook'=>'manageClassic', 'op'=>'tools', 'article'=>$article)).'">Verwaltung</a>';

    return $cont;
  }
}

?>
