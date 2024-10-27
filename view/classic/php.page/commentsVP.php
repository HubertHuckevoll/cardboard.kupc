<?php
namespace view\classic\page;
class commentsVP extends kupcClassicVP
{
  /**
   * Konstruktor
   * _______________________________________________________________
   */
  public function __construct($ep, $hook, $linker = null)
  {
    parent::__construct($ep, $hook, $linker);
    $this->active = 'comments';
  }

  /**
   * Kommentare
   * _______________________________________________________________
   */
  protected function mainContent()
  {
    $comments = $this->data['comments'];
    $cont  = '';
    $cont .= '<div class="prefBox">';
    $cont .= '<div class="caption">Kommentare verwalten</div>';
    $cont .= '<div class="prefLine">';

    if (($comments !== false) && (count($comments) > 0))
    {
      $i = 0;
      foreach ($comments as $comment)
      {
        $sender = $comment['sender'];
        $time = (int) $comment['time'];

        $msg = $comment['message'];
        $msg = preg_replace('/(http:\/\/.*)(\s|$)/', '<a href="\\1" target="_blank" title="\\1">\\1</a>', $msg);
        $msg = str_replace("\r", '', $msg);
        $msg = str_replace("\n", '<br />', $msg);

        $str = '<div class="caption">'.
                 '<a href="'.$this->linker->href($this->ep, array('hook'=>'comments', 'op'=>'delete', 'which'=>$i, 'article'=>$this->getData('article'))).'">[X]</a>&nbsp;'.
                  '"'.$sender.'" schrieb am '.date("d.m.y, H:i", $time).' Uhr'.
               '</div>'.
               '<div>'.$msg.'</div>'.
               '<div><textarea class="adminCommentEditor" data-which="'.$i.'" data-article="'.$this->getData('article').'">'.$comment['adminComment'].'</textarea></div>';

        $cont .= $str.$cont;
        $i++;
      }
    }
    else
    {
      $cont .= '<div>Keine Einträge.</div>';
    }

    $cont .= '</div>'; // prefLine
    $cont .= '</div>'; // prefBox

    return $cont;
  }
}

?>
