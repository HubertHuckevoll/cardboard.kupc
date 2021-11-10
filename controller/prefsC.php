<?php

class prefsC extends kupcC
{
  /**
   * Prefs
   * _________________________________________________________________
   */
  public function run($op)
  {
    $prefs = array();

    switch($op)
    {
      case 'articlePrefs':
        $this->initView('prefsVP');
        if (($prefs = $this->prefsM->getPrefs($this->articleBox, $this->article)) == false)
        {
          $this->view->setData('status', __CLASS__.': Fehler beim Abholen der Prefs für "'.$this->article.'".');
          $this->view->setData('prefs', array());
        }
        else
        {
          $this->view->setData('prefs', $prefs);
        }
        $this->view->drawPage();
      break;

      case 'setPref': // AJAX
        $prefKey = $this->requestM->getReqVar('prefKey');
        $prefVal = $this->requestM->getReqVar('prefVal');
        $this->initView('prefsVP');
        if ($this->prefsM->setPref($this->articleBox, $this->article, $prefKey, $prefVal) == false)
        {
          $this->view->setData('status', __CLASS__.': Aktualisieren von "'.$prefKey.'" mit "'.$prefVal.'" fehlgeschlagen.');
        }
        else
        {
          $this->view->setData('status', 'Einstellung aktualisiert.');
        }
        $this->view->drawJson();
      break;

      case 'reset': // Not ajax
        $this->initView('prefsVP');
        if ($this->prefsM->resetPrefs($this->articleBox, $this->article) == false)
        {
          $this->view->setData('status', __CLASS__.': Rücksetzen der Prefs für den Artikel "'.$this->article.'" fehlgeschlagen.');
          $this->view->setData('prefs', array());
        }
        else
        {
          $prefs = $this->prefsM->getPrefs($this->article);
          $this->view->setData('prefs', $prefs);
          $this->view->setData('status', 'Alle Einstellungen für Artikel "'.$this->article.'" zurückgesetzt.');
        }
        $this->view->drawPage();
      break;
    }
  }
}

?>
