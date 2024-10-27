<?php

/**
 * The basic tag renderer, overwrite to add tags.
 * ______________________________________________
 */

class cbTagRendererM
{
  public $hints = array();

  public $smileyTable = array(
    ':)'   => 'smile.png',
    ':D'   => 'smile-big.png',
    ':('   => 'sad.png',
    ':`('  => 'crying.png',
    ':p'   => 'tongue.png',
    ':o'   => 'shock.png',
    ':@'   => 'angry.png',
    ':s'   => 'confused.png',
    ';)'   => 'wink.png',
    ':$'   => 'embarrassed.png',
    ':|'   => 'disappointed.png',
    '+o('  => 'sick.png',
    ':#'   => 'shut-mouth.png',
    '|)'   => 'sleepy.png',
    '8)'   => 'eyeroll.png',
    ':/'   => 'thinking.png',
    ':--)' => 'lying.png',
    '8|'   => 'nerdy.png',
    '8o|'  => 'teeth.png'
  );

  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct()
  {
  }

  /**
   * Chunk rendern
   * _______________________________________________________________
   */
  public function render($text)
  {
    $this->hints = array();
    $tags = $this->getTags();

    $text = $this->bbRenderDoubleTags($tags, $text);
    $text = $this->bbRenderSingleTags($tags, $text);
    $text = $this->bbRenderReplaceSmileys($text);
    $text = str_replace("\n", " ", $text);

    return $text;
  }

  /**
   * Render double tags like [green]Das ist grüner Text[/green].
   *
   * Alternate preg string:
   * $text = preg_replace_callback('/\[('.$tag.')(?:\]| ([^\]]*)\])(.*)\[\/\1\]/Us', array(&$this, "bbRenderReplaceTags"), $chunk, -1, $anz);
   *
   * Working:
   * $text = preg_replace_callback('/\[('.$tag.')(?:\]|([^\]]*)\])(.*)\[\/\1\]/Us', array(&$this, "bbRenderReplaceTags"), $text, -1, $anz);
   * ________________________________________________________________
   */
  protected function bbRenderDoubleTags($tags, $text)
  {
    $anz = 0;
    foreach ($tags as $tag)
    {
      do
      {
        $anz = 0;
        $text = preg_replace_callback('/\[('.$tag.')(?:\]|([^\]]*)\])(.*)\[\/\1\]/Us', array(&$this, "bbRenderReplaceTags"), $text, -1, $anz);
      }
      while ($anz > 0);
    }

    return $text;
  }

  /**
   * Render single tags like [n]
   * ________________________________________________________________
   */
  protected function bbRenderSingleTags($tags, $text)
  {
    $anz = 0;

    foreach ($tags as $tag)
    {
      do
      {
        $anz = 0;
        $text = preg_replace_callback('/\[('.$tag.')(?:\]| ([^\]]*)\])/', array(&$this, "bbRenderReplaceTags"), $text, -1, $anz);
      }
      while ($anz > 0);
    }

    return $text;
  }


  /* strip bb Tags
    _________________________________________________________________
  */
  public function bbRenderStrip($text)
  {
    $text = $this->cleanString($text);

    // Remove page tags completely
    $text = preg_replace('/\[page\].*\[\/page\]/Us', '', $text);

    // Get tags
    // We need to use the defined tags for stripping
    // so the user can continue to use [] without the
    // fear of having them get stripped by bbRender
    $tags = $this->getTags();

    // Replace
    foreach ($tags as $tag)
    {
      $text = preg_replace('/\[\/?'.$tag.'[^\]]*\]/', '', $text);
    }

    return $text;
  }

  /**
   * provide the tags
   * ___________________________________________________________
   */
  public function getTags()
  {
    $tags = array();
    $funcs = get_class_methods($this);

    foreach($funcs as $func)
    {
      if (strpos($func, 'tag_') === 0)
      {
        $tags[] = substr($func, 4);
      }
    }

    return $tags;
  }

