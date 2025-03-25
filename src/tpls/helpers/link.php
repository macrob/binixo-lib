<?php

return function ($options) {
  $content = $options['fn']($this);
  
  foreach ($options['hash'] as $key => $value) {
    // Приводим $value к строке, если он null
    $safeValue = $value ?? '';
    $content = str_replace(strtoupper($key), $safeValue, $content);
  }

  return $content;
};