<?php

class cbArticleBuilderClassicM
{
  // Intern
  public $articleBox = '';
  public $articleName = '';

  public $abstractSize = 300;
  public $pageParagraphLimit = 10;
  public $paragraphSep = "\n\n";

  public $fm = null; // files crud model
  public $tM = null; // text crud model
  public $cbbr = null;

  public $data = array(
    'type' => 'classic',
    'version' => 1,
    'articleBox' => '',
    'articleName' => '',
    'articleText' => '',
    'paginatedText' => '',
    'pagesInfo' => array(), // Headlines of pages
    'pagesAbstracts' => array(), // Abstracts of pages
    'date' => '',
    'headline' => '',
    'images' => null,
    'inlineImages' => null, // These are only available after running cbTagRendererClassicM->render()
    'downloadFiles' => null,
    'mediaFiles' => null,
    'otherFiles' => null,
    'aAbstract' => ''
  );

  /**
   * Konstruktor
   * _________________________________________________________________
   */
  function __construct($articleBox, $articleName, $cbbr = null)
  {
    $this->articleBox = $articleBox;

    // Artikelname und -pfad setzen
    $this->articleName = $articleName;

    // Compiled JSON file
    $this->articleF = getPathFS(CB_DATA_ROOT.$this->articleBox.CB_BUILT.$this->articleName.'.json');

    // Artikel Box und Name setzen
    $this->data['articleBox'] = $articleBox;
    $this->data['articleName'] = $articleName;

    // We need the classic tag renderer
    $this->cbbr = ($cbbr == null) ? new cbTagRendererClassicM() : $cbbr;
		$this->cbbr->articlePath = CB_DATA_ROOT.$this->articleBox.CB_DATA_ASSETS.$this->articleName.DIRECTORY_SEPARATOR;

    $this->fm = new cbFilesCrudM($this->articleBox);
    $this->tM = new cbTextClassicM($this->articleBox, $this->articleName);
    $this->tM->load();
  }

  /**
   * Artikel anlegen, alle Daten setzen, rendern, speichern.
   * _________________________________________________________________
   */
  function load()
  {
    try
    {
      $text = $this->tM->getArticleText();
      $this->data['articleText'] = $text;
      $dateHeadText = $this->tM->getDateHeadlineBody();
  
      if ($dateHeadText !== false)
      {
        $this->data['date']          = $dateHeadText['articleDate'];
        $this->data['headline']      = $dateHeadText['articleHeadline'];
        $articleBody                 = $dateHeadText['articleBody'];
        
        $this->data['paginatedText'] = $this->parseArticlePaginatedText($articleBody);
        $this->data['aAbstract']     = $this->parseArticleAbstract($articleBody);
  
        $this->data['images']        = $this->fm->fetchImagesForArticle($this->articleName);
        list($this->data['downloadFiles'], $this->data['mediaFiles'], $this->data['otherFiles']) = $this->fm->fetchFilesForArticle($this->articleName);
  
        $this->renderArticleFindMetadataAndInlineImages();
  
        $this->createPagesAbstracts();
        
        $this->saveToFile();
  
        return $this->data;
      }
      else
      {
        throw new Exception(__CLASS__.': Fehler beim Parsen der Artikeldatei.');
      }
    }
    catch(Exception $e)
    {
      throw $e;
    }
  }

  /**
   * Artikeltext parsen
   * ___________________________________________________________________
   */
  function parseArticlePaginatedText($text)
  {
    $pages = array();
    $rPages = array(array());
    $pagesInfo = array();

    if (preg_match_all('/\[page\](.*)\[\/page\]/Us', $text, $pagesInfo, PREG_PATTERN_ORDER))
    {
      $this->data['pagesInfo'] = $pagesInfo[1];
    }

    $pages = preg_split('/\[page\](.*)\[\/page\]/Us', $text, null, PREG_SPLIT_NO_EMPTY); //PREG_SPLIT_DELIM_CAPTURE
    if (count($pages) == 1)
    { // Keine Page - Tags vorhanden, automatische Paginierung
      $textArr = explode($this->paragraphSep, $text);
      $pIdx = 0;
      $i = 0;
      foreach ($textArr as $textChunk)
      {
        $rPages[$pIdx][] = $textChunk;
        if ($i == ($this->pageParagraphLimit-1))
        {
          $pIdx++;
          $i = 0;
        }
        else
        {
          $i++;
        }
      }
    }
    else
    { // Page - Tags vorhanden, manuelle Paginierung
      $i = 0;
      for ($i = 0; $i < count($pages); $i++)
      {
        $textArr = explode($this->paragraphSep, $pages[$i]);
        $rPages[$i] = $textArr;
      }
    }

    return $rPages;
  }

  /**
   * Abstract = Zusammenfassung bauen
   * __________________________________________________________________
   */
  function parseArticleAbstract($text)
  {
    $text = $this->cbbr->bbRenderStrip($text);
    $text = strip_tags($text);
    $text = (strlen($text) >= $this->abstractSize) ? mb_substr($text, 0, mb_strpos($text, ' ', $this->abstractSize)) : $text;
    $text = $text.'...';

    return $text;
  }
  
  /**
   * Tag Renderer aufrufen
   * _________________________________________________________________
   */
  function renderArticleFindMetadataAndInlineImages($page = null)
  {
    // Reset array
    $this->cbbr->data['inlineImages'] = array();
  
    // Render text
    if ($page === null)
    { // Render all pages
      for ($page=0; $page < count($this->data['paginatedText']); $page++)
      {
        for ($paragraph=0; $paragraph < count($this->data['paginatedText'][$page]); $paragraph++)
        {
          $this->data['paginatedText'][$page][$paragraph] = $this->cbbr->render($this->data['paginatedText'][$page][$paragraph]);
        }
      }
    }
    else
    { // Render specified page only
      for ($paragraph=0; $paragraph < count($this->data['paginatedText'][$page]); $paragraph++)
      {
        $this->data['paginatedText'][$page][$paragraph] = $this->cbbr->render($this->data['paginatedText'][$page][$paragraph]);
      }
    }

    // Store inline images from our rendered [img] tags
    $this->data['inlineImages'] = $this->cbbr->data['inlineImages'];
    
    // Try to extract meta data
    $this->data['author'] = (isset($this->cbbr->hints['author'])) ? $this->cbbr->hints['author'] : '';
  }

  /**
   * Seitenzusammenfassungen erstellen
   * __________________________________________________________________
   */
  public function createPagesAbstracts()
  {
    $textA = $this->data['paginatedText'];
    
    foreach ($textA as $text)
    {
  	  $str = $text[0];
      $str = $this->cbbr->bbRenderStrip($str);
      $str = strip_tags($str);
      $str = (strlen($str) >= $this->abstractSize) ? mb_substr($str, 0, mb_strpos($str, ' ', $this->abstractSize)) : $str;
      $str = $str.'...';
      $this->data['pagesAbstracts'][] = $str;
    }
  }
  
  /**
   * Save to Disk
   * ___________________________________________________________________
   */
  public function saveToFile()
  {
    jsonM::save($this->articleF, $this->data);
  }
  
}

?>
