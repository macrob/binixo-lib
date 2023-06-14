<?php

return function ($options) {
  return str_replace('HREF', $options['hash']['href'], $options['fn']($this));
  // return 'test';
};