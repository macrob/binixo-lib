<?php 
namespace BinixoLib;
if (!defined('TMP_DIR') || !file_exists(TMP_DIR)) {
  throw new \Exception('TMP_DIR not defined or directory is it not exist');
}

class Cache 
{
  private $filename;
  private $ttl;

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

  /**
   * @param string $filename
   * @param int|null $ttl время жизни кеша в секундах; null — без ограничения
   */
  function __construct($filename, $ttl = null) {
    $this->filename = $filename;
    $this->ttl = $ttl;
  }

  public function getPath() {
    return self::getFilename($this->filename);
  }

  public function isExist() {
    $path = self::getFilename($this->filename);

    if (!file_exists($path)) {
      return false;
    }

    if ($this->ttl !== null && (time() - filemtime($path)) >= $this->ttl) {
      unlink($path);
      return false;
    }

    return true;
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