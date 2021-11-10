<?php

class kupcC extends cbPageC
{
  public $box = array();
  public $articleBox = '';

  public $article = '';

  public $internal = false;
  public $loggedIn = false;

  public $ep = '';

  public $defaultCSS = array();
  public $defaultCSSFile = 'cbUserClasses.css';

  public $userCSSf = '';
  public $userCSS = array();

  public $cbbr = null; // classic bb render
  public $gbbr = null; // gdocs bb render

  public $tM = null; // either classic or gdocs text crud
  public $fM = null;  // files crud
  public $prefsM = null; // prefs model
  public $commentsM = null; // comments model

  /**
   * Konstruktor
   * ________________________________________________________________
   */
  public function __construct($cbbr = null, $gbbr = null, $userCSSf = null, $ep = 'kupc.php')
  {
    session_start();

    parent::__construct();

    $this->ep = $ep;

    $this->userCSSf = $userCSSf;
    $this->cbbr = ($cbbr == null) ? new cbTagRendererClassicM() : $cbbr;
    $this->gbbr = ($gbbr == null) ? new cbTagRendererGDocsM()   : $gbbr;
  }

  /**
   * login a new
   * ________________________________________________________________
   */
  public function login()
  {
    $boxes = $this->boxes->get();

    $login = $this->requestM->getReqVar('login');
    $pass = $this->requestM->getReqVar('password');

    if (
          ($login != '') &&
          ($pass  != '')
       )
    {
      $box = $boxes[$login];
      $articleBox = $box['box'];

      if ($this->boxExists($articleBox))
      {
        if (password_verify($pass, $box['pwd']))
        {
          $_SESSION['loggedIn'] = $login;

          $this->loggedIn = true;
          $this->box = $box;
          $this->articleBox = $articleBox;

          return true;
        }
      }
    }
    elseif (
            ($login == '') &&
            ($pass == '') &&
            (isset($_SESSION['loggedIn']))
           )
    {
      $this->loggedIn = true;
      $this->box = $boxes[$_SESSION['loggedIn']];
      $this->articleBox = $this->box['box'];

      return true;
    }

    return false;
  }

  /**
   * do some setup
   * ________________________________________________________________
   */
  public function init()
  {
    $numArticles = 0;

    try
    {
      if ($this->box['type'] == 'classic')
      {
        $this->tM = new cbTextCrudClassicM($this->articleBox);
      }
      elseif ($this->box['type'] == 'gdocs')
      {
        $this->tM = new cbTextCrudGDocsM($this->articleBox);
      }

      $this->tM->load();

      if (count($this->tM->getArticles()) > 0)
      {
        $this->fM        = new cbFilesCrudM($this->articleBox);
        $this->commentsM = new cbCommentsM($this->articleBox);

        $this->prefsM    = cbArticlePrefsM::getInstance();

        $article = $this->requestM->getReqVar('article');
        if ($article != '')
        {
          $this->setArticle($article);
        }
        else
        {
          $this->setArticleFirstArticleInBox();
        }

        $this->pushArticleAndArticleListToView();

        return true;
      }
      else
      {
        return false;
      }
    }
    catch(Exception $e)
    {
      $this->article = '';
      return false;
    }
    finally
    {
      $this->internal = (isset($this->box['internal'])) ? $this->box['internal'] : false;
      $this->defaultCSS = $this->getCSS(getPathFS(CB_CSS_ROOT.$this->defaultCSSFile));
      $this->userCSS = $this->getCSS($this->userCSSf);
    }
  }

  /**
   * if we have an article, set an internal field for easier access
   * _________________________________________________________________
   */
  public function setArticle($article)
  {
    $this->article = $article;
  }

  /**
   * set article to first article in box
   * _________________________________________________________________
   */
  public function setArticleFirstArticleInBox()
  {
    try
    {
      $cbaArr = $this->tM->getArticles();
      $keys = array_keys($cbaArr);
      $articleName = $cbaArr[$keys[0]]['articleName'];
      $this->setArticle($articleName);
    }
    catch(Exception $e)
    {
      throw $e;
    }
  }

  /**
   * push the articles to the view
   * _________________________________________________________________
   */
  public function pushArticleAndArticleListToView()
  {
    try
    {
      if ($this->view !== null)
      {
        $this->view->setData('article', $this->article);

        if ($this->tM !== null)
        {
          $articles = $this->tM->getArticles();
          $this->view->setData('articles', $articles);
        }
      }
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }


  /**
   * read a CSS file and return the classes
   * return an empty array if no css is found
   * _________________________________________________________________
   */
  public function getCSS($cssf)
  {
    $cssc = array();

    if (file_exists($cssf))
    {
      $text = file_get_contents($cssf);

      $ret = preg_match_all("/\/\*\s*(.*)\s*\*\/\s*\.(.*)\s*\{/", $text, $cssc, PREG_SET_ORDER);
      if ($ret)
      {
        foreach($cssc as &$c)
        {
          $c[1] = trim($c[1]);
          $c[2] = trim($c[2]);
        }

        return $cssc;
      }
    }

    return array();
  }

  /**
   * existiert die Box überhaupt?
   * _________________________________________________________________
   */
  public function boxExists($box)
  {
    if ($box != '')
    {
      $files = scanpath(getPathFS(CB_DATA_ROOT));
      foreach ($files as $file)
      {
        if ($file === $box)
        {
          return true;
        }
      }
    }
    return false;
  }

  /**
   * initView
   * _________________________________________________________________
   */
  public function initView($uiViewName = '')
  {
    $this->ui = ($this->box['type'] == 'classic') ? 'classic' : 'gdocs';
    parent::initView($uiViewName);

    $this->view->setData('articleBox', $this->articleBox);
    $this->view->setData('internal', $this->internal);
    $this->view->setData('boxNameAlias', $this->box['alias']);

    $this->view->setData('tags', $this->cbbr->getTagDescs());
    $this->view->setData('smileyTable', $this->cbbr->smileyTable);
    $this->view->setData('defaultCSS', $this->defaultCSS);
    $this->view->setData('userCSSf', $this->userCSSf);
    $this->view->setData('userCSS', $this->userCSS);
    $this->view->setData('ver', getVer());

    $this->pushArticleAndArticleListToView();
  }

  /**
   * run - overwritten by our children
   * _________________________________________________________________
   */
  public function run($op)
  {
  }

  /**
   * Wrapper: Update Box
   * _________________________________________________________________
   */
  public function buildBox()
  {
    try
    {
      if ($this->box['type'] == 'classic')
      {
        $bcbm = new cbBoxBuilderClassicM($this->articleBox);
      }
      elseif ($this->box['type'] == 'gdocs')
      {
        $bcbm = new cbBoxBuilderGDocsM($this->articleBox);
      }

      $bcbm->load();
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }

  /**
   * Wrapper: Update Article
   * _________________________________________________________________
   */
  public function buildArticle($article)
  {
    try
    {
      if ($this->box['type'] == 'classic')
      {
        $artObj = new cbArticleBuilderClassicM($this->articleBox, $article, $this->cbbr);
      }
      elseif ($this->box['type'] == 'gdocs')
      {
        $entry = $this->tM->getArticle($article);
        $artObj = new cbArticleBuilderGDocsM($this->articleBox, $entry, $this->gbbr);
      }

      $artData = $artObj->load();
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }
}

?>
