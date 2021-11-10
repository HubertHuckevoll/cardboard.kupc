<?php

class loginC extends kupcC
{
  /**
   * draw KuPC login screen
   * _________________________________________________________________
   */
  public function run($op)
  {
    $this->initView('view\common\page\loginVP');
    $this->view->drawPage();
  }
}

?>
