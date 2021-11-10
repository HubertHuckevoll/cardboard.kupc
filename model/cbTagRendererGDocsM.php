<?php

/**
 * GDocs Tag Renderer - it's the base tag renderer
 * + ?
 * Stub class for future expansion
 * _________________________________________________
 */

class cbTagRendererGDocsM extends cbTagRendererBaseM
{
  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct()
  {
    parent::__construct();
  }
  
  /**
   * Render double tags like [green]Das ist grüner Text[/green].
   * greedy version, does not support nested tags
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
        $text = preg_replace_callback('/\[('.$tag.')(?:\]|([^\]]*)\])(.*)\[\/\1\]/Um', array(&$this, "bbRenderReplaceTags"), $text, -1, $anz);
      }
      while ($anz > 0);
    }
    
    return $text;
  }
  
  /**
   * Clean Strings: trim, remove tags, replace typographic chars
   * _______________________________________________________________
   */
  protected function cleanString($str)
  {
    $str = parent::cleanString($str);
    
    $chr_map = array(
       // Windows codepage 1252
       "\xC2\x82" => "'", // U+0082⇒U+201A single low-9 quotation mark
       "\xC2\x84" => '"', // U+0084⇒U+201E double low-9 quotation mark
       "\xC2\x8B" => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
       "\xC2\x91" => "'", // U+0091⇒U+2018 left single quotation mark
       "\xC2\x92" => "'", // U+0092⇒U+2019 right single quotation mark
       "\xC2\x93" => '"', // U+0093⇒U+201C left double quotation mark
       "\xC2\x94" => '"', // U+0094⇒U+201D right double quotation mark
       "\xC2\x9B" => "'", // U+009B⇒U+203A single right-pointing angle quotation mark
    
       // Regular Unicode     // U+0022 quotation mark (")
                              // U+0027 apostrophe     (')
       "\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
       "\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
       "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
       "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
       "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
       "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
       "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
       "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
       "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
       "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
       "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
       "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
    );
    
    // This kills any HTML that has already been rendered by previous bbTags that were inside of this tag...
    // which is fine for GDocs articles but not for Classic articles
    $str = strip_tags($str);

    $chr = array_keys($chr_map); // but: for efficiency you should
    $rpl = array_values($chr_map); // pre-calculate these two arrays
    $str = str_replace($chr, $rpl, html_entity_decode($str, ENT_QUOTES, "UTF-8"));
    
    return $str;
  }
  
}
  
?>