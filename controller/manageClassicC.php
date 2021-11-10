<?php

class manageClassicC extends kupcC
{
  /**
   * CRUD operations for classic articles
   * __________________________________________________________________
   */
  public function run($op)
  {
    switch($op)
    {
      case 'tools':
        $this->initView('toolsVP');
        $this->view->drawPage();
      break;

      case 'newStart':
        $this->initView('createVP');
        $newArticleName = (($newArticleName = $this->requestM->getReqVar('newArticleName')) != '') ? $newArticleName : date("Y-m-d");
        $this->view->setData('articleName', $newArticleName);
        $this->view->drawPage();
      break;

      case 'newEnd':
        $newArticleName = makeFileName($this->requestM->getReqVar('newArticleName'));
        try
        {
          $this->initView('editVP');
          $this->tM->newArticle($newArticleName);
          $this->setArticle($newArticleName);
          $this->buildArticle($this->article);
          $this->buildBox();
          $this->pushArticleAndArticleListToView();

          $this->view->setData('text', date("Y-m-d").': '.$newArticleName);
          $this->view->setData('status', 'Artikel erfolgreich angelegt.');
          $this->view->drawPage();
        }
        catch(Exception $e)
        {
          $this->initView('createVP');
          $this->view->setData('status', $e->getMessage());
          $this->view->setData('articleName', $newArticleName);
          $this->view->drawPage();
        }
      break;

      case 'remove':
        try
        {
          $this->initView('redirectVP');
          $this->tM->deleteArticle($this->article);
          $this->fM->deleteArticle($this->article);
          $this->prefsM->resetPrefs($this->articleBox, $this->article);
          $this->commentsM->deleteArticle($this->article);
          $this->view->setData('status', 'Artikel "'.$this->article.'" gel&ouml;scht.');
          $this->buildBox();

          $this->setArticleFirstArticleInBox();
          $this->pushArticleAndArticleListToView();

          $this->view->setData('href', array('hook' => 'text', 'op'=>'load', 'article' => $this->article));
          $this->view->drawPage();
        }
        catch(Exception $e)
        {
          $this->initView('toolsVP');
          $this->view->setData('status', $e->getMessage());
          $this->view->drawPage();
        }
      break;

      case 'rename':
        try
        {
          $this->initView('toolsVP');
          $newname = makeFileName($this->requestM->getReqVar('newname'));
          $this->tM->renameArticle($this->article, $newname);
          $this->fM->renameArticle($this->article, $newname);
          $this->prefsM->renameArticle($this->articleBox, $this->article, $newname);
          $this->commentsM->renameArticle($this->article, $newname);

          $this->setArticle($newname);
          $this->buildArticle($newname);
          $this->buildBox();
          $this->pushArticleAndArticleListToView();

          $this->view->setData('status', 'Artikel umbenannt.');
          $this->view->drawPage();
        }
        catch (Exception $e)
        {
          $this->initView('toolsVP');
          $this->view->setData('status', $e->getMessage());
          $this->view->drawPage();
        }
      break;
    }
  }
}

?>
