<?php

class gtoolsC extends kupcC
{
  /**
   * gtools
   * __________________________________________________________________
   */
  public function run($op)
  {
    switch($op)
    {
      case 'thumbsForAllArticles':
        $this->initView('gtoolsVP');
        $result = $this->fM->updateThumbsForAllArticles();
        $this->view->setData('status', $result.' Thumbnail/s für alle Artikel in der Box neu angelegt.');
        $this->view->drawPage();
      break;

      case 'resetPrefsForAllArticles':
        $this->initView('gtoolsVP');
        if ($this->prefsM->resetAllPrefs($this->articleBox) == false)
        {
          $this->view->setData('status', __CLASS__.': Rücksetzen der Prefs für die Artikelbox "'.$this->articleBox.'" fehlgeschlagen.');
        }
        else
        {
          $this->view->setData('status', 'Prefs für alle Artikel in der Artikelbox "'.$this->articleBox.'" zurückgesetzt.');
        }
        $this->view->drawPage();
      break;

      case 'buildAll':
        try
        {
          $this->initView('gtoolsVP');

          if ($this->box['type'] == 'classic')
          {
            $m = new cbBuilderClassicM($this->articleBox, $this->cbbr);
            $m->load();
          }
          elseif ($this->box['type'] == 'gdocs')
          {
            $m = new cbBuilderGDocsM($this->articleBox, $this->gbbr);
            $m->load();
          }

          $this->setArticleFirstArticleInBox();
          $this->pushArticleAndArticleListToView();
          $this->view->setData('status', 'Box-Inhalte erfolgreich neu erstellt.');
          $this->view->drawPage();
        }
        catch(Exception $e)
        {
          $this->view->setData('status', $e->getMessage());
          $this->view->drawPage();
        }
      break;

      default:
        $this->initView('gtoolsVP');
        $this->view->drawPage();
      break;
    }
  }
}

?>
