# binixo-lib

#### локальный пример (limit / Next)
```bash
./example/serve.sh
# открыть http://127.0.0.1:8080/exmpl2.php
# PORT=8090 ./example/serve.sh   # другой порт
```

#### подготовка: 
в директории лендоса SVN_REP/binixo.kz/ выполняем команду
```bash 
composer require macrob/binixo-lib
```


отркрываем www/index.php

```php
<?php include('../vendor/autoload.php'); 
define('APP_ROOT', realpath(__DIR__));
define('TMP_DIR', realpath(APP_ROOT . '/../tmp/'));
```

далеее есть 2 варианта использования 
- вариант 1, полный серверный рендер
- вариант 2, клиентский рендер с `limit` / Next
  
#### Вариант 1:
full server render (PHP печатает HTML офферов)

на страницу /offers/index.html добавлям следующий код

```php
    <div id="offerwall">
      <?php 
        $biLib = new \BinixoLib\Offerwall();

        $biLib->tpl = '1';
        $biLib->lang = 'ru';
        $biLib->currency = 'KZT';
        $biLib->cacheTtl = 60; // секунды; null — без ограничения
        $biLib->limit = 5; // опционально: только первые N офферов в HTML

        $biLib->url = 'https://kz.binixocrm.com/fd/offerwall/lender/json2?id=6193a180100734dc7cf60c01';
        $biLib->urlMob = 'https://kz.binixocrm.com/fd/offerwall/lender/json2?id=6193a1a2100734dc7cf60c2d';
        $biLib->offerwallJs = 'https://cdn.binixocrm.com/js/v1/offerwall-v2.0.2.js';
        
        $biLib->injectJs(true);
        $biLib->render();
      ?>
    </div>
```

и не забываем вставить трекинг
```html
    <script>
  window.addEventListener("load", async() => {
    tracking.doit();
  });
</script>
```

#### Вариант 2:

клиентский рендер с `limit` и кнопкой Next.
PHP **не** вызывает `render()` / `printJsonOffers*` — иначе в HTML уедет весь список.
Офферы грузит JS по `url` / `urlMob`.

```php
      <?php 
        $biLib = new \BinixoLib\Offerwall();

        $biLib->tpl = '1';
        $biLib->lang = 'ru';
        $biLib->currency = 'KZT';
        $biLib->limit = 5;

        $biLib->url = 'https://kz.binixocrm.com/fd/offerwall/lender/json2?id=6193a180100734dc7cf60c01';
        $biLib->urlMob = 'https://kz.binixocrm.com/fd/offerwall/lender/json2?id=6193a1a2100734dc7cf60c2d';
        $biLib->offerwallJs = 'https://cdn.binixocrm.com/js/v1/offerwall-v2.0.2.js';
        
        $biLib->injectJs(true);
        $biLib->printClientOptions('#offerwall'); // только опции, без офферов
      ?>
```


```html
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

    // TRACKING
    tracking.doit();
  });
</script>
```
