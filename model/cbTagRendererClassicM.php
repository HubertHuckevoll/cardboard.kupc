<?php

/**
 * The renderer for classic articles
 * _______________________________________________________________
 */

class cbTagRendererClassicM extends cbTagRendererBaseM
{
  public $articlePath = ''; // set this by hand once it's available - usually not at constructing time. must have trailing "/".

  protected $cbRoot = '';
  protected $cbImgRoot = '';
  protected $cbDataRoot = '';

  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct()
  {
    parent::__construct();

    $this->cbRoot       = CB_ROOT;
    $this->cbImgRoot    = CB_IMG_ROOT;
    $this->cbDataRoot   = CB_DATA_ROOT;
  }

  /**
   * get File URL - if no URL is provided create an
   * URL for the local ressource
   * ___________________________________________________________
   */
  protected function getFileURL($file)
  {
    if (   (!strstr($file, 'http://'))
        && (!strstr($file, 'https://'))
       )
    {
      $erg = rtrim($this->articlePath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$file;
      return $erg;
    }
    else
    {
      return $file;
    }
  }

  /**
   * Tags and their descriptions
   * ___________________________________________________________
   */
  protected function tag_img($data, $p, $cAttrs)
  {
    $mediaFile = $this->getFileURL($data);

		if (strstr($mediaFile, 'http://') || strstr($mediaFile, 'https://'))
    {
     	$this->data['inlineImages'][] = $mediaFile;
    }

    $erg = '<img alt="'.basename($mediaFile).'" src="'.$mediaFile.'"'.$cAttrs.' />';
    return $erg;
  }

  protected function meta_img()
  {
    return array('desc' => '(Lokales) Bild einbinden.',
                 'code' => '[img]http://www.geos-infobase.de/BB1023/bilder/logo.gif[/img]');
  }

  protected function tag_link($data, $p, $cAttrs)
  {
    $to = isset($p['to']) ? $p['to'] : '';
    $type = isset($p['type']) ? $p['type'] : '';

    switch($type)
    {
      case 'local':
        $to = $this->getFileURL($to);
      break;
    }

    $target = '';
    if (strstr($to, 'http://') || strstr($to, 'https://'))
    {
      $target = ' target="_blank"';
    }

    $erg = '<a href="'.$to.'"'.$target.$cAttrs.'>'.$data.'</a>';

    return $erg;
  }

  protected function meta_link()
  {
    return array('desc' => 'Link. Optional mit Verweis auf lokale Datei, dazu type="local" angeben',
                 'code' => '[link to="http://www.geos-infobase.de"]Link zur Infobase[/link]');
  }

  protected function tag_mp4($data, $p, $cAttrs)
  {
    $w = (isset($p['w'])) ? $p['w'] : 480;
    $h = (isset($p['h'])) ? $p['h'] : 360;
    $mediaFile = $this->getFileURL($data);

    $erg .= '<video width="'.$w.'" height="'.$h.'" controls'.$cAttr.'>'.
               '<source src="'.$mediaFile.'" type="video/mp4">'.
               'Video-Tag wird nicht unterst&uuml;tzt.'.
            '</video>';

    return $erg;
  }

  protected function meta_mp4()
  {
    return array('desc' => '(Lokales) MP4 - Video einbinden.',
                 'code' => '[mp4]cute_cat.mp4[/mp4]');
  }

  protected function tag_mp3($data, $p, $cAttrs)
  {
    $mediaFile = $this->getFileURL($data);

    $erg = '<audio controls'.$cAttrs.'>'.
              '<source src="'.$mediaFile.'" type="audio/mpeg">'.
              'Audio-Tag wird nicht unterst&uuml;tzt'.
           '</audio>';

    return $erg;
  }

  protected function meta_mp3()
  {
    return array('desc' => '(Lokale) MP3 - Datei einbinden.',
                 'code' => '[mp3]http://freesongs.de/mysong.mp3[/mp3]');
  }

  protected function tag_codefile($data, $p, $cAttrs)
  {
    $file = getPathFS($this->articlePath.'/'.$data);
    $lang = pathinfo($file, PATHINFO_EXTENSION);
    $text = trim(file_get_contents($file));

    if (stristr($lang, 'php'))
    {
      $text = str_replace("\r\n", "\n", $text);
      $text = highlight_string($text, true);
      $erg = '<div'.$cAttrs.'>'.$text.'</div>';
    }
    else
    {
      $text = htmlentities($text, ENT_QUOTES);
      $text = str_replace("\r\n", "\n", $text);
      $text = str_replace("\n", '<br />', $text);
      $erg = '<pre'.$cAttrs.'>'.$text.'</pre>';
    }

    return $erg;
  }

  protected function meta_codefile()
  {
    return array('desc' => 'Bindet eine Code - Datei ein. Für PHP steht Syntaxhervorhebung zur Verfügung.',
                 'code' => '[codefile]vlc.cmd[/codefile]');
  }

}

?>