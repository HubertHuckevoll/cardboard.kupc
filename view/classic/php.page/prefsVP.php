<?php
namespace view\classic\page;
class prefsVP extends kupcClassicVP
{
  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct($ep = '', $hook, $linker = null)
  {
    parent::__construct($ep, $hook, $linker);
    $this->active = 'prefs';
  }

  /**
   * Prefs
   * _______________________________________________________________
   */
  protected function mainContent()
  {
    $prefs = $this->data['prefs'];

    if (!$this->data['internal'])
    {

      $isInvisible =      ($prefs['invisible']        === true) ? ' checked' : '';
      $commentsDisabled = ($prefs['commentsDisabled'] === true) ? ' checked' : '';

      $cont  = '';
      $cont .= '<div class="prefBox">';
      $cont .= '<div class="caption">Einstellungen f&uuml;r "<span id="prefFormArticle">'.$this->getData('article').'</span>"</div>'.
                '<div class="prefLine"><input class="pref" id="invisible" type="checkbox"'.$isInvisible.' /><label for="invisible">Artikel verstecken</label></div>'.
                '<div class="prefLine"><input class="pref" id="commentsDisabled" type="checkbox"'.$commentsDisabled.' /><label for="commentsDisabled">Für diesen Artikel keine Kommentare zulassen</label></div>'.
                '<div class="prefLine"><a class="cButton b_revert" href="'.$this->linker->href($this->ep, array('hook'=>'prefs', 'op'=>'reset', 'article'=>$this->getData('article'))).'">Einstellungen zur&uuml;cksetzen</a></div>';
      $cont .= '</div>';
    }
    else
    {
      $cont  = $this->internalNote();
    }

    return $cont;
  }
}

?>
