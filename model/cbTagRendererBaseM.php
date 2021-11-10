<?php

/**
 * Basic Tags that do not require local dependencies
 * _________________________________________________
 */

class cbTagRendererBaseM extends cbTagRendererM
{
  public $data = array(); // array for inline images, mainly.

  /**
   * Tags and their descriptions
   * ___________________________________________________________
   */
  protected function tag_img($data, $p, $cAttrs)
  {
    $this->data['inlineImages'][] = $data;
    $erg = '<img alt="'.$data.'" src="'.$data.'"'.$cAttrs.' />';
    return $erg;
  }

  protected function meta_img()
  {
    return array('desc' => 'Bild einbinden.',
                 'code' => '[img]http://www.geos-infobase.de/BB1023/bilder/logo.gif[/img]');
  }

  protected function tag_link($data, $p, $cAttrs)
  {
    $to = $p['to'];
    $target = ' target="_blank"';
    $erg = '<a href="'.$to.'"'.$target.$cAttrs.'>'.$data.'</a>';
    return $erg;
  }

  protected function meta_link()
  {
    return array('desc' => 'Link.',
                 'code' => '[link to="http://www.geos-infobase.de"]Link zur Infobase[/link]');
  }

  protected function tag_email($data, $p, $cAttrs)
  {
    $erg = '<a href="mailto:'.encodeEmail($p['to']).'"'.$cAttrs.'>'.$data.'</a>';
    return $erg;
  }

  protected function meta_email()
  {
    return array('desc' => 'E-Mail Adresse verlinken.',
                 'code' => '[email to="dummy@dummynet.de"]Dummy anmailen[/email]');
  }

  protected function tag_yt($data, $p, $cAttrs)
  {
    $w = (isset($p['w'])) ? $p['w'] : 425;
    $h = (isset($p['h'])) ? $p['h'] : 344;

    $erg = '<iframe class="youtube-player" type="text/html" width="'.$w.'" height="'.$h.'" src="http://www.youtube.com/embed/'.$data.'" frameborder="0"></iframe>';
    return $erg;
  }

  protected function meta_yt()
  {
    return array('desc' => 'YouTube - Video einbinden.',
                 'code' => '[yt]5ruNijRWf-U[/yt]');
  }

  protected function tag_table($data, $p, $cAttrs)
  {
    $rows = explode('°', $data);

    $erg = '<table'.$cAttrs.'>';
    $i = 0;
    foreach ($rows as $row)
    {
      if ($i == 0)
      {
        $o_tag = '<th>';
        $c_tag = '</th>';
      }
      else
      {
        $o_tag = '<td>';
        $c_tag = '</td>';
      }
      $erg .= '<tr>';
      $cells = explode('|', $row);
      foreach ($cells as $cell)
      {
        $erg .= $o_tag.$cell.$c_tag;
      }
      $erg .= '</tr>';
      $i++;
    }
    $erg .= '</table>';
    return $erg;
  }

  protected function meta_table()
  {
    return array('desc' => 'Tabelle erzeugen.',
                 'code' => '[table]rot|blau|gelb|violett[/table]');
  }

  protected function tag_map($data, $p, $cAttrs)
  {
    $erg = '';
    $url = '';
    $zoom = (isset($p['zoom'])) ? $p['zoom'] : 10;
    $place = urlencode($data);

    $url = 'https://nominatim.openstreetmap.org/search?format=json&polygon=0&q='.$place;
    $loc = request($url);
    $loc = json_decode($loc, true);

    $erg = '<div'.$cAttrs.'>'.
              '<img src="https://static-maps.yandex.ru/1.x/'.
              '?lang=de_DE&ll='.$loc[0]['lon'].','.$loc[0]['lat'].
              '&size=350,350'.
              '&z='.$zoom.
              '&l=map'.
              '&pt='.$loc[0]['lon'].','.$loc[0]['lat'].',vkgrm">'.
           '</div>';

    return $erg;
  }

  protected function meta_map()
  {
    return array('desc' => 'Statische Map. Parameter: "zoom" (Standard = 10). ',
                 'code' => '[map zoom="10"]Jungfernstieg, Erfurt[/map]');
  }

