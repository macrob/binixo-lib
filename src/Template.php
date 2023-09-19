<?php
namespace BinixoLib;

use \LightnCandy\LightnCandy;

class Template
{
  public $tpl = '1';

  /* for leadform default.hbs */
  public $tplSub;
  public $lang = 'ru';
  public $currency;
  // offerwall || leadform
  public $type = 'offerwall';

  private function getI18n($lang)
  {
    $langFilename = $lang .'.json';

    $cdnFile = new Cdn(
      join('/', ['lang', $langFilename]),
      $this->type
    );

    return json_decode($cdnFile->get(), true);   
  }

  private function getCurrency() {
    $cdnFile = new Cdn('cur.json', $this->type);

    $currencies = json_decode($cdnFile->get(), true); 
    return isset($currencies[$this->currency]) ? $currencies[$this->currency] : null;
  }

  private function getOfferwallTemplate()
  {
    $template = $this->tpl;

    $tplDesktop = new Cdn(
      join('/', ['tpls', $template, 'tpl.v3.hbs']),
      $this->type
    );

    $tplMob = new Cdn(
      join('/', ['tpls', $template, 'tpl.v3mob.hbs']),
      $this->type
    );

    $std = new \stdClass();
    $std->desktop = $tplDesktop->get();
    $std->mob = $tplMob->get();

    return $std;
  }

  private function getLeadformTemplate()
  {

    $tpl = new Cdn(
      join('/', ['tpls', $this->tpl, $this->tplSub]),
      $this->type
    );

    $std = new \stdClass();
    $std->tpl = $tpl->get();

    return $std;
  }

  private function downloadPartials() {
    $template = $this->tpl;

    $partials = [];

    $partials[] = 'itemCode.hbs';
    $partials[] = 'itemMfo.hbs';

    foreach($partials as $item) {

      $cdn = new Cdn(
        join('/', ['tpls', $template, 'partials', $item])
      );

      $cdn->download();
    }
  }

  private function getPartials() {
    $template = $this->tpl;

    $cache = new Cache(
      $this->type.'_'.join(DIRECTORY_SEPARATOR, ['tpls', $template, 'partials'])
    );
    
    if (!$cache->isExist()) {
      $this->downloadPartials($template);
    }

    $rs = [];

    $files = scandir($cache->getPath());
    $files = array_filter($files, function ($var) {
      return fnmatch('*.hbs', $var);
    });

    foreach ($files as $item) {
      $fileName = join(DIRECTORY_SEPARATOR, [$cache->getPath(), $item]);
      $info = pathinfo($fileName);
      $rs[$info['filename']] = file_get_contents($fileName);
    }

    return $rs;
  }

  private function getHelpers() {
    $rs = [];

    $dir = join(DIRECTORY_SEPARATOR, [__DIR__, 'tpls', 'helpers']);

    $files = scandir($dir);
    $files = array_filter($files, function ($var) {
      return fnmatch('*.php', $var);
    });

    foreach ($files as $item) {
      $fileName = $dir . DIRECTORY_SEPARATOR . $item;
      $info = pathinfo($fileName);
      $rs[$info['filename']] = include($fileName);
    }

    return $rs;
  }


  private function compile() {
    $flags = LightnCandy::FLAG_BESTPERFORMANCE | LightnCandy::FLAG_HANDLEBARSJS;
    // LightnCandy::FLAG_RENDER_DEBUG | LightnCandy::FLAG_HANDLEBARSJS | LightnCandy::FLAG_ERROR_LOG,
    
    $tpl = $this->getOfferwallTemplate();
    $partials = $this->getPartials();
    $helpers = $this->getHelpers();

    $tplMob = LightnCandy::compile($tpl->mob, array(
      'flags' => $flags,
      
      'helpers' => $helpers,
      'partials' => $partials
    ));

    $tplDesktop = LightnCandy::compile($tpl->desktop, array(
      'flags' => $flags,
      'helpers' => $helpers,
      'partials' => $partials
    ));

    $cacheMob = new Cache('render-mob.php');
    $cacheMob->save('<?php ' . $tplMob . '?>');

    $cacheDesktop = new Cache('render-desktop.php');
    $cacheDesktop->save('<?php ' . $tplDesktop . '?>');
  }

  private function getLeadformCachename() {
    return 'render-leadform-'.$this->tpl.'-'.$this->tplSub.'.php';
  }

  private function compileLeadform() {
    $flags = LightnCandy::FLAG_BESTPERFORMANCE | LightnCandy::FLAG_HANDLEBARSJS;
    // LightnCandy::FLAG_RENDER_DEBUG | LightnCandy::FLAG_HANDLEBARSJS | LightnCandy::FLAG_ERROR_LOG,
    
    $tpl = $this->getLeadformTemplate();
    $helpers = $this->getHelpers();

    
    $tplDefault = LightnCandy::compile($tpl->tpl, array(
      'flags' => $flags,
      'helpers' => $helpers,
    ));

    $cache = new Cache($this->getLeadformCachename());
    $cache->save('<?php ' . $tplDefault . '?>');

  }

  private function fetchOfferwallDesktop($offers) {    
    $this->compile();
    
    $cache = new Cache('render-desktop.php');
    $renderer = include($cache->getPath());

    return $renderer(array(
      'offers' => $offers,
      'i18n' => $this->getI18n(
        $this->lang
      ),
      'currency' => $this->getCurrency()
    ));
  }

  private function fetchOfferwallMob($offers) {
    $this->compile();
    
    $cache = new Cache('render-mob.php');
    $renderer = include($cache->getPath());
    
    return $renderer(array(
      'offers' => $offers,
      'i18n' => $this->getI18n(
        $this->lang
      ),
      'currency' => $this->getCurrency()
    ));
  }

  private function fetchLeadform() {
    $this->compileLeadform();
    
    $cache = new Cache($this->getLeadformCachename());
    $renderer = include($cache->getPath());
  
    $form = $renderer(array(
      'i18n' => $this->getI18n(
        $this->lang
      )
    ));

    return $form;
  }

  private function fetchOfferwall($isMob, $offers) {
    $content = $isMob ? $this->fetchOfferwallMob($offers) : $this->fetchOfferwallDesktop($offers);
    return $content;
  }

  public function fetch($isMob, $offers = []) {

    switch($this->type) {
      case 'offerwall':
        return $this->fetchOfferwall($isMob, $offers);
      break;
      case 'leadform':

        return $this->fetchLeadform();
      break;
    }
    
  }
}
