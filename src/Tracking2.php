<?php
namespace BinixoLib;


class Tracking2 {
  private $loc;

  private $params = [
    'utm_source' => null,
    'utm_medium' => null,
    'utm_campaign' => null,
    'utm_content' => null,

    'shortId' => null,
    'gclid' => null,

    'aff' => null
  ];

  private $jsLibSrc;
  private $trackingDomain;

  /* GA4 */
  private $gaApiKey;
  private $gaMeasurementId;

  function __construct($loc) {
    $this->loc = $loc;
  }

  /* GA4 metods */
  private function getGACookieSessionKey() {
    return '_ga_'.substr($this->gaMeasurementId, 2);;
  }

  private function getGASessionId() {
    $sessionId = $_COOKIE[$this->getGACookieSessionKey()] ?? null;
    if($sessionId === null) {
      return;
    }

    $sessionIdParts = explode('.', $sessionId);
    $tPart = isset($sessionIdParts[2]) ? $sessionIdParts[2] : 0;
    return $tPart;
  }

  private function getGAClientId() {
    $sessionId = $_COOKIE['_ga'] ?? null;
    if($sessionId === null) {
      return;
    }
    
    return explode('.', $sessionId, 3)[2];
  }

  private function getGAMeasurementId() {
    return $this->gaMeasurementId;
  }

  private function getGAApiKey() {
    return $this->gaApiKey;
  }

  public function getJs() {
    $gaAttr = '';
    if ($this->gaMeasurementId && $this->gaApiKey) {
      $gaAttr = "data-ga_api_key=\"{$this->getGAApiKey()}\" data-ga_measurement_id=\"{$this->getGAMeasurementId()}\" data-ga_session_id=\"{$this->getGASessionId()}\" data-ga_client_id=\"{$this->getGAClientId()}\"";
    }

    $utmAttr = [];
    foreach ($this->params as $paramName => $val) {
      $getParamName = strtolower($paramName);

      $utmAttr[] = "data-{$getParamName}=\"{$val}\"";
    }

    $utmAttrStr = implode(' ', $utmAttr);
    return "<script src=\"{$this->jsLibSrc}\" data-loc=\"{$this->loc}\" data-tracking=\"{$this->getTrackingDomain()}\" {$gaAttr} {$utmAttrStr}>  </script>";
  }

  private function getTrackingDomain() {
    return preg_replace('#^(?:https?://)?/?(.*?)/?$#', '$1', $this->trackingDomain);
  }

  public function setTrackingDomain($value) {
    return $this->trackingDomain = $value;
  }

  public function setJsLibSrc($value) {
    return $this->jsLibSrc = $value;
  }

  public function setGAMeasurementId($value) {
    $this->gaMeasurementId = $value;
  }

  public function setGAApiKey($value) {
    return $this->gaApiKey = $value;
  }

  public function injectJs()
  {
    print $this->getJs();
  }

  public function replace($content) {
    $params = [];

    foreach ($this->params as $paramName => $val) {
      $paramName = str_replace('utm_', '', $paramName);
      $params["[$paramName]"] = $val;
    }

    $params['[loc]'] = $this->loc;
    $params['[webid]'] = $this->params['utm_content']; //deprecated

    return str_replace(array_keys($params), array_values($params), $content);
  }

  
  public function detectParams() {
    foreach ($this->params as $paramName => $val) {
      $getParamName = strtolower($paramName);

      if (isset($_GET[$getParamName])) {
          $this->params[$paramName] = $_GET[$getParamName];
          $this->save($paramName);
      } elseif (isset($_COOKIE[$paramName])) {
        $this->params[$paramName] = $_COOKIE[$paramName];
      }
    }
  }

  public function save($paramName) {
    setcookie($paramName, $this->params[$paramName], time()+3600*24*365, '/');
  }

}
