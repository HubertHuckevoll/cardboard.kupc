<?php

class cbFilesCrudM
{
  protected $articleBox = '';
  protected $assetsPath = '';
  protected $article = '';

  public $thumbWidth = 200;

  /**
   * Konstruktor
   * _________________________________________________________________
   */
  public function __construct($articleBox)
  {
    if ($articleBox == '')
    {
      throw new Exception(__CLASS__.': Der Name der angeforderten Artikel-Box war leer.');
    }

    $this->articleBox = $articleBox;
    $this->assetsPath = CB_DATA_ROOT.$this->articleBox.CB_DATA_ASSETS;

    if (!file_exists(getPathFS($this->assetsPath)))
    {
      @mkdir(getPathFS($this->assetsPath), 0777, true); //create directories recursively
    }
  }

  /**
   * Artikel löschen
   * _________________________________________________________________
   */
  public function deleteArticle($article)
  {
    $path = $this->getArticleAssetsPathFS($article);

    if (file_exists($path))
    {
      $files = scanpath($path);
      foreach ($files as $file)
      {
        @unlink($path.$file);
      }

      $erg = @rmdir($path);
      if ($erg === false)
      {
        throw new Exception(__CLASS__.': Pfad ('.$path.') konnte nicht gelöscht werden.');
      }
    }
  }

  /**
   * Artikel umbenennen
   * _________________________________________________________________
   */
  public function renameArticle($article, $newname)
  {
    if (file_exists($this->getArticleAssetsPathFS($article)))
    {
      $erg = @rename($this->getArticleAssetsPathFS($article), $this->getArticleAssetsPathFS($newname));

      if ($erg === false)
      {
        throw new Exception(__CLASS__.': Umbenennen fehlgeschlagen.');
      }
    }
  }

  /**
   * alle Dateien abholen
   * _________________________________________________________________
   */
  public function getFiles($article)
  {
    $data = array();

    $data['images'] = $this->fetchImagesForArticle($article);
    list($data['downloadFiles'], $data['mediaFiles'], $data['otherFiles']) = $this->fetchFilesForArticle($article);

    return $data;
  }

  /**
   * Thumbnails für Artikel anlegen
   * _________________________________________________________________
   */
  public function createThumbs($article)
  {
    $thumbWidth = null;
    $numThumbs = 0;
    $articlePath = $this->getArticleAssetsPathFS($article);

    if (is_dir($articlePath))
    {
      $rawFiles = scanpath($articlePath);

      foreach($rawFiles as $file)
      {
        $thumbWidth = $this->thumbWidth;
        if (
                (!strstr(strtolower($file), '_thumb.gif'))
             && (!strstr(strtolower($file), '_thumb.jpg'))
           )
        {
          if (
                  strstr(strtolower($file), '.jpg')
               || strstr(strtolower($file), '.png')
             )
          {
            $fname = $articlePath.$file;
            $basename = pathinfo($file, PATHINFO_FILENAME);
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $thumb = $articlePath.strtolower($basename).'_thumb.jpg';

            list($width, $height) = getimagesize($fname);

            if ($thumbWidth < $width)
            {
              $ratio = ($width / $thumbWidth);
              $thumbHeight = ceil($height / $ratio);
            }
            else
            {  // Make images only smaller, never larger
              $thumbWidth = $width;
              $thumbHeight = $height;
            }

            if ($ext == 'jpg')
            {
              $src_img = imagecreatefromjpeg($fname);
            }
            elseif ($ext == 'png')
            {
              $src_img = imagecreatefrompng($fname);
            }

            if (isset($src_img))
            {
              $dst_img = imagecreatetruecolor($thumbWidth, $thumbHeight);
              imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
              imagejpeg($dst_img, $thumb, 75);
              imagedestroy($dst_img);
              imagedestroy($src_img);
              $numThumbs++;
            }
          }
        }
      }
    }

    return $numThumbs;
  }

