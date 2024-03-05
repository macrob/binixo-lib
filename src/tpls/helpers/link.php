<?php

return function ($options) {
  $content = $options['fn']($this);
  foreach($options['hash'] as  $key => $value) {
    $content = str_replace(strtoupper($key), $value, $content);
  }

  return $content;
};