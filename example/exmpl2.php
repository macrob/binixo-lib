<?php

include(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php');


define('CRM2_ENDPOINT_V3', 'https://binixo.mx/s3');
define('OFFERWALL_V3', '6699019ca45acda8280739a4');
define('OFFERWALL_MOBILE_V3', '6699087ea45acda8280739a5');  // advert
// локальный билд для демо; на проде — CDN offerwall-v2.0.3.js
define('LIB_JS_OFFERWALL_V2', 'js/offerwall-v2.0.8.js');


define('APP_ROOT', realpath(__DIR__));
// define('TMP_DIR', realpath(APP_ROOT . '/../tmp/'));
define('TMP_DIR', realpath(APP_ROOT . '/tmp/'));

$biLib = new \BinixoLib\Offerwall();

$biLib->tpl = '3';
$biLib->lang = 'es';
$biLib->currency = 'MXN';
$biLib->limit = 8;
// $biLib->offset = 0; // с какого оффера начинать (0-based)

$biLib->url = CRM2_ENDPOINT_V3 . '/offers/' . OFFERWALL_V3;
$biLib->urlMob = CRM2_ENDPOINT_V3 . '/offers/' . OFFERWALL_MOBILE_V3;
$biLib->offerwallJs = LIB_JS_OFFERWALL_V2;

// JS либа + опции (без дампа офферов)
$biLib->injectJs(false);
$biLib->printClientOptions('#offerwall');

$offerCount = $biLib->getOfferCount();

?>
<link rel="stylesheet" href="https://binixo.mx/css/offers-tpl3.css">

<div class="offers">

  <div id="offerwall">
    <?php
      // первый экран — серверный рендер (offset + limit)
      $biLib->render();
    ?>
  </div>
  <button id="next-btn" type="button">Next</button>
  <span id="status"></span>
  <script>
    window.addEventListener("load", async() => {
      offerwallOptions.tpl = '3.1';
      const instance = new ofr.Offerwall(offerwallOptions);

      // PHP уже отрисовал первую страницу — только подгружаем список для next()
      await instance.resume({
        offset: 8,
        limit: 10,
      });

      instance.onNext(({ shown, total, hasNext, batchSize }) => {
        console.log('next batch', { shown, total, hasNext, batchSize });
      });

      const statusEl = document.getElementById('status');
      const nextBtn = document.getElementById('next-btn');

      function syncStatus() {
        statusEl.textContent =
          instance.getShownCount() + ' / ' + instance.getOfferCount() +
          ' (php: <?= (int) $offerCount ?>)';
        nextBtn.disabled = !instance.hasNext();
      }

      syncStatus();

      nextBtn.addEventListener('click', async () => {
        await instance.next();
        syncStatus();
      });
    });
  </script>
</div>