  /**
   * Thumbs für ALLE Artikel in der Box neu anlegen.
   * Es muss doch EINEN Vorteil haben, dass wir hier
   * immer auf Box Level operieren...
   * _________________________________________________________________
   */
  public function updateThumbsForAllArticles()
  {
    $result = 0;
    $articleNames = scanpath(getPathFS($this->assetsPath));

    foreach($articleNames as $articleName)
    {
      $result += $this->createThumbs($articleName);
    }

    return $result;
  }

  /**
   * Bildbeschreibung aktualisieren
   * _________________________________________________________________
   */
  public function updateImageDesc($article, $file, $desc)
  {
    $ret = null;
    $descFile = $this->getArticleAssetsPathFS($article).pathinfo($file, PATHINFO_FILENAME).'.txt';
    $erg = @file_put_contents($descFile, $desc);

    if ($erg === false)
    {
      throw new Exception(__CLASS__.': Beschreibung konnte nicht aktualisiert werden.');
    }
  }

  /**
   * Datei umbenennen
   * Achtung: Artikel muss anschliessend neu gebaut oder
   * Dateien neu eingelesen werden!
   * _________________________________________________________________
   */
  public function renameArticleFile($article, $file, $newName)
  {
    $orgBase = pathinfo($file, PATHINFO_FILENAME);
    $newBase = pathinfo($newName, PATHINFO_FILENAME);

    $orgF = $this->getArticleAssetsPathFS($article).$file;
    $orgFDesc = $this->getArticleAssetsPathFS($article).$orgBase.'.txt';
    $orgFThumb = $this->getArticleAssetsPathFS($article).$orgBase.'_thumb.jpg';

    $newF = $this->getArticleAssetsPathFS($article).$newName;
    $newFDesc = $this->getArticleAssetsPathFS($article).$newBase.'.txt';
    $newFThumb = $this->getArticleAssetsPathFS($article).$newBase.'_thumb.jpg';

    if (@rename($orgF, $newF) === true)
    {
      @rename($orgFDesc, $newFDesc);
      @rename($orgFThumb, $newFThumb);
    }
    else
    {
      throw new Exception(__CLASS__.': Datei konnte nicht umbenannt werden.');
    }
  }

  /**
   * Dateien hochladen
   * _________________________________________________________________
   */
  public function uploadFiles($article)
  {
    $suc = null;
    $path = $this->getArticleAssetsPathFS($article);

    if (!is_dir($path))
    {
      @mkdir($path, 0777, true); // create directories recursively
    }

    foreach($_FILES as $file)
    {
      $fname = $path.strtolower(makeFileName(getFileBasename($file['name'])).'.'.getFileExt($file['name']));
      if (move_uploaded_file($file['tmp_name'], $fname))
      {
        $suc++;
      }
    }

    if ($suc !== null)
    {
      $this->createThumbs($article);
    }
    else
    {
      throw new Exception(__CLASS__.': Konnte Dateien nicht hochladen.');
    }

    return $suc;
  }

  /**
   * Datei löschen
   * _________________________________________________________________
   */
  public function deleteFile($article, $file)
  {
    $file = $this->getArticleAssetsPathFS($article).$file;

    if (file_exists($file))
    {
      @unlink($file);
      $base = pathinfo($file, PATHINFO_FILENAME);
      @unlink($this->getArticleAssetsPathFS($article).$base.'.txt');
      @unlink($this->getArticleAssetsPathFS($article).$base.'_thumb.jpg');
    }
    else
    {
      throw new Exception(__CLASS__.': Datei "'.$file.'" konnte nicht gefunden / gelöscht werden.');
    }
  }

