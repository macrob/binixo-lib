<?php
namespace BinixoLib;

// https://binixo.vn/?utm_source=affise&utm_medium=11&utm_campaign=62ea2cfaec918200015a7671&utm_content=060522
class Tracking {
  public $utmSource;
  public $utmMedium;
  public $utmCampaign;
  public $utmContent;

  function __construct() {
  }

  public function detectParams() {
    $this->utmSource = isset($_REQUEST['utm_source']) ? $_REQUEST['utm_source'] : null;
    $this->utmMedium = isset($_REQUEST['utm_medium']) ? $_REQUEST['utm_medium'] : null;
    $this->utmCampaign = isset($_REQUEST['utm_campaign']) ? $_REQUEST['utm_campaign'] : null;
    $this->utmContent = isset($_REQUEST['utm_content']) ? $_REQUEST['utm_content'] : null;

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
    $this->save();
  }

  public function save() {
    setcookie('utm_source', $this->utmSource, time()+3600*24*365, '/');
    setcookie('utm_medium', $this->utmMedium, time()+3600*24*365, '/');
    setcookie('utm_campaign', $this->utmCampaign, time()+3600*24*365, '/');
    setcookie('utm_content', $this->utmContent, time()+3600*24*365, '/');
  }

}
