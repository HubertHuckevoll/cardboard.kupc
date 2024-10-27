<?php
namespace view\classic\page;
class filesShowVP extends kupcClassicVP
{
  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct($ep, $hook, $linker)
  {
    parent::__construct($ep, $hook, $linker);
    $this->active = 'filesShow';
  }

  /**
   * Json zurückgeben - special variant for filesShowV
   * _______________________________________________________________
   */
  public function drawJson()
  {
    $ret = array('status' => $this->getData('status'));
    if ($this->getData('saneName') != '')
    {
      $ret['saneName'] = $this->getData('saneName');
    }

    echo json_encode($ret);
  }

  /**
   * show attached / uploaded files
   * _______________________________________________________________
   */
  protected function mainContent()
  {
    $files = $this->data['files'];

    $imageTable = '<table class="filesTable" cellspacing="0" cellpadding="0"><caption>Bilder</caption><tr>';
    $downlTable = '<table class="filesTable" cellspacing="0" cellpadding="0"><caption>Downloads</caption><tr>';
    $mediaTable = '<table class="filesTable" cellspacing="0" cellpadding="0"><caption>Media</caption><tr>';
    $otherTable = '<table class="filesTable" cellspacing="0" cellpadding="0"><caption>Andere</caption><tr>';
    $images = (array) $files['images'];
    $downls = (array) $files['downloadFiles'];
    $medias = (array) $files['mediaFiles'];
    $others = (array) $files['otherFiles'];
    $columns = 4;
    $imgC = 0;
    $downlC = 0;
    $mediaC = 0;
    $otherC = 0;

    $cont  = '<div class="toolbar">';
    $cont .= '<a class="cButton b_upload" href="'.$this->linker->href($this->ep, array('hook'=>'files', 'op'=>'uploadStart', 'article'=>$this->getData('article'))).'">Datei(en) zu "<span id="prefFormArticle">'.$this->getData('article').'</span>" hinzuf&uuml;gen</a>&nbsp;';
    $cont .= '<a class="cButton" href="'.$this->linker->href($this->ep, array('hook'=>'files', 'op'=>'thumbsForArticle', 'article'=>$this->getData('article'))).'">Thumbnails neu anlegen</a>';
    $cont .= '</div>';

    if (count($images) > 0)
    {
      foreach ($images as $image)
      {
        $file = basename($image['file']);
        $thumb = ($image['thumb'] != '') ? $image['thumb'] : CB_IMG_ROOT.'placeholder.png';
        $desc = $image['fileInfo'];

        if ($imgC == $columns)
        {
          $imageTable .= '</tr><tr>';
          $imgC = 0;
        }
        $imageTable .= '<td>'.
                         '<input class="fnameEditor" type="text" value="'.$file.'" data-file="'.$file.'" data-article="'.$this->getData('article').'" />'.
                         '<a class="rmvButton" href="'.$this->linker->href($this->ep, array('hook'=>'files', 'op'=>'deleteEnd', 'file'=>$file, 'article'=>$this->getData('article'))).'"></a>'.
                         '<img class="thumb" src="'.$thumb.'?x='.microtime().'" />'.
                         '<textarea class="descEditor" cols="20" rows="10" data-file="'.$file.'" data-article="'.$this->getData('article').'">'.$desc.'</textarea>'.
                       '</td>';
        $imgC++;
      }
    }

    if (count($downls) > 0)
    {
      foreach ($downls as $downl)
      {
        $downl = $downl['fname'];
        if ($downlC == $columns)
        {
          $downlTable .= '</tr><tr>';
          $downlC = 0;
        }
        $downlTable .= '<td>'.
                        '<a class="rmvButton" href="'.$this->linker->href($this->ep, array('hook'=>'files', 'op'=>'deleteEnd', 'file'=>$downl, 'article'=>$this->getData('article'))).'"></a>'.
                        '<br />'.'<br />'.
                        '<input class="fnameEditor" type="text" value="'.$downl.'" data-file="'.$downl.'" data-article="'.$this->getData('article').'" />'.
                       '</td>';
        $downlC++;
      }
    }

    if (count($medias) > 0)
    {
      foreach ($medias as $media)
      {
        $media = $media['fname'];
        if ($mediaC == $columns)
        {
          $mediaTable .= '</tr><tr>';
          $mediaC = 0;
        }
        $mediaTable .= '<td>'.
                        '<a class="rmvButton" href="'.$this->linker->href($this->ep, array('hook'=>'files', 'op'=>'deleteEnd', 'file'=>$media, 'article'=>$this->getData('article'))).'"></a>'.
                        '<br />'.'<br />'.
                        '<input class="fnameEditor" type="text" value="'.$media.'" data-file="'.$media.'" data-article="'.$this->getData('article').'" />'.
                       '</td>';
        $mediaC++;
      }
    }

    if (count($others) > 0)
    {
      foreach ($others as $other)
      {
        $other = $other['fname'];
        if ($otherC == $columns)
        {
          $otherTable .= '</tr><tr>';
          $otherC = 0;
        }
        $otherTable .= '<td>'.
                        '<a class="rmvButton" href="'.$this->linker->href($this->ep, array('hook'=>'files', 'op'=>'deleteEnd', 'file'=>$other, 'article'=>$this->getData('article'))).'"></a>'.
                        '<br />'.'<br />'.
                        '<input class="fnameEditor" type="text" value="'.$other.'" data-file="'.$other.'" data-article="'.$this->getData('article').'" />'.
                       '</td>';
        $otherC++;
      }
    }

    $imageTable .= '</tr></table>';
    $downlTable .= '</tr></table>';
    $mediaTable .= '</tr></table>';
    $otherTable .= '</tr></table>';

    $cont .= $imageTable.$downlTable.$mediaTable.$otherTable;

    return $cont;
  }

}


?>
