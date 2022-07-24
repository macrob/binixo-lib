<?php

include(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php');
define('APP_ROOT', realpath(__DIR__));
// define('TMP_DIR', realpath(APP_ROOT . '/../tmp/'));
define('TMP_DIR', realpath(APP_ROOT . '/tmp/'));

$biLib = new \BinixoLib\Offerwall();

$biLib->url = 'https://kz.binixocrm.com/fd/offerwall/lender/json2?id=6193a180100734dc7cf60c01';
$biLib->urlMob = 'https://kz.binixocrm.com/fd/offerwall/lender/json2?id=6193a1a2100734dc7cf60c2d';
$biLib->offerwallJs = 'https://cdn.binixocrm.com/js/v1/offerwall-0.0.1.js';


$biLib->injectJs(true);
$biLib->printJsonOffersMob('offersMob');
$biLib->printJsonOffersDesktop('offersDesk');

?>
  <div id="offerwall"></div>
  <script>
    window.addEventListener("load", async() => {

      await ofr.render({
        offers: {
          mob: offersMob,
          desktop: offersDesk
        },
        selector: '#offerwall',
        currency: 'KZT',
        lang: 'ru'
      });

    });
  </script>