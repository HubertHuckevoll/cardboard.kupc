<?php
namespace view\common\page;
class kupcBaseVP extends \cb\view\page\cbPageVP
{
  /**
   * assemble regular KuPC page
   * _______________________________________________________________
   */
  public function drawPage($errStr = '')
  {
    $erg = '<!DOCTYPE html>
            <html lang="de">
            <head>
              <title>'.$this->data['article'].'</title>
              <meta charset="UTF-8">
              <meta http-equiv="Cache-Control" content="no-store, no-cache, max-age=0, must-revalidate">
              <meta http-equiv="pragma" content="no-cache">
              <meta name="robots" content="noindex,nofollow">
              <meta name="description" content="Klecks und Pinsel-Club 2">
              <link rel="shortcut icon" href="./favicon.ico">
              <link rel="stylesheet" type="text/css" href="'.CB_CSS_ROOT.'cardboardTags.css">
              <link rel="stylesheet" type="text/css" href="'.CB_CSS_ROOT.'cbUserClasses.css">
              <link rel="stylesheet" type="text/css" href="'.CB_CSS_ROOT.'cardboardCSS.css">
              <link rel="stylesheet" type="text/css" href="'.CB_KUPC_ROOT.'view/common/css/desktop.css">
              <link rel="stylesheet" type="text/css" href="'.$this->data['userCSSf'].'">
              <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
              <script src="'.CB_KUPC_ROOT.'view/common/js/shortcut.js"></script>
              <script src="'.CB_KUPC_ROOT.'view/common/js/kupc.js"></script>
            </head>
            <body>
            <div id="status">'.($this->data['status'] ?? '').'</div>
            <div id="ep" style="display: none;">'.$this->ep.'</div>';

    // wrapper for full height
    $erg .= '<main>';

    // Open Toolbars
    $erg .= '<div id="mainToolbar">';
      // Button toolbar
      $erg .= '<div id="mainToolbarButtons">';
        $erg .= '<div id="mainToolbarLeftButtons">'.$this->mainToolbarLeftButtons().'</div>';
        $erg .= '<div id="mainToolbarRightButtons">'.$this->mainToolbarRightButtons().'</div>';
      $erg .= '</div>';

      // Tab toolbar
      $erg .= '<div id="mainToolbarTabs">';
      if ($this->getData('article') != '')
      {
        $erg .= '<div id="mainToolbarLeftTabs">'.$this->mainToolbarLeftTabs().'</div>';
      }
      $erg .= '<div id="mainToolbarRightTabs">'.$this->mainToolbarRightTabs().'</div>';
      $erg .= '</div>';
    $erg .= '</div>';

    // Content
    $erg .= '<div id="contentBox">';
    $erg .= $this->mainContent();
    $erg .= '</div>';

    // Footer
    $erg .= '<div id="footer">';
      $erg .= '<div class="footerLeft">';
      if ($this->data['internal'] == true)
      {
        $erg .= '<span>Änderungen in dieser Box sollten mit dem Administrator abgesprochen sein!</span>';
      }
      $erg .= '</div>';
      $erg .= '<div class="footerRight">';
      $erg .= '"Klecks und Pinsel-Club"&nbsp;&middot;&nbsp;car<span style="font-style: italic;">d</span><span style="font-style: italic;">b</span>oard '.$this->data['ver'].'&nbsp;&middot;&nbsp;Copyright &copy; 2009/4+ by MeyerK';
      $erg .= '</div>';
    $erg .= '</div>';

    // end of wrapper for full height
    $erg .= '</main>';

    // end of document
    $erg .= '</body></html>';

    echo $erg;
  }

  /**
   * Json ausgeben
   * _______________________________________________________________
   */
  public function drawJson()
  {
    $ret = array('status' => $this->getData('status'));
    echo json_encode($ret);
  }

  /**
   * Linke Toolbar Buttons
   * _______________________________________________________________
   */
  protected function mainToolbarLeftButtons()
  {
    // "Now playing" button
    $caption = ($this->getData('article') != '') ? $this->getData('article') : 'Textauswahl';
    $cont = '<a class="cButton" id="nowPlaying">'.ellipsis($caption, 37).'</a>';

    // Menu
    $cont .= '<div id="blockMenu" style="display: none;">';

      $cont .= '<div class="boxNameMoniker">'.$this->getData('articleBox').' ('.$this->getData('boxNameAlias').')</div>';

      // Artikel hinzufügen
      foreach($this->getData('articles') as $art)
      {
        $cont .= '<a class="navLink" href="'.$this->linker->href($this->ep, array('hook' => 'text', 'op' => 'load', 'article' => $art['articleName'])).'">'.$this->fDate($art['date']).' '.$art['articleName'].'</a>';
      }

    $cont .= '</div>';

    return $cont;
  }

  /**
   * Rechte Buttons
   * _______________________________________________________________
   */
  protected function mainToolbarRightButtons()
  {
    $cont = '<a class="cButton b_logout" href="'.$this->linker->href($this->ep, array('hook' => 'logout')).'">Logout</a>';

    return $cont;
  }

  /**
   * Linke Tabs
   * _______________________________________________________________
   */
  protected function mainToolbarLeftTabs()
  {
    $article = $this->data['article'];
    $cont = '';
    $tag = '';

    $sClass = ($this->active == 'edit') ? 'activeTab' : 'tab';
    $cont .= '<a class="'.$sClass.' b_edit" href="'.$this->linker->href($this->ep, array('hook'=>'text','op'=>'load', 'article'=>$article)).'">Text</'.$tag.'>';

    $sClass = (($this->active == 'filesUpload') or ($this->active == 'filesShow')) ? 'activeTab' : 'tab';
    $cont .= '<a class="'.$sClass.' b_upload" href="'.$this->linker->href($this->ep, array('hook'=>'files', 'op'=>'show', 'article'=>$article)).'">Dateien</'.$tag.'>';

    $sClass = ($this->active == 'comments') ? 'activeTab' : 'tab';
    $cont .= '<a class="'.$sClass.' b_view" href="'.$this->linker->href($this->ep, array('hook'=>'comments','op'=>'show', 'article'=>$article)).'">Kommentare</'.$tag.'>';

    $sClass = ($this->active == 'prefs') ? 'activeTab' : 'tab';
    $cont .= '<a class="'.$sClass.' b_hint" href="'.$this->linker->href($this->ep, array('hook'=>'prefs', 'op'=>'articlePrefs', 'article'=>$article)).'">Eigenschaften</'.$tag.'>';

    return $cont;
  }

  /**
   * Rechte Tabs
   * _______________________________________________________________
   */
  protected function mainToolbarRightTabs()
  {
    $cont = '';
    $sClass = 'tab';

    $sClass = ($this->active == 'gtools') ? 'activeTab' : 'tab';
    $cont .= '<a class="'.$sClass.' b_gtools" href="'.$this->linker->href($this->ep, array('hook'=>'gtools', 'article'=>$this->getData('article'))).'">Werkzeuge</a>';

    return $cont;
  }

  /**
   * internalNote
   * _______________________________________________________________
   */
  protected function internalNote()
  {
    $cont = '<div class="prefBox" style="border-color: red;">'.
              'Der Administrator hat alle Inhalte dieser Box als "intern" markiert. '.
              'Das Ändern von Einstellungen, Umbenennen und Löschen von Artikeln '.
              'ist daher nicht erlaubt.'.
            '</div>';

    return $cont;
  }

}

?>