  /**
   * Alle zum Artikel gehörigen Bilder einlesen
   * _________________________________________________________________
   */
  function fetchImagesForArticle($article)
  {
    $files = array();
    $path = $this->getArticleAssetsPathFS($article);

    if (is_dir($path))
    {
      $rawFiles = scanpath($path);

      foreach($rawFiles as $file)
      {
        if (
                (!strstr(strtolower($file), '_thumb.gif'))
             && (!strstr(strtolower($file), '_thumb.jpg'))
           )
        {
          if (
                  strstr(strtolower($file), '.jpg')
               || strstr(strtolower($file), '.png')
             )
          {
            $imgStruct = $this->fetchImageInfo($article, $file);
            $files[] = $imgStruct;
          }
        }
      }
    }

    return $files;
  }

  /**
   * Meta-Infos zu Bildern einlesen
   * _________________________________________________________________
   */
  function fetchImageInfo($article, $file)
  {
    $path = $this->getArticleAssetsPath($article);
    $basename = pathinfo($file, PATHINFO_FILENAME);

    $ret['fname'] = $file;
    $ret['path'] = $path;
    $ret['file'] = $path.$file;
    $ret['fileInfo'] = '';
    $ret['thumb'] = $path.strtolower($basename).'_thumb.jpg';
    $ret['width'] = 0;
    $ret['height'] = 0;
    $ret['thumbWidth'] = 0;
    $ret['thumbHeight'] = 0;

    $imgInfoFile = $path.$basename.'.txt';

    // Fetch image dimensions, calc thumbs
    list($ret['width'], $ret['height']) = @getimagesize(getPathFS($ret['file']));

    if (file_exists(getPathFS($ret['thumb'])))
    {
      list($ret['thumbWidth'], $ret['thumbHeight']) = @getimagesize(getPathFS($ret['thumb']));
    }
    else
    {
      $ret['thumb'] = '';
    }

    // Read description
    if (is_file(getPathFS($imgInfoFile)))
    {
      $str = @file_get_contents(getPathFS($imgInfoFile));
      $str = trim($str);
      $str = mb_convert_encoding($str, "UTF-8");
      $ret['fileInfo'] = $str;
    }

    return $ret;
  }

  /**
   * Alle zum Artikel gehörigen Dateien einlesen
   * _________________________________________________________________
   */
  function fetchFilesForArticle($article)
  {
    $ret = array(array(), array(), array());
    $path = $this->getArticleAssetsPathFS($article);

    if (is_dir($path))
    {
      $rawFiles = scanpath($path);
      foreach ($rawFiles as $file)
      {
        if (
            (getFileExt($file) == 'zip') ||
            (getFileExt($file) == 'pdf')
           )
        {
          $ret[0][] = $this->fetchFileInfo($article, $file);
        }
        elseif (
                 (getFileExt($file) == 'flv') ||
                 (getFileExt($file) == 'mp4') ||
                 (getFileExt($file) == 'mp3')
               )
        {
          $ret[1][] = $this->fetchFileInfo($article, $file);
        }
        elseif (
                (getFileExt($file) != 'txt') &&
                (getFileExt($file) != 'json') &&
                (getFileExt($file) != 'png') &&
                (getFileExt($file) != 'jpg') &&
                (getFileExt($file) != 'jpeg') &&
                (is_file($this->getArticleAssetsPathFS($article).$file))
               )
        {
          $ret[2][] = $this->fetchFileInfo($article, $file);
        }
      }
    }

    return $ret;
  }

  /**
   * Fetch file info
   * _________________________________________________________________
   */
  function fetchFileInfo($article, $file)
  {
    $path = $this->getArticleAssetsPath($article);
    $ret['fname'] = $file;
    $ret['path'] = $path;
    $ret['file'] = $path.$file;

    return $ret;
  }

  /**
   * get absolute path in the filesystem
   * _________________________________________________________________
   */
  public function getArticleAssetsPathFS($article)
  {
    return getPathFS($this->assetsPath.$article.DIRECTORY_SEPARATOR);
  }

  /**
   * get path relative to document root
   * ________________________________________________________________
   */
  public function getArticleAssetsPath($article)
  {
    return $this->assetsPath.$article.DIRECTORY_SEPARATOR;
  }

}

?>
