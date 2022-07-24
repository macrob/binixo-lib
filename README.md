# binixo-lib


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
- вариант 2, частичный
  
#### Вариант 1:
full server render

на страницу /offers/index.html добавлям следующий код

```php
    <div id="offerwall">
      <?php 
        $biLib = new \BinixoLib\Offerwall();

        $biLib->tpl = '1';
        $biLib->lang = 'ru';
        $biLib->currency = 'KZT';

        $biLib->url = 'https://kz.binixocrm.com/fd/offerwall/lender/json2?id=6193a180100734dc7cf60c01';
        $biLib->urlMob = 'https://kz.binixocrm.com/fd/offerwall/lender/json2?id=6193a1a2100734dc7cf60c2d';
        $biLib->offerwallJs = 'https://cdn.binixocrm.com/js/v1/offerwall-0.0.1.js';
        
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

на страницу /offers/index.html добавлям следующий код

```php
      <?php 
        $biLib = new \BinixoLib\Offerwall();

        $biLib->url = 'https://kz.binixocrm.com/fd/offerwall/lender/json2?id=6193a180100734dc7cf60c01';
        $biLib->urlMob = 'https://kz.binixocrm.com/fd/offerwall/lender/json2?id=6193a1a2100734dc7cf60c2d';
        $biLib->offerwallJs = 'https://cdn.binixocrm.com/js/v1/offerwall-0.0.1.js';
        
        $biLib->injectJs(true);
        $biLib->printJsonOffersMob('offersMob');
        $biLib->printJsonOffersDesktop('offersDesk');
      ?>
```


```html
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

  // TRACKING 
  tracking.doit();
</script>
```