  protected function tag_script($data, $p, $cAttrs)
  {
    $func = $p['func'];
    foreach($p as $sParamKey => $sParamVal)
    {
      if (preg_match('/param([0-9]*)/', $sParamKey, $paramNum))
      {
        if (!is_numeric($sParamVal))
        {
          $sParamVal = '"'.$sParamVal.'"';
        }
        $sParams[$paramNum[1]] = $sParamVal;
      }
    }

    if (count($sParams) > 0)
    {
      ksort($sParams, SORT_NUMERIC);
      $sParamsStr = implode(', ', $sParams);
    }
    else
    {
      $sParamsStr = '';
    }
    $erg = "<script type=\"text/javascript\">/*<![CDATA[*/\r\n\t".$func."(".$sParamsStr.");\r\n/*]]>*/</script>";

    return $erg;
  }

  protected function meta_script()
  {
    return array('desc' => 'Javascript-Funktion aufrufen - "Handle with care!"',
                 'code' => '[script func="window.setTimeout" param0="location.reload()" param1="600000"]');
  }

  protected function tag_n($data, $p, $cAttrs)
  {
    return '<br>';
  }

  protected function meta_n()
  {
    return array('desc' => 'Neue Zeile erzwingen.',
                 'code' => '[n]');
  }

  protected function tag_p($data, $p, $cAttrs)
  {
    return '<br><br>';
  }

  protected function meta_p()
  {
    return array('desc' => 'Neuen Absatz erzwingen.',
                 'code' => '[p]');
  }

  protected function tag_t($data, $p, $cAttrs)
  {
    $erg = '<div'.$cAttrs.'>'.$data.'</div>';
    return $erg;
  }

  protected function meta_t()
  {
    return array('desc' => 'Text mit CSS - Klassen formatieren.',
                 'code' => '[t.bauchbinde]Fotos: Copyright (C) MeyerK[/t]');
  }

  protected function tag_blob($data, $p, $cAttrs)
  {
    $erg = '<div'.$cAttrs.'>'.$data.'</div>';
    return $erg;
  }

  protected function meta_blob()
  {
    return array('desc' => 'Block stark strukturierten Textes / Inhaltes. Sollte z.B. benutzt werden, wenn die Seite mit JavaScript überarbeitet wird. Zum Formatieren von Text wird [t] empfohlen.',
                 'code' => '[blob.slide][img]rosaElephant.jpg[/img][t]Denken Sie nicht an einen rosa Elephant.[/t][/blob]');
  }

  protected function tag_h1($data, $p, $cAttrs)
  {
    return '<h1'.$cAttrs.'>'.$data.'</h1>';
  }

  protected function meta_h1()
  {
    return array('desc' => 'Überschrift 1. Ebene.',
                 'code' => '[h1]Überschrift 1. Ebene[/h1]');
  }

  protected function tag_h2($data, $p, $cAttrs)
  {
    return '<h2'.$cAttrs.'>'.$data.'</h2>';
  }

  protected function meta_h2()
  {
    return array('desc' => 'Überschrift 2. Ebene.',
                 'code' => '[h2]Überschrift 2. Ebene[/h2]');
  }

  protected function tag_li1($data, $p, $cAttrs)
  {
    return '<ul'.$cAttrs.'><li>'.$data.'</li></ul>';
  }

  protected function meta_li1()
  {
    return array('desc' => 'Aufzählung 1. Ebene.',
                 'code' => '[li1]Aufzählung 1. Ebene[/li1]');
  }

  protected function tag_li2($data, $p, $cAttrs)
  {
    return '<ul'.$cAttrs.'><li>'.$data.'</li></ul>';
  }

  protected function meta_li2()
  {
    return array('desc' => 'Aufzählung 2. Ebene.',
                 'code' => '[li2]Aufzählung 2. Ebene[/li2]');
  }

  protected function tag_obf($data, $p, $cAttrs)
  {
    return '<span'.$cAttrs.'>'.obfuscateStr($data).'</span>';
  }

  protected function meta_obf()
  {
    return array('desc' => 'Text erzeugen, den SPAM-Roboter möglichst nicht erfassen sollen... (Achtung, keine hohe Sicherheit).',
                 'code' => '[obf]Mein nicht so super geheimer Text[/obf]');
  }

}

?>