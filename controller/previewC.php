<?php

class previewC extends kupcC
{
  /**
   * Preview laden
   * Achtung: Ajax
   * _________________________________________________________________
   */
  public function run($op)
  {
    $cba = new cbArticleM($this->articleBox, $this->article);

    $this->initView('previewVP');
    $cba->load();
    $pa = $cba->getArticle();

    $this->view->setData('article', $pa);
    $this->view->drawAjax();
  }
}

?>
