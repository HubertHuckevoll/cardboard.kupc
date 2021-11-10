<?php

class textC extends kupcC
{
  /**
   * text operations - read / save
   * __________________________________________________________________
   */
  public function run($op)
  {
    $m = ($this->box['type'] == 'classic') ? new cbTextClassicM($this->articleBox, $this->article) : new cbTextGDocsM($this->articleBox, $this->article);
    $m->load();

    switch($op)
    {
      case 'load':
        $this->initView('editVP');

        $this->view->setData('text', $m->getArticleText());
        $this->view->setData('status', '');
        $this->view->drawPage();
      break;

      // this can only be triggered in classic articles -
      // editV.php doesn't have "save" for GDocs for now
      case 'save':
        $this->initView('editVP');
        $text = $this->requestM->getReqVar('data', false); // getReqVar('data', false): "false" = do not strip tags;

        try
        {
          $m->updateArticleText($text);
          $this->buildArticle($this->article);
          $this->view->setData('status', 'Aktualisiert.');
        }
        catch (Exception $e)
        {
          $this->view->setData('status', $e->getMessage());
        }
        $this->view->drawJson();

      break;
    }
  }
}

?>
