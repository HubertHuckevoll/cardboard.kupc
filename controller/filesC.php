<?php

class filesC extends kupcC
{
  /**
   * Dateien
   * _________________________________________________________________
   */
  public function run($op)
  {
    switch($op)
    {
      case 'show':
        $this->initView('filesShowVP');
        $files = $this->fM->getFiles($this->article);
        $this->view->setData('files', $files);
        $this->view->drawPage();
      break;

      case 'uploadStart':
        $this->initView('filesUploadVP');
        $this->view->drawPage();
      break;

      case 'uploadEnd':
        $this->initView('filesShowVP');
        try
        {
          $ret = $this->fM->uploadFiles($this->article);
          $this->view->setData('status', $ret.' Datei/en hochgeladen.');
          $files = $this->fM->getFiles($this->article);
          $this->view->setData('files', $files);

          $this->buildArticle($this->article);
          $this->view->drawPage();
        }
        catch (Exception $e)
        {
          $this->view->setData('status', $e->getMessage());
          $this->view->drawPage();
        }
      break;

      case 'deleteEnd':
        $this->initView('filesShowVP');
        $file = $this->requestM->getReqVar('file');
        try
        {
          $this->fM->deleteFile($this->article, $file);
          $this->view->setData('status', 'Datei und ggf. Thumbnail gelöscht.');
          $files = $this->fM->getFiles($this->article);
          $this->view->setData('files', $files);

          $this->buildArticle($this->article);
          $this->view->drawPage();
        }
        catch (Exception $e)
        {
          $this->view->setData('status', $e->getMessage());
          $this->view->drawPage();
        }
      break;

      case 'descEditor': // AJAX
        $file = $this->requestM->getReqVar('file');
        $desc = $this->requestM->getReqVar('desc');
        $this->initView('filesShowVP');
        try
        {
          $this->fM->updateImageDesc($this->article, $file, $desc);
          $this->view->setData('status', 'Beschreibung aktualisiert.');

          $this->buildArticle($this->article);
          $this->view->drawJson();
        }
        catch (Exception $e)
        {
          $this->view->setData('status', $e->getMessage());
          $this->view->drawJson();
        }
      break;

      case 'fnameEditor': // AJAX
        $file = $this->requestM->getReqVar('file');
        $newname = $this->requestM->getReqVar('newname');
        $newname = makeFileName(getFileBasename($newname)).'.'.getFileExt($newname);

        $this->initView('filesShowVP');
        $this->view->setData('saneName', $newname); //return sanitized name
        try
        {
          $this->fM->renameArticleFile($this->article, $file, $newname);
          $this->view->setData('status', 'Datei umbenannt.');

          $this->buildArticle($this->article);
          $this->view->drawJson();
        }
        catch (Exception $e)
        {
          $this->view->setData('status', $e->getMessage());
          $this->view->drawJson();
        }
      break;

      case 'thumbsForArticle':
        try
        {
          $this->initView('filesShowVP');
          $files = $this->fM->getFiles($this->article);
          $this->view->setData('files', $files);
          $result = $this->fM->createThumbs($this->article);
          $this->view->setData('status', $result.' Thumbnail/s neu angelegt.');

          $this->buildArticle($this->article);
          $this->view->drawPage();
        }
        catch (Exception $e)
        {
          $this->view->setData('status', $e->getMessage());
          $this->view->drawPage();
        }
      break;
    }
  }
}

?>
