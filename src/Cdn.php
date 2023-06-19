<?php 
namespace BinixoLib;

define('CDN_OFFERWALL', 'https://cdn.binixocrm.com/offerwall');
define('CDN_LEADFORM', 'https://cdn.binixocrm.com/leadform');

class Cdn {
  
  /*
  *  'https://cdn.binixocrm.com/offerwall' -  offerwall'
  *   'https://cdn.binixocrm.com/leadform' -  leadform'
  */
  
  // offerwall || leadform
  public $type = 'offerwall';


  private $filename;

  function __construct($filename, $type='offerwall') {
    $this->filename = $filename;
    $this->type = $type;
  }

  private function getCacheFilename() {
    // if(strpos($this->filename, 'lang') !== false) {
    //   return $this->type.'_'.$this->filename;
    // } else {
    //   return $this->filename;
    // }

    return $this->type.'_'.$this->filename;
  }

  public function get() {
    $cacheFile = new Cache($this->getCacheFilename());

    if (!$cacheFile->isExist()) {
      $content = $this->getContent();
      $cacheFile->save($content);
    }

    return $cacheFile->get();
  }

  public function download() {
    $cacheFile = new Cache($this->getCacheFilename());
    $content = $this->getContent();
    $cacheFile->save($content);
  }

  private function getCdnUrl() {
    switch($this->type) {
      case 'offerwall':
        return CDN_OFFERWALL;
      break;
      case 'leadform':
        return CDN_LEADFORM;
      break;
    }
  }
  private function getContent() {
    return file_get_contents($this->getCdnUrl() . '/' . $this->filename);
  }

}