<?php
namespace BinixoLib;

use \LightnCandy\LightnCandy;

class Template
{
  public $tpl = '1';
  public $lang = 'ru';

  public function getI18n($lang)
  {
    $langFilename = $lang .'.json';

    $cdnFile = new Cdn(
      join(DIRECTORY_SEPARATOR, ['lang', $langFilename])
    );

    return json_decode($cdnFile->get(), true);   
  }

  private function getTemplate()
  {
    $template = $this->tpl;

    $tplDesktop = new Cdn(
      join('/', ['tpls', $template, 'tpl.v3.hbs'])
    );

    $tplMob = new Cdn(
      join('/', ['tpls', $template, 'tpl.v3mob.hbs'])
    );

    $std = new \stdClass();
    $std->desktop = $tplDesktop->get();
    $std->mob = $tplMob->get();

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
      join(DIRECTORY_SEPARATOR, ['tpls', $template, 'partials'])
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
    
    $tpl = $this->getTemplate();
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

  public function fetchDesktop($offers) {    
    $this->compile();
    
    $cache = new Cache('render-desktop.php');
    $renderer = include($cache->getPath());

    return $renderer(array(
      'offers' => $offers,
      'i18n' => $this->getI18n(
        $this->lang
      )
    ));
  }

  public function fetchMob($offers) {
    $this->compile();
    
    $cache = new Cache('render-mob.php');
    $renderer = include($cache->getPath());
    
    return $renderer(array(
      'offers' => $offers,
      'i18n' => $this->getI18n(
        $this->lang
      )
    ));
  }

  public function fetch($isMob, $offers) {
    $content = $isMob ? $this->fetchMob($offers) : $this->fetchDesktop($offers);
    return $content;
  }
}
