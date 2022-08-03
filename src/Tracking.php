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

  public $source;
  public $clga;
  

  function __construct($loc, $ga) {
    $this->loc = $loc;
    $this->ga = $ga;
  }




  public function replace($content) {
    $params = array(
      '[shortid]' => $this->shortid,

      '[source]' => $this->utmSource,
      '[medium]' => $this->utmMedium,
      '[campaign]' => $this->utmCampaign,
      '[content]' => $this->utmContent,

      '[loc]' => $this->loc,
      '[webid]' => $this->utmContent,
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
