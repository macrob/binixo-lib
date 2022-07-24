<?php

return function ($items, $options) {
  $out = '';
  $rows = array_chunk($items, 4);

  foreach ($rows as $item) {
    $out .= '<div class="ofr-row row">' . $options['fn']($item) . '</div>';
  }

  return $out;
};