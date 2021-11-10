<?php

class manageGDocsC extends kupcC
{
  /**
   * CRUD operations for GDocs articles
   * __________________________________________________________________
   */
  public function run($op)
  {
    try
    {
      switch ($op)
      {
        case 'tools':
          $this->initView('toolsVP');
          $entry = $this->tM->getArticle($this->article);
          $this->view->setData('articleName', $entry['articleName']);
          $this->view->setData('date', $entry['date']);
          $this->view->setData('gurl', $entry['gurl']);
        break;

        case 'newStart':
          $this->initView('createVP');
          $this->view->setData('date', time());
        break;

        case 'newEnd':
          try
          {
            $this->initView('editVP');
            $name = makeFileName($this->requestM->getReqVar('articleName'));
            $date = strtotime($this->requestM->getReqVar('date'));
            $gurl = $this->requestM->getReqVar('gurl');
            $entry = $this->tM->newArticle($name, $date, $gurl);
            $this->setArticle($name);
            $this->buildArticle($this->article);
            $this->buildBox();

            $m = new cbTextGDocsM($this->articleBox, $this->article);
            $m->load();
            $data = $m->getArticleText();
            $this->pushArticleAndArticleListToView();
            $this->view->setData('text', $data);
            $this->view->setData('status', 'Artikel hinzugefügt.');
          }
          catch (Exception $e)
          {
            $this->initView('createVP');
            $this->view->setData('status', $e->getMessage());
            $this->view->setData('articleName', $name);
            $this->view->setData('date', time());
            $this->view->setData('gurl', $gurl);
          }
        break;

        case 'update':
          $this->initView('toolsVP');
          $name = makeFileName($this->requestM->getReqVar('articleName'));
          $newName = makeFileName($this->requestM->getReqVar('newArticleName'));
          $date = strtotime($this->requestM->getReqVar('date'));
          $gurl = $this->requestM->getReqVar('gurl');
          $entry = $this->tM->updateArticle($name, $newName, $date, $gurl);
          $this->buildArticle($this->article);
          $this->buildBox();

          $this->setArticle($newName);
          $this->pushArticleAndArticleListToView();
          $this->view->setData('articleName', $newName);
          $this->view->setData('date', $date);
          $this->view->setData('gurl', $gurl);
          $this->view->setData('status', 'Artikel aktualisiert.');
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
          }
          catch (Exception $e)
          {
            $this->initView('toolsVP');
            $this->view->setData('status', $e->getMessage());
            $this->view->setData('articleName', $name);
            $this->view->setData('date', time());
            $this->view->setData('gurl', $gurl);
          }
        break;
      }
      $this->view->drawPage();
    }
    catch (Exception $e)
    {
      $this->view->setData('status', $e->getMessage());
      $this->view->drawPage();
    }
  }
}

?>
