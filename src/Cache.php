<?php 
namespace BinixoLib;
if (!defined('TMP_DIR') || !file_exists(TMP_DIR)) {
  throw new \Exception('TMP_DIR not defined or directory is it not exist');
}

class Cache 
{
  private $filename;

  public static function getFilename ( $filename ) {

    $pth = TMP_DIR . DIRECTORY_SEPARATOR . $filename;

    return $pth;
  }

  public static function truncate() {
    $it = new \RecursiveDirectoryIterator(TMP_DIR, 
      \RecursiveDirectoryIterator::SKIP_DOTS);

    $files = new \RecursiveIteratorIterator($it,
      \RecursiveIteratorIterator::CHILD_FIRST);
    
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
  }

  function __construct($filename) {
    $this->filename = $filename;
  }

  public function getPath() {
    return self::getFilename($this->filename);
  }

  public function isExist() {
    return file_exists(self::getFilename($this->filename));
  }

  public function save($content) {
    $filePth = self::getFilename($this->filename);
    $fileDirname = pathinfo($filePth, PATHINFO_DIRNAME);


    if (!file_exists($fileDirname)) {
      mkdir($fileDirname, 0777, true);
    };

    file_put_contents($filePth, $content);
  }

  public function print() {
    print file_get_contents(
      self::getFilename($this->filename)
    );
  }

  public function get() {
    return file_get_contents(
      self::getFilename($this->filename)
    );
  }
}