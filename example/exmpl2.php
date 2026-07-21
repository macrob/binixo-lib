<?php

include(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php');


define('CRM2_ENDPOINT_V3', 'https://binixo.mx/s3');
define('OFFERWALL_V3', '6699019ca45acda8280739a4');
define('OFFERWALL_MOBILE_V3', '6699087ea45acda8280739a5');  // advert
define('LIB_JS_OFFERWALL_V2', 'https://cdn.binixocrm.com/js/v1/offerwall-v2.0.2.js');


define('APP_ROOT', realpath(__DIR__));
// define('TMP_DIR', realpath(APP_ROOT . '/../tmp/'));
define('TMP_DIR', realpath(APP_ROOT . '/tmp/'));

$biLib = new \BinixoLib\Offerwall();

$biLib->tpl = '3';
$biLib->lang = 'es';
$biLib->currency = 'MXN';
$biLib->limit = 5;

$biLib->url = CRM2_ENDPOINT_V3 . '/offers/' . OFFERWALL_V3;
$biLib->urlMob = CRM2_ENDPOINT_V3 . '/offers/' . OFFERWALL_MOBILE_V3;
$biLib->offerwallJs = LIB_JS_OFFERWALL_V2;

// PHP не рендерит и не дампит офферы — только JS + опции
$biLib->injectJs(true);
$biLib->printClientOptions('#offerwall');

?>
  <div id="offerwall"></div>
  <button id="next-btn" type="button">Next</button>
  <span id="status"></span>
  <script>
    window.addEventListener("load", async() => {
      const instance = new ofr.Offerwall(offerwallOptions);

      await instance.render();

      const statusEl = document.getElementById('status');
      const nextBtn = document.getElementById('next-btn');

      function syncStatus() {
        statusEl.textContent =
          instance.getShownCount() + ' / ' + instance.getTotalCount();
        nextBtn.disabled = !instance.hasNext();
      }

      syncStatus();

      nextBtn.addEventListener('click', async () => {
        await instance.next();
        syncStatus();
      });
    });
  </script>
