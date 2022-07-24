<?php
namespace BinixoLib;

class Offerwall {
  public $url;
  public $urlMob;
  public $tpl = '1';
  public $lang = 'ru';

  public $offerwallJs;

  function __construct() {
    if (isset($_GET['cache'])) {
      Cache::truncate();
    }
  }

  public function render()
  {
    $template = new Template();
    $template->tpl = $this->tpl;
    $template->lang = $this->lang;

    $detect = new \Mobile_Detect();

    $isMob = $detect->isMobile();

    $cacheName = $isMob ? 'offerwall-mob.php' : 'offerwall-desktop.php';

    $cache = new Cache($cacheName);

    if (!$cache->isExist()) {
      $offers = $isMob ? $this->getOffersMob() : $this->getOffersDesktop();
      $content = $template->fetch($isMob, $offers);
      $cache->save($content);
    }

    $cache->print();
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


}
