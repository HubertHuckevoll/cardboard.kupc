<?php
namespace view\classic\page;
class editVP extends kupcClassicVP
{
  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct($ep, $hook, $linker = null)
  {
    parent::__construct($ep, $hook, $linker);
    $this->active = 'edit';
  }

  /**
   * Artikel bearbeiten
   * _______________________________________________________________
   */
  protected function mainContent()
  {
    $text = $this->data['text'];
    $article = $this->data['article'];

    // Toolbars
    $cont  = '<div class="editToolbars">';
    $cont .= '<div class="toolbar">'.
              '<a class="cButton b_save" id="saveButton" data-article="'.$article.'">Speichern <span class="shortcut">Strg+S</span></a>&nbsp;'.
              '<a class="cButton b_revert" id="revertButton">Letzter Stand</a>&nbsp;'.
              '<a id="designHintButton" style="border-right: none;" href="#">Textgestaltung</a>'.
             '</div>';
    $cont .= '<div class="toolbar">'.$this->createTagButtons($this->data['tags']).'</div>';
    $cont .= '<div class="toolbar">'.$this->createStyleButtons($this->data['defaultCSS']).'</div>';
    $cont .= '<div class="toolbar">'.$this->createStyleButtons($this->data['userCSS']).'</div>';
    $cont .= '<div class="toolbar">'.$this->createSmileyButtons($this->data['smileyTable']).'</div>';
    $cont .= '</div>';

    // Wrapper
    $cont .= '<div id="textInfoBoxWrapper">';

      // Textarea
      $cont .= '<textarea name="data" id="data">'.$text.'</textarea>';

      // Infobox
      $cont .= '<div id="infoBox">';

        // Help
        $cont .= '<div id="help">';
        foreach ($this->data['defaultCSS'] as $class)
        {
          $cont .= '<div class="doc '.$class[2].'"><strong>'.$class[2].'</strong><p>'.$class[1].'</p></div>';
        }

        foreach ($this->data['userCSS'] as $class)
        {
          $cont .= '<div class="doc '.$class[2].'"><strong>'.$class[2].'</strong><p>'.$class[1].'</p></div>';
        }

        foreach ($this->data['tags'] as $tagName => $tagMeta)
        {
          $cont .= '<div class="doc '.$tagName.'"><strong>'.$tagName.'</strong><p>'.$tagMeta['desc'].'</p>';
          $cont .= '<code>'.$tagMeta['code'].'</code>';
          $cont .= '</div>';
        }

        $cont .= '<div class="doc css" style="display: block;">'.
                 '<h3>Textgestaltung</h3>'.
                 'Mit Hilfe von Tags wie "t" und "link" kann der Text gestaltet und funktional erweitert werden. '.
                 'CSS-Klassen können an die Tags angehangen werden. Wenn Sie eigene CSS - Klassen in '.
                 'Ihrem Projekt definieren, können Sie diese selbstverständlich ebenfalls verwenden, diese Klassen '.
                 'erscheinen in der zweiten Zeile mit CSS - Klassen.'.
                 '<br /><br />'.
                 'Um also beispielsweise fetten, kursiven, roten und zentrierten Text zu erzeugen, '.
                 'verwenden Sie folgenden Befehl:'.
                 '<p style="font-family: monospace;">'.
                    '[t.i.b.red.center]Was für ein gewaltiges Beispiel![/t]'.
                    '</p>'.
                 '</p>'.
                 '<hr />'.
                 '<p>'.
                  '<div class="i b red center">Was für ein gewaltiges Beispiel!</div>'.
                 '</p>'.
                 '</div>';
        $cont .= '</div>'; // end help

        //Preview
        $cont .= '<div id="preview"></div>';

      $cont .= '</div>'; // end infoBox

    $cont .= '</div>'; // end textInfoBoxWrapper

    return $cont;
  }

  /**
   * createTagButtons
   * _______________________________________________________________
   */
  protected function createTagButtons($tags = array())
  {
    $html = '';
    if (count($tags) > 0)
    {
      foreach ($tags as $tagName => $tagMeta)
      {
        $html .= '<a class="tbButton tagButton" href="#">'.$tagName.'</a>';
      }
    }
    return $html;
  }

  /**
   * create Style Buttons
   * _______________________________________________________________
   */
  protected function createStyleButtons($cssc = array())
  {
    $html  = '';
    if (count($cssc) > 0)
    {
      foreach ($cssc as $c)
      {
        $html .= '<a class="tbButton styleButton" href="#">'.$c[2].'</a> ';
      }
    }
    return $html;
  }

  /**
   * create Smiley Buttons
   * _______________________________________________________________
   */
  protected function createSmileyButtons($smileys = array())
  {
    $html = '';
    if (count($smileys) > 0)
    {
      $dir = CB_IMG_ROOT.'smileys/';
      foreach ($smileys as $smText => $smFile)
      {
        $html .= '<a class="tbButton emoticonButton" title="'.$smText.'" href="#">'.
                    '<img src="'.$dir.$smFile.'" />'.
                 '</a>';
      }
    }
    return $html;
  }

}


?>
