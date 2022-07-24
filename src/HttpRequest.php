<?php
namespace BinixoLib;


class HttpRequest {
  public $httpCode;

  public function get($url, $params = array()) {
    return $this->request($url, $params, 'GET');
  }

  public function getJson($url, $params = array(), $associative = false) {
    return json_decode($this->get($url, $params), $associative);
  }

  private function request($url, $params = array(), $requestType = 'GET')
  {
    $type = strtoupper($requestType);
    if (!in_array($type, array('GET', 'POST', 'PUT', 'DELETE'))) {
      $type = 'GET';
    }

    $options = array(
      CURLOPT_URL            => $url,
      CURLOPT_CUSTOMREQUEST  => $type,
      CURLOPT_CONNECTTIMEOUT => 10,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
    );

    $ch = curl_init();

    if ($type == 'GET') {
      $query = parse_url($url, PHP_URL_QUERY);

      if ($query) {
        $url .= '&' . $this->httpBuildQuery($params);
      } else {
        $url .= '?' . $this->httpBuildQuery($params);
      }

      $options[CURLOPT_URL] = $url;
    } else {
      $options[CURLOPT_POST] = true;
      if (array_filter($params, 'is_object')) {
        $options[CURLOPT_POSTFIELDS] = $params;
      } else {
        $options[CURLOPT_POSTFIELDS] = $this->httpBuildQuery($params);
      }
    }

    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    $error = curl_error($ch);

    $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($error) {
      throw new Exceptions\Http($error);
    }

    return $response;
  }

  /**
   * Build HTTP query
   *
   * @param array $params
   *
   * @return string
   */
  private function httpBuildQuery($params = array())
  {
    return http_build_query($params, '', '&', PHP_QUERY_RFC1738);
  }
}