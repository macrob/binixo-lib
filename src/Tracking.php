<?php

https://infinsa.g2afse.com/click?pid=2&offer_id=1343&
// sub1=[source]&sub2=[ga]&sub3=[clga]&sub4=[shortid]&sub5=[loc]&sub6=[webid]

namespace BinixoLib;

// https://binixo.vn/?utm_source=affise&utm_medium=11&utm_campaign=62ea2cfaec918200015a7671&utm_content=060522
class Tracking {
  public $loc;
  public $ga;

  public $utmSource;
  public $utmMedium;
  public $utmCampaign;
  public $utmContent;
  public $shortid;

  public $gclid;

  public $clga;
  
  public $js = 'https://cdn.binixocrm.com/js/v1/tracking-0.0.5.js';
  public $redic = 'cr3d.loan';

  /* kz 'Rv8vVDLRTcyKdXJ9VzkFtw' */
  public $gaApiKey;

  /*kz 'G-8EXH7F19PM' */
  public $gaMeasurementId;

  function __construct($loc, $ga) {
    $this->loc = $loc;
    $this->ga = $ga;
  }

  /* GA4 metods */
  private function getGACookieSessionKey() {
    return '_ga_'.substr($this->gaMeasurementId, 2);;
  }

  public function getGASessionId() {
    $sessionId = $_COOKIE[$this->getGACookieSessionKey()] ?? null;
    if($sessionId === null) {
      return;
    }

    return explode('.', $sessionId)[2];
  }

  public function getGAClientId() {
    $sessionId = $_COOKIE['_ga'] ?? null;
    if($sessionId === null) {
      return;
    }
    
    return explode('.', $sessionId, 3)[2];
  }

  public function getGAMeasurementId() {
    return $this->gaMeasurementId;
  }

  public function getGAApiKey() {
    return $this->gaApiKey;
  }


  public function getJs() {
    $gaAttr = '';
    if ($this->gaMeasurementId && $this->gaApiKey) {
      $gaAttr = "data-ga_api_key=\"{$this->getGAApiKey()}\" data-ga_measurement_id=\"{$this->getGAMeasurementId()}\" data-ga_session_id=\"{$this->getGASessionId()}\" data-ga_client_id=\"{$this->getGAClientId()}\"";
    }

    return "<script src=\"{$this->js}\" data-ga=\"{$this->ga}\" data-loc=\"{$this->loc}\" data-redic=\"{$this->redic}\" {$gaAttr}>  </script>";
  }

  public function injectJs($isInline = false)
  {
    if ($isInline) {
      $request = new HttpRequest();
      $content = $request->get($this->js);

      print "<script>{$content}</script>";
    } else {

      // <script src="https://cdn.binixocrm.com/js/v1/tracking-0.0.3.js" data-ga="UA-111418536-21" data-loc="kz"
      // data-redic="cr3d.loan"> </script>

      print $this->getJs();
    }
  }

  public function replace($content) {
    $params = array(
      '[shortid]' => $this->shortid,

      '[source]' => $this->utmSource,
      '[medium]' => $this->utmMedium,
      '[campaign]' => $this->utmCampaign,
      '[content]' => $this->utmContent,
      '[webid]' => $this->utmContent,

      '[loc]' => $this->loc,
      '[ga]' => $this->ga,

      '[gclid]' => $this->gclid,

    );

    return str_replace(array_keys($params), array_values($params), $content);
  }

  public function detectParams() {
    $this->utmSource = isset($_REQUEST['utm_source']) ? $_REQUEST['utm_source'] : null;
    $this->utmMedium = isset($_REQUEST['utm_medium']) ? $_REQUEST['utm_medium'] : null;
    $this->utmCampaign = isset($_REQUEST['utm_campaign']) ? $_REQUEST['utm_campaign'] : null;
    $this->utmContent = isset($_REQUEST['utm_content']) ? $_REQUEST['utm_content'] : null;
    $this->shortid = isset($_REQUEST['shortid']) ? $_REQUEST['shortid'] : null;
    $this->gclid = isset($_REQUEST['gclid']) ? $_REQUEST['gclid'] : null;

    if ($this->utmSource === null && isset($_COOKIE['utm_source'])) {
      $this->utmSource = $_COOKIE['utm_source'];
    }

    if ($this->utmMedium === null && isset($_COOKIE['utm_medium'])) {
      $this->utmMedium = $_COOKIE['utm_medium'];
    }

    if ($this->utmCampaign === null && isset($_COOKIE['utm_campaign'])) {
      $this->utmCampaign = $_COOKIE['utm_campaign'];
    }

    if ($this->utmContent === null && isset($_COOKIE['utm_content'])) {
      $this->utmContent = $_COOKIE['utm_content'];
    }

    if ($this->shortid === null && isset($_COOKIE['shortid'])) {
      $this->shortid = $_COOKIE['shortid'];
    }

    if ($this->gclid === null && isset($_COOKIE['gclid'])) {
      $this->gclid = $_COOKIE['gclid'];
    }

    $this->save();
  }

  public function save() {

    setcookie('utm_source', $this->utmSource, time()+3600*24*365, '/');
    setcookie('utm_medium', $this->utmMedium, time()+3600*24*365, '/');
    setcookie('utm_campaign', $this->utmCampaign, time()+3600*24*365, '/');
    setcookie('utm_content', $this->utmContent, time()+3600*24*365, '/');
  }

}
