<?php

class commentsC extends kupcC
{
  /**
   * Kommentare löschen, Admin-Comment hinzufügen
   * _________________________________________________________________
   */
  public function run($op)
  {
    $cbc = new cbCommentsM($this->articleBox);

    $this->initView('commentsVP');
    $cbc->load($this->article);

    switch($op)
    {
      case 'delete':
        $which = $this->requestM->getReqVar('which');
        $status = ($cbc->commentsDeleteEntry($this->article, $which) === true) ? 'Kommentar gelöscht.' : 'Kommentar konnte nicht gelöscht werden.';
        $this->view->setData('status', $status);
        $this->view->setData('comments', $cbc->getComments($this->article));
        $this->view->drawPage();

      break;

      case 'adminCommentEdit': // Ajax
        $which = $this->requestM->getReqVar('which');
        $text = $this->requestM->getReqVar('text');
        $status = ($cbc->commentsUpdateAdminComment($this->article, $which, $text) === true) ? 'Admin-Kommentar aktualisiert.' : 'Admin-Kommentar nicht aktualisiert.';
        $this->view->setData('status', $status);
        $this->drawJson();
      break;

      default:
        $this->view->setData('comments', $cbc->getComments($this->article));
        $this->view->drawPage();
      break;
    }
  }
}

?>
