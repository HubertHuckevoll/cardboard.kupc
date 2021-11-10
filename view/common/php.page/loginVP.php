<?php
namespace view\common\page;
class loginVP extends \cb\view\page\cbPageVP
{
  /**
   * Lodschin
   * _______________________________________________________________
   */
  public function drawPage($errMsg = '')
  {
    $html .= '<!DOCTYPE HTML>
              <html lang="de">
              <head>
                <title>Willkommen - Klecks und Pinsel-Club</title>
                <meta charset="UTF-8">
                <meta http-equiv="Cache-Control" content="no-store, no-cache, max-age=0, must-revalidate">
                <meta http-equiv="pragma" content="no-cache">
                <meta http-equiv="content-language" content="de">
                <meta name="robots" content="noindex,nofollow">
                <meta name="description" content="Klecks und Pinsel-Club">
                <link rel="shortcut icon" href="'.CB_ROOT.'favicon.ico">
                <link rel="stylesheet" type="text/css" href="'.CB_KUPC_ROOT.'view/common/css/desktop.css" title="CSS" media="screen, projection">
                <style>
                  h2 {
                    margin-top: 0px;
                    margin-bottom: 5px;
                  }

                  #loginBox {
                    box-shadow: 10px 10px 5px #888;
                  }
                </style>
                <script>
                  document.addEventListener("DOMContentLoaded", function() {
                    document.querySelector(\'input[name="login"]\').focus();
                  });
                </script>
              </head>
              <body>
              <div id="loginBox">
                <img src="'.CB_KUPC_ROOT.'view/common/assets/tintenfleck.gif" style="float: left; margin-left: 20px; margin-top: 15px;"></img>
                <h2>&bdquo;Klecks und Pinsel-Club&ldquo;</h2>
                car<span style="font-style: italic;">db</span>oard&nbsp;<span id="cbVer">'.$this->data['ver'].'</span>
                <form name="loginForm" id="loginForm" action="'.$this->linker->href($this->ep, array('hook'=>'text', 'op'=>'load')).'" method="POST">
                 <span>Login</span>&nbsp;&nbsp;<input type="text" name="login" id="login" size="37"></input><br />
                 <span>Passwort</span>&nbsp;&nbsp;<input type="password" name="password" id="password" size="37"></input><br />
                 <input type="submit" class="cButton b_login" value="Anmelden" />
                </form>
              </div>
              </body>
              </html>';

    echo $html;
  }

}

?>
