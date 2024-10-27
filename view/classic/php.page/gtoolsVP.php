<?php
namespace view\classic\page;
class gtoolsVP extends kupcClassicVP
{
  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct($ep, $hook, $linker)
  {
    parent::__construct($ep, $hook, $linker);
    $this->active = 'gtools';
  }

  /**
   * GTools anzeigen
   * _______________________________________________________________
   */
  protected function mainContent()
  {
    $cont = '';

    if (!$this->data['internal'])
    {
      $cont .= '<div class="prefBox">'.
                '<div class="caption">Eigenschaften</div>'.
                  '<div class="prefLine">'.
                    '<a class="cButton b_revert" href="'.$this->linker->href($this->ep, array('hook'=>'gtools', 'op'=>'resetPrefsForAllArticles')).'">Einstellungen f&uuml;r alle Artikel in dieser Box zur&uuml;cksetzen</a>'.
                  '</div>'.
              '</div>';

      $cont .= '<div class="prefBox">'.
                '<div class="caption">Thumbnails</div>'.
                  '<div class="prefLine">'.
                    '<a class="cButton" href="'.$this->linker->href($this->ep, array('hook'=>'gtools', 'op'=>'thumbsForAllArticles')).'">Thumbnail(s) für alle Artikel neu anlegen</a>'.
                  '</div>'.
              '</div>';

      $cont .= '<div class="prefBox">'.
                '<div class="caption">Verwaltung</div>'.
                  '<div class="prefLine">'.
                    '<a class="cButton b_refresh" href="'.$this->linker->href($this->ep, array('hook'=>'gtools', 'op'=>'buildAll')).'">Alle Artikel in der Box neu erstellen</a>'.
                  '</div>'.
              '</div>';

    }
    else
    {
      $cont .= $this->internalNote();
    }

    return $cont;
  }

}


?>
