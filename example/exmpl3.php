<?php

include(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php');
define('APP_ROOT', realpath(__DIR__));
// define('TMP_DIR', realpath(APP_ROOT . '/../tmp/'));
define('TMP_DIR', realpath(APP_ROOT . '/tmp/'));

$biLib = new \BinixoLib\Offerwall();


$biLib->tpl = '1';
$biLib->lang = 'ru';
$biLib->currency = 'KZT';

$biLib->url = 'https://kz.binixocrm.com/fd/offerwall/lender/json2?id=6193a180100734dc7cf60c01';
$biLib->urlMob = 'https://kz.binixocrm.com/fd/offerwall/lender/json2?id=6193a1a2100734dc7cf60c2d';
$biLib->offerwallJs = 'https://cdn.binixocrm.com/js/v1/offerwall-v2.0.2.js';


try {
  // $request->test();
  // $test = new \BinixoLib\Exceptions\Http('dasds');

  $biLib->injectJs(true);
  $biLib->render();

} catch (\BinixoLib\Exceptions\Http $e) {
  var_dump($e);
  exit;
}