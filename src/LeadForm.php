<?php
namespace BinixoLib;

class LeadForm {

  public $scriptFormJs = 'https://cdn.binixocrm.com/js/v1/form-p0.0.1.js';
  public $cssFormTemplate = 'https://cdn.binixocrm.com/leadform/tpls/1/css/main.css';

  public $selector;

  /* api url */
  public $url;

  /* succes url for redirect */
  public $successUrl;
  public $successCrezuLeadUrl;

  public $tpl;
  public $userAgreement;
  // public $redirectUrl;
  // public $addUtmCampaign;
  public $task;
  // public $options;
  public $lang;
  public $mask;
  // NOT USED public $onsubmit;

  public function render() {
    print $this->fetch();
  }

  public function getScriptFormJs() {
    return '<script src="'.$this->scriptFormJs.'"></script>';
  }

  public function getCssFormTemplate() {
    return '<link rel="stylesheet" href="'.$this->cssFormTemplate.'">';
  }

  public function getLeadformJsOption() {

    $params = [
      'selector' => $this->selector,
      'url' => $this->url,
      'lang' => $this->lang,
      'successUrl' => $this->successUrl,
      'task' => $this->task,
      'mask' => $this->mask
    ];

    if ($this->successCrezuLeadUrl) {
      $params['successCrezuLeadUrl'] = $this->successCrezuLeadUrl;
    }
    return json_encode($params);
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

    print $template->fetch($isMob);
  }
}
