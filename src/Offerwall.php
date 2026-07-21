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
  /** @var int 0-based индекс, с которого начинать (пропуск первых N офферов) */
  public $offset = 0;

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
    $offsetKey = (string) $this->normalizeOffset();
    $cacheName = $isMob
      ? $md5.'_offerwall-mob-o'.$offsetKey.'-l'.$limitKey.'.php'
      : $md5.'_offerwall-desktop-o'.$offsetKey.'-l'.$limitKey.'.php';

    $cache = new Cache($cacheName, $this->cacheTtl);

    if (!$cache->isExist()) {
      $offers = $isMob ? $this->getOffersMob() : $this->getOffersDesktop();
      $offers = $this->applyOffsetAndLimit($offers);
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
      'tpl' => is_numeric($this->tpl) ? (0 + $this->tpl) : $this->tpl,
      'limit' => $this->normalizeLimit(),
      'offset' => $this->normalizeOffset() ?: null,
    ], function ($value) {
      return $value !== null && $value !== '';
    });

    $this->printJsVaraible($variableName, $options);
  }

  /**
   * Количество офферов в фиде (без учёта offset/limit).
   * @param bool|null $forceMob null — определить по User-Agent
   */
  public function getOfferCount($forceMob = null)
  {
    if ($forceMob === null) {
      $detect = new MobileDetect();
      $isMob = $detect->isMobile();
    } else {
      $isMob = (bool) $forceMob;
    }

    $offers = $isMob ? $this->getOffersMob() : $this->getOffersDesktop();
    return count($offers);
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

  private function normalizeOffset()
  {
    if (!is_numeric($this->offset)) {
      return 0;
    }
    $offset = (int) $this->offset;
    return $offset > 0 ? $offset : 0;
  }

  private function applyOffsetAndLimit($offers)
  {
    if (!is_array($offers)) {
      return $offers;
    }
    $list = array_values($offers);
    $offset = $this->normalizeOffset();
    $limit = $this->normalizeLimit();

    if ($offset > 0) {
      $list = array_slice($list, $offset);
    }
    if ($limit !== null) {
      $list = array_slice($list, 0, $limit);
    }
    return $list;
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
