<?php
  include(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php');
  define('APP_ROOT', realpath(__DIR__));
  // define('TMP_DIR', realpath(APP_ROOT . '/../tmp/'));
  define('TMP_DIR', realpath(APP_ROOT . '/tmp/'));


  $biLeadForm = new \BinixoLib\LeadForm();


  $biLeadForm->selector = '#form';
  $biLeadForm->url = 'https://ua.binixocrm.com/v2/form/contact';
  $biLeadForm->tpl = 1;
  $biLeadForm->userAgreement = 'https://fastcredit.net.ua/privacy-policy/';
  $biLeadForm->redirectUrl = 'https://t1ny.io';
  $biLeadForm->lang = 'en';
?>

<!doctype html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Binixo.KZ</title>

    <base href="/"/>
    <meta http-equiv=Content-Security-Policy content="default-src 'self' https: http: ws: wss: 'unsafe-eval' 'unsafe-inline'; img-src 'self' data: http: https:; connect-src ws: wss: https: http:">

    <?php print $biLeadForm->fetchCss();?>
</head>
<body>

<div class="loan-container">
<?php
try {

  $biLeadForm->render();
} catch (\BinixoLib\Exceptions\Http $e) {
  var_dump($e);
  exit;
}

?>
</div>
Hello


</body>
