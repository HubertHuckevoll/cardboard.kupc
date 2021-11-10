<?php

/**
 * kupc Router
 * ___________________________________________________________________
 */
class kupcR
{
  public $pageCntrl = null;
  public $op = '';

  /**
   * Konstruktor
   * _________________________________________________________________
   */
  public function __construct($cbbr = null, $gbbr = null, $userCSSf = null, $ep = 'kupc.php')
  {
    $requestM = new cbRequestM();
    $ep   = ($ep == '') ? basename($_SERVER["SCRIPT_NAME"]) : $ep;
    $hook = $requestM->getReqVar('hook');
    $this->op = $requestM->getReqVar('op');

    // check hook
    if ($hook === false)
    {
      $hook = 'login';
    }

    $pageCntrlName = $hook.'C';
    $this->pageCntrl = new $pageCntrlName($cbbr, $gbbr, $userCSSf, $ep);

    if ($this->pageCntrl->login() === true)
    {
      $this->pageCntrl->hook = $hook;
      $hasArticles = $this->pageCntrl->init();

      // if box is empty, redirect to controller that adds article
      if (
          (!$hasArticles) && ($this->op !== 'newEnd') &&
          ($hook !== 'logout')
         )
      {
        $pageCntrlName = ($this->pageCntrl->box['type'] == 'classic') ? 'manageClassicC' : 'manageGDocsC';
        $this->pageCntrl = new $pageCntrlName($cbbr, $gbbr, $userCSSf, $ep);
        $this->pageCntrl->hook = $hook;
        $this->op = 'newStart';
        $this->pageCntrl->login();
        $this->pageCntrl->init();
      }
    }
    else // if login failed, return to login screen
    {
      $pageCntrlName = 'loginC';
      $this->pageCntrl = new $pageCntrlName($cbbr, $gbbr, $userCSSf, $ep);
      $this->pageCntrl->hook = $hook;
    }
  }

  /**
   * run!
   * _________________________________________________________________
   */
  public function run()
  {
    try
    {
      $this->pageCntrl->run($this->op);
    }
    catch(Exception $e)
    {
      die($e->getMessage());
    }
  }
}

?>
