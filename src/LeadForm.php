<?php
namespace BinixoLib;

class LeadForm {
  public $selector;
  public $url;
  public $successUrl;
  public $tpl;
  public $userAgreement;
  public $redirectUrl;
  public $addUtmCampaign;
  public $task;
  public $options;
  public $lang;
  public $mask;
  public $onsubmit;

  public function render() {
    print $this->fetch();
      // Реализация метода render
      // В этом методе можно использовать свойства класса для выполнения требуемой логики
      // Например: $this->selector, $this->url, $this->successUrl и другие
  }


  public function renderJS() {

    print '<script src="https://cdn.binixocrm.com/js/v1/form-p0.0.1.js"></script>';

    ?>
      <script>
        window.addEventListener("load", async () => {

          leadform.render({
            selector: '<?=$this->selector?>',
            url: '<?=$this->url?>',
            lang: '<?=$this->lang?>',
            successUrl: 'https://myurlgroup1.com?short=[shortId]',
            options: {
              loanDays: 5,
              loanAmount: 15000,
            },
            task: ['zerobounce']
          });

        });
      </script>
    <?php

  }

  public function fetchCss() {
    return '<link rel="stylesheet" href="https://cdn.binixocrm.com/leadform/tpls/1/css/main.css">';
  }
  
  public function fetch()
  {
    $template = new Template();
    $template->type = 'leadform';

    $template->tpl = $this->tpl;
    $template->tplSub = 'default.hbs';

    $template->lang = $this->lang;


    $detect = new \Mobile_Detect();

    $isMob = $detect->isMobile();
    

    /*
    $md5 = md5 ($_SERVER['REQUEST_URI']);
    $cacheName = $isMob ? $md5.'_leadform-mob.php' : $md5.'_leadform-desktop.php';
    $cache = new Cache($cacheName);

    if (!$cache->isExist()) {
      $offers = $isMob ? $this->getOffersMob() : $this->getOffersDesktop();
      $content = $template->fetch($isMob, $offers);
      $cache->save($content);
    }

    return $cache->get();
    */

    print '<div id="'.$this->selector.'">';
    print $template->fetch($isMob);
    print '</div>';
    $this->renderJS();
    

    
  }
}
