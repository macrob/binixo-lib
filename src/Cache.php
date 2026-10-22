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

    $mtime = @filemtime($path);
    if ($mtime === false) {
      return false;
    }

    if ($this->ttl !== null && (time() - $mtime) >= $this->ttl) {
      @unlink($path);
      return false;
    }

    return true;
  }

  public function save($content) {
    $filePth = self::getFilename($this->filename);
    $fileDirname = pathinfo($filePth, PATHINFO_DIRNAME);

    if (!is_dir($fileDirname) && !@mkdir($fileDirname, 0777, true) && !is_dir($fileDirname)) {
      throw new \Exception('Cache: cannot create dir ' . $fileDirname);
    }

    // атомарно: параллельный include() не должен видеть недописанный файл
    $tmp = $filePth . '.' . uniqid('', true) . '.tmp';
    file_put_contents($tmp, $content);
    rename($tmp, $filePth);
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