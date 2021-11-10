<?php

class cbArticleBuilderGDocsM
{
  public $pageSeparator = 'h1';
  public $abstractSize = 300;
  
  // $data Array
  /*
  'type' => 'gdocs',
  'version' => '3',
  'articleBox' => '',
  'articleName' => '',
  'gurl' => '',
  'gid' => '',
  'styles' => '',
  'articleText' => '',
  'aAbstract' => '',
  'paginatedText' => '',
  'pagesInfo' => array(), // Headlines of pages
  'pagesAbstracts' => array(), // Abstracts of pages
  'date' => '',
  'headline' => '',
  'inlineImages' => array()
  */
  
  public $articleBox = null;
  public $articleName = null;
  
  public $fm = null; // files crud model
  public $tM = null;
  
  public $data = array();
  
  // Internal
  public $dom = null;
  public $contents = null;
  public $gurl = null; // Google URL
  public $articleF = null;
  
  // gbbr
  public $gbbr = null;

  /**
   * Constructor
   * ___________________________________________________________________
   */
  function __construct($articleBox, $docInfo, $gbbr = null)
  {
    if ($articleBox == '')
    {
      throw new Exception(__CLASS__.': Der angeforderte Box-Name war leer.');
    }

    if ($docInfo['gurl'] == '')
    {
      throw new Exception(__CLASS__.': Die URL des angeforderten GDocs Dokuments war leer.');
    }
    
    // Box
    $this->articleBox = $articleBox;
    $this->articleName = $docInfo['articleName'];
    $this->articleF = getPathFS(CB_DATA_ROOT.$this->articleBox.CB_BUILT.$this->articleName.'.json');

    $this->data['articleBox'] = $this->articleBox;
    $this->data['articleName'] = $this->articleName;

    // Date
    $this->data['date'] = $docInfo['date'];

    // Google URL
    $this->gurl = $docInfo['gurl'];
    $this->data['gurl'] = $this->gurl;

    // Google ID - not needed by cbCommentsM anymore, still might be useful
    $re = '/.*\/(.*)\/pub/';
    preg_match($re, $this->gurl, $matches);
    $this->gid = $matches[1];
    $this->data['gid'] = $this->gid;
    
    // Tag renderer
    $this->gbbr = ($gbbr == null) ? new cbTagRendererGDocsM() : $gbbr;
    
    // make sure some properties have the right type and set the document type to gdocs
    $this->data['type'] = 'gdocs';
    $this->data['version'] = '3';
    $this->data['pagesInfo'] = array();
    $this->data['pagesAbstracts'] = array();
    $this->data['inlineImages'] = array();
    
    $this->fm = new cbFilesCrudM($this->articleBox);
    $this->tM = new cbTextGDocsM($this->articleBox, $this->articleName);
    $this->tM->load();
  }

  /**
   * Load - must be called manually!
   * throws exception so we can call construct and load in one go
   * returns data array
   * ___________________________________________________________________
   */
  public function load()
  {
    try
    {
      $html = $this->tM->getArticleText();
      if ($html != false)
      {
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        libxml_use_internal_errors(true);
        $this->dom = new DomDocument();
        $this->dom->loadHTML($html);
        $this->dom->preserveWhiteSpace = false;
        $this->contents = $this->dom->getElementById('contents');
  
        $this->fetchTitle();
        $this->fetchStyles();
        $this->fetchImagesAndFiles();
        $this->fetchInlineImages();
        $this->fetchTextAndPaginate();
        $this->renderArticle();
        $this->fetchYTLinks();
        $this->fetchAbstract();
        $this->createPagesAbstracts();
      
        $this->saveToFile();
        
        return $this->data;
      }
      else
      {
        throw new Exception(__CLASS__.': Angefordertes HTML-Dokument war leer.');
      }
    }
    catch(Exception $e)
    {
      throw $e;
    }
  }
  
  /**
   * Extract titles
   * ___________________________________________________________________
   */
  public function fetchTitle()
  {
    $title = $this->dom->getElementsByTagName('title');
    $this->data['headline'] = $title->item(0)->nodeValue;
  }
  
  /**
   * Extract Styles - at the moment they must be prepended on every page!
   * ___________________________________________________________________
   */
  public function fetchStyles()
  {
    $styles = $this->contents->getElementsByTagName('style');
    $styles = $styles->item(0)->nodeValue;
    
    // Let's prefix all the styles with a class of the article name
    // so the scope is restricted to the div with the article name set
    // as class
    $regex = '/.*\{.*\}/U';
    preg_match_all($regex, $styles, $matches, PREG_PATTERN_ORDER, 0);
    $matches = $matches[0];
    $styles = '';
    foreach ($matches as $match)
    {
      $styles .= '.'.$this->data['articleName'].' '.$match;
    }

    $this->data['styles'] = $styles;
  }
  
  
  /**
   * fetch Files and Images
   * ___________________________________________________________________
   */
  public function fetchImagesAndFiles()
  {
    $this->data['images'] = $this->fm->fetchImagesForArticle($this->articleName);
    list($this->data['downloadFiles'], $this->data['mediaFiles'], $this->data['otherFiles']) = $this->fm->fetchFilesForArticle($this->articleName);
  }
  
  /**
   * Inline Images
   * ___________________________________________________________________
   */
  public function fetchInlineImages()
  {
    $imgs = $this->contents->getElementsByTagName('img');
    
    foreach($imgs as $img)
    {
      $this->data['inlineImages'][] = $img->getAttribute('src');
    }
  }
  