  /**
   * provide the tag descriptions
   * ___________________________________________________________
   */
  public function getTagDescs()
  {
    $tags['page'] = array('desc' => 'Neue Seite erzwingen. Der Inhalt des Tags wird als Zwischenüberschrift definiert und ist später als Link erreichbar. '.
                                    'Achtung: "page" erzeugt keinen sichtbaren Inhalt!',
                          'code' => '[page]5. Tag auf der Insel[/page]');
    $funcs = get_class_methods($this);

    foreach($funcs as $func)
    {
      if (strpos($func, 'tag_') === 0)
      {
        $tag = substr($func, 4);
        $mFn = 'meta_'.$tag;
        $meta = array('desc' => 'Keine Beschreibung verfügbar.', 'code' => 'Kein Beispielcode verfügbar.');
        if (method_exists($this, $mFn))
        {
          $meta = $this->$mFn();
        }

        $tags[$tag] = $meta;
      }
    }

    return $tags;
  }


  /**
   * Exec Tag
   * ___________________________________________________________
   */
  protected function execTag($tag, $data, $p, $cAttrs)
  {
    $func = 'tag_'.$tag;

    if (method_exists($this, $func))
    {
      $erg = $this->$func($data, $p, $cAttrs);
    }
    else
    {
      $erg = '<div'.$cAttrs.'">'.$data.'</div>';
    }

    return $erg;
  }

  /**
   * Tags ersetzen
   * _______________________________________________________________
   */
  protected function bbRenderReplaceTags($treffer)
  {
    // Find tag, raw parameters and data
    $original = trim($treffer[0]);

    $tag = $treffer[1];
    $rawParams = isset($treffer[2]) ? $treffer[2] : '';
    $data = isset($treffer[3]) ? $treffer[3] : '';

    $tag = strtolower($this->cleanString($tag));
    $rawParams = $this->cleanString($rawParams);
    //$data = $this->cleanString($data); //FIXME!!!!!

    $p['c'][] = 'tag_'.$tag;

    // Parse paramters
    if ($rawParams != '')
    {
      // find css classes attached to the tag: t.b
      if (preg_match('/^\.([\.\w]*)(.*)/', $rawParams, $params))
      {
        $p['c'] = array_merge($p['c'], explode('.', $params[1]));
        $rawParams = trim($params[2]);
      }

      // find full fletched parameters of the form: to="http://www.geos-infobase.de"
      // and classes
      if (preg_match_all('/(.*)="(.*)"/U', $rawParams, $params, PREG_SET_ORDER))
      {
        foreach ($params as $param)
        {
          if ($param[1] == 'c')
          {
            $p['c'][] = $param[2];
          }
          else
          {
            $p[trim($param[1])] = trim($param[2]);
          }
        }
      }
    }

    // Set up common attributes

    // Set up CSS classes. By default, there is a class of the name "tag_tagname", other classes are appended.
    $class = ' class="';
    foreach ($p['c'] as $cl)
    {
      $class .= $cl.' ';
    }
    $class = rtrim($class);
    $class .= '"';

    // ID - discouraged, use classes
    $id = (isset($p['i'])) ? ' id="'.$p['i'].'"' : '';

    // Inline styles - discouraged, use classes
    $styles = (isset($p['s'])) ? ' style="'.$p['s'].'"' : '';

    // Convenience - prepare common attributes as string
    $cAttrs = $id.$class.$styles;

    // Call tag function
    $erg = $this->execTag($tag, $data, $p, $cAttrs);

    return $erg;
  }

  /**
   * Smileys ersetzen
   * _______________________________________________________________
   */
  protected function bbRenderReplaceSmileys($data)
  {
    $dir = CB_IMG_ROOT.'smileys/';

    foreach($this->smileyTable as $text => $file)
    {
      $data = str_replace(' '.$text, '<img class="smiley" src="'.$dir.$file.'" />', $data);
    }

    return $data;
  }

  /**
   * The only built-in tag in this "abstract" class
   * Stores metadata
   * ___________________________________________________________
   */
  protected function tag_hint($data, $p, $cAttrs)
  {
    $type = isset($p['is']) ? $p['is'] : 'empty';
    $this->hints[$type] = $data;

    return '';
  }

  protected function meta_hint()
  {
    return array('desc' => 'Meta Tags',
                 'code' => '[hint is="datum"]28.04.1977[/hint] oder [hint is="author"]B. Schulte[/hint]');
  }

  /**
   * return hints
   * _______________________________________________________________
   */
  public function getHints()
  {
    return $this->hints;
  }

  /**
   * Clean Strings: trim, remove tags, replace typographic chars
   * _______________________________________________________________
   */
  protected function cleanString($str)
  {
    $str = trim($str);
    return $str;
  }

}

?>