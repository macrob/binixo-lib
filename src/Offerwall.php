<?php
namespace BinixoLib;
use Detection\MobileDetect;

class Offerwall {
  public $url;
  public $urlMob;
  public $tpl = '1';
  public $lang = 'ru';
  public $currency;
  /** @var int|null время жизни кеша в секундах; null — без ограничения */
  public $cacheTtl = 60;
  /** @var int|null сколько офферов рисовать за раз (SSR / клиентский bootstrap) */
  public $limit;

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

    $detect = new MobileDetect();

    $isMob = $detect->isMobile();
    
    $md5 = md5 ($_SERVER['REQUEST_URI']);
    $limitKey = $this->normalizeLimit() === null ? 'all' : (string) $this->normalizeLimit();
    $cacheName = $isMob
      ? $md5.'_offerwall-mob-'.$limitKey.'.php'
      : $md5.'_offerwall-desktop-'.$limitKey.'.php';

    $cache = new Cache($cacheName, $this->cacheTtl);

    if (!$cache->isExist()) {
      $offers = $isMob ? $this->getOffersMob() : $this->getOffersDesktop();
      $offers = $this->applyLimit($offers);
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

  /**
   * Печатает опции для new ofr.Offerwall(...) без загрузки/дампа офферов.
   * Офферы подтянет сам JS по url/urlMob.
   */
  public function printClientOptions($selector = '#offerwall', $variableName = 'offerwallOptions')
  {
    $options = array_filter([
      'url' => $this->url,
      'urlMob' => $this->urlMob,
      'selector' => $selector,
      'currency' => $this->currency,
      'lang' => $this->lang,
      'tpl' => is_numeric($this->tpl) ? (int) $this->tpl : $this->tpl,
      'limit' => $this->normalizeLimit(),
    ], function ($value) {
      return $value !== null && $value !== '';
    });

    $this->printJsVaraible($variableName, $options);
  }

  private function getOffersDesktop() {
    $request = new HttpRequest();
    return $this->normalizeOffers($request->getJson($this->url, [], true));
  }

  private function getOffersMob() {
    $request = new HttpRequest();
    return $this->normalizeOffers($request->getJson($this->urlMob, [], true));
  }

  private function normalizeOffers($data)
  {
    if (is_array($data)) {
      if (array_keys($data) === range(0, count($data) - 1)) {
        return $data;
      }
      if (isset($data['offers']) && is_array($data['offers'])) {
        return $data['offers'];
      }
      if (isset($data['data']) && is_array($data['data'])) {
        return $data['data'];
      }
    }

    return is_array($data) ? $data : [];
  }

  private function normalizeLimit()
  {
    if (!is_numeric($this->limit)) {
      return null;
    }
    $limit = (int) $this->limit;
    return $limit > 0 ? $limit : null;
  }

  private function applyLimit($offers)
  {
    $limit = $this->normalizeLimit();
    if ($limit === null || !is_array($offers)) {
      return $offers;
    }
    return array_slice(array_values($offers), 0, $limit);
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
