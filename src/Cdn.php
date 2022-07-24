<?php 
namespace BinixoLib;


class Cdn {
  const CDN = 'https://cdn.binixocrm.com/offerwall';

  private $filename;

  function __construct($filename) {
    $this->filename = $filename;
  }

  public function get() {
    $cacheFile = new Cache($this->filename);

    if (!$cacheFile->isExist()) {
      $content = $this->getContent();
      $cacheFile->save($content);
    }

    return $cacheFile->get();
  }

  public function download() {
    $cacheFile = new Cache($this->filename);
    $content = $this->getContent();
    $cacheFile->save($content);
  }

  private function getContent() {
    return file_get_contents(self::CDN . '/' . $this->filename);
  }

}