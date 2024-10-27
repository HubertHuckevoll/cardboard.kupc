<?php
namespace view\gdocs\page;
class filesUploadVP extends kupcGDocsVP
{
  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct($ep, $hook, $linker = null)
  {
    parent::__construct($ep, $hook, $linker);
    $this->active = 'filesUpload';
  }

  /**
   * Dateien hochladen
   * _______________________________________________________________
   */
  protected function mainContent()
  {
    $article = $this->data['article'];

    $cont = '<form class="prefBox" name="uploadForm" method="post" enctype="multipart/form-data" action="'.$this->linker->href($this->ep, array('hook'=>'files', 'op'=>'uploadEnd', 'article'=>$this->getData('article'))).'">'.
              '<div class="caption">Datei(en) hinzuf&uuml;gen...</div>'.
              '<div class="prefLine">'.
                '<div>'.
                  '<input type="file" name="file_0" id="file_0" size="40"></input>&nbsp;&nbsp;'.
                  '<input type="file" name="file_1" id="file_1" size="40"></input>'.
                '</div>'.
                '<div>'.
                  '<input type="file" name="file_2" id="file_2" size="40"></input>&nbsp;&nbsp;'.
                  '<input type="file" name="file_3" id="file_3" size="40"></input>'.
                '</div>'.
                '<div>'.
                  '<input type="file" name="file_4" id="file_4" size="40"></input>&nbsp;&nbsp;'.
                  '<input type="file" name="file_5" id="file_5" size="40"></input>'.
                '</div>'.
                '<div>'.
                  '<input type="file" name="file_6" id="file_6" size="40"></input>&nbsp;&nbsp;'.
                  '<input type="file" name="file_7" id="file_7" size="40"></input>'.
                '</div>'.
                '<div>'.
                  '<input type="file" name="file_8" id="file_8" size="40"></input>&nbsp;&nbsp;'.
                  '<input type="file" name="file_9" id="file_9" size="40"></input>'.
                '</div>'.
                '<div class="prefLine">'.
                  '<a class="cButton b_upload" href="javascript:document.uploadForm.submit();">Hochladen</a>&nbsp;&nbsp;'.
                  '<a class="cButton b_cancel" href="'.$this->linker->href($this->ep, array('hook'=>'files','op'=>'show','article'=>$this->getData('article'))).'">Abbrechen</a>'.
                '</div>'.
              '</div>'.
            '</form>';

    return $cont;
  }

}


?>