  /**
   * Rework YouTube Links to YouTube embeddable Iframes
   * ___________________________________________________________________
   */
  public function fetchYTLinks()
  {
    $links = $this->contents->getElementsByTagName('a');
    $ytLinks = array();
    
    foreach($links as $link)
    {
      $href = $link->getAttribute('href');
      if (strpos($href, 'youtube.com') !== false)
      {
        $ytLinks[] = $link;
      }
    }
    
    if (sizeof($ytLinks) > 0)
    {
      foreach ($ytLinks as $ytLink)
      {
        $orgHref = $ytLink->getAttribute('href');
        
        $ytEmbed = $this->dom->createElement('iframe', '');
        $ytEmbedW = $this->dom->createAttribute('width');
        $ytEmbedW->value = '100%';
        $ytEmbedH = $this->dom->createAttribute('height');
        $ytEmbedH->value = 'auto';
        $ytEmbedF = $this->dom->createAttribute('frameborder');
        $ytEmbedF->value = 0;
        $ytEmbedA = $this->dom->createAttribute('allowfullscreen');
        $ytEmbedA->value = 'allowfullscreen';
        $ytEmbedSrc = $this->dom->createAttribute('src');
        
        $re = '/watch\?v%3D(.*)&/U';
        preg_match($re, $orgHref, $matches);
        
        $ytEmbedSrc->value = 'https://www.youtube.com/embed/'.$matches[1];
        
        $ytEmbed->appendChild($ytEmbedW);
        $ytEmbed->appendChild($ytEmbedH);
        $ytEmbed->appendChild($ytEmbedF);
        $ytEmbed->appendChild($ytEmbedA);
        $ytEmbed->appendChild($ytEmbedSrc);
        
        $ytLink->parentNode->replaceChild($ytEmbed, $ytLink);
      }
    }
  }
  
  /**
   * Paginate Text. Store raw text without tags.
   * ___________________________________________________________________
   */
  public function fetchTextAndPaginate()
  {
    $page = -1;
    
    foreach ($this->contents->childNodes as $childNode)
    {
      $nodeHtml = $this->node2html($childNode);
      $nodeText = trim(strip_tags($nodeHtml));
      
      // new page
      if ($childNode->tagName == $this->pageSeparator)
      {
        $page++;
        $this->data['pagesInfo'][$page] = $childNode->nodeValue;
      };
      
      // add text
      if (($childNode->tagName != $this->pageSeparator) &&
          ($childNode->tagName != 'style') &&
          ($childNode->tagName != 'script') &&
          ($page >= 0))
      {
        $this->data['paginatedText'][$page] = $this->data['paginatedText'][$page].$nodeHtml;
        $this->data['articleText'] .= $nodeText.' ';
      }
      
      // extract some metadata from page 1
      if ($page == -1)
      {
        $notNeeded = $this->gbbr->render($nodeText);
        $this->data = array_merge($this->data, $this->gbbr->getHints());
      }
    }
    
    logger::vh($this->data['paginatedText']);
  }
  
  /**
   * Render article
   * ATM this is implemented mainly for compatibility reasons to make
   * classic cardboard articles somewhat copy/pasteable into GDocs...
   * It still is nice tho because it offers the possibility to add
   * complex functionality to plain GDocs articles...
   * ___________________________________________________________________
   */
  public function renderArticle($page = null)
  {
    // Reset array
    $this->gbbr->data['inlineImages'] = array();
    
    // Render text
    if ($page === null)
    { // Render all pages
      for ($i=0; $i<count($this->data['paginatedText']); $i++)
      {
        $this->data['paginatedText'][$i] = $this->gbbr->render($this->data['paginatedText'][$i]);
      }
    }
    else
    { // Render specified page only
      $this->data['paginatedText'][$page] = $this->gbbr->render($this->data['paginatedText'][$page]);
    }
    
    // Combine existing inline images from the GDoc with those from our rendered [img] tags
    $this->data['inlineImages'] = array_merge($this->data['inlineImages'], $this->gbbr->data['inlineImages']);
  }
  
  /**
   * Create Article Abstract
   * ___________________________________________________________________
   */
  public function fetchAbstract()
  {
    $aAbstract = $this->data['articleText'];
    $aAbstract = $this->gbbr->bbRenderStrip($aAbstract);
    $aAbstract = (strlen($aAbstract) >= $this->abstractSize) ? mb_substr($aAbstract, 0, mb_strpos($aAbstract, ' ', $this->abstractSize)) : $aAbstract;
    $aAbstract = $aAbstract.'...';
    $this->data['aAbstract'] = $aAbstract;
  }
  
  /**
   * Seitenzusammenfassungen erstellen
   * __________________________________________________________________
   */
  public function createPagesAbstracts()
  {
    $textA = $this->data['paginatedText'];
    
    foreach ($textA as $str)
    {
      $str = $this->gbbr->bbRenderStrip($str);
  	  $str = strip_tags($str);
      $str = (strlen($str) >= $this->abstractSize) ? mb_substr($str, 0, mb_strpos($str, ' ', $this->abstractSize)) : $str;
      $str = $str.'...';
      $this->data['pagesAbstracts'][] = $str;
    }
  }
  
  /**
   * Save to disk
   * ___________________________________________________________________
   */
  public function saveToFile()
  {
    jsonM::save($this->articleF, $this->data);
  }
  
  /**
   * Convert DOMDocument node to html
   * ___________________________________________________________________
   */
  protected function node2html($node)
  {
    $erg = $this->dom->saveHTML($node);
    return $erg;
  }
}
  
?>
