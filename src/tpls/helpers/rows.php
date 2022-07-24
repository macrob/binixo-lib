<?php

if (! function_exists('arrayChunkByItemCode')) {
  function arrayChunkByItemCode($items, $colcount = 4)
  {
    $rows = [];
    $curArr = [];
  
    foreach ($items as $item) {
      if (isset($item['code'])) {
        if (count($curArr) > 0) {
          $rows[] = $curArr;
          $curArr = [];
        }
  
        $rows[] = [$item];
      } else {
        $curArr[] = $item;
      }
  
      if (count($curArr) >= $colcount) {
        $rows[] = $curArr;
        $curArr = [];
      }
    }
  
    if (count($curArr) > 0) {
      $rows[] = $curArr;
    }
  
    return $rows;
  }
}

return function ($items, $options) {
  $out = '';
  $colcount = $options['hash']['cols'];
  $chunkedRows = arrayChunkByItemCode($items, $colcount);

  foreach ($chunkedRows as $item) {
    $out .= '<div class="ofr-row row">' . $options['fn']($item) . '</div>';
  }

  return $out;
};