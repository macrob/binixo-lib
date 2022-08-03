<?php
namespace BinixoLib;

class Offerwall {
  public $url;
  public $urlMob;
  public $tpl = '1';
  public $lang = 'ru';
  public $currency;

  public $offerwallJs;

  function __construct() {
    if (isset($_GET['nocache'])) {
      Cache::truncate();
    }
  }

  public function render()
  {
    print $this->fetch();
  }

  public function fetch()
  {
    $template = new Template();
    $template->tpl = $this->tpl;
    $template->lang = $this->lang;
    $template->currency = $this->currency;

    $detect = new \Mobile_Detect();

    $isMob = $detect->isMobile();
    
    $md5 = md5 ($_SERVER['REQUEST_URI']);
    $cacheName = $isMob ? $md5.'_offerwall-mob.php' : $md5.'_offerwall-desktop.php';

    $cache = new Cache($cacheName);

    if (!$cache->isExist()) {
      $offers = $isMob ? $this->getOffersMob() : $this->getOffersDesktop();
      $content = $template->fetch($isMob, $offers);
      $cache->save($content);
    }

    return $cache->get();
  }

  public function injectJs($isInline = false)
  {
    if ($isInline) {
      $request = new HttpRequest();
      $content = $request->get($this->offerwallJs);

      print "<script>{$content}</script>";
    } else {
      print "<script src=\"{$this->offerwallJs}\">  </script>";
    }
  }

  private function getOffersDesktop() {
    $request = new HttpRequest();
    return $request->getJson($this->url, [], true);
  }

  private function getOffersMob() {
    $request = new HttpRequest();
    return $request->getJson($this->urlMob, [], true);
  }



  public function printJsonOffersDesktop($variableName)
  {
    $content = $this->getOffersDesktop();
    $this->printJsVaraible($variableName, $content);
  }

  public function printJsonOffersMob($variableName)
  {
    $content = $this->getOffersMob();
    $this->printJsVaraible($variableName, $content);
  }

  private function printJsVaraible($variableName, $variableValue)
  {
    $variableValue = json_encode($variableValue);
    print "<script> const {$variableName} =  {$variableValue}; </script>";
  }


}
