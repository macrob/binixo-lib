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
  public $cookiePolicy;
  public $terms;
  public $privacyPolicy;

  // public $redirectUrl;
  // public $addUtmCampaign;
  public $task;
  // public $options;
  public $lang;
  public $mask;
  public $wizardLogo;

  private $jsParams = [];
  // NOT USED public $onsubmit;

  public function render() {
    print $this->fetch();
  }

  public function setJsParams($params) {
    $this->jsParams = $params;
  }

  public function setJsParam($key, $value) {
    $this->jsParams[$key] = $value;
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
      'mask' => $this->mask,
      'wizardLogo' => $this->wizardLogo,
      /*
      'userAgreement' => $this->userAgreement,
      'cookiePolicy' => $this->cookiePolicy,
      'terms' => $this->terms,
      'privacyPolicy' => $this->privacyPolicy,
      */
    ];

    $params = array_merge($params, $this->jsParams);

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

    $template->options = array(
      'userAgreement' => $this->userAgreement,
      'cookiePolicy' => $this->cookiePolicy,
      'terms' => $this->terms,
      'privacyPolicy' => $this->privacyPolicy,
    );

    $detect = new \Mobile_Detect();
    $isMob = $detect->isMobile();

    print $template->fetch($isMob);
  }
}
