<?php

/**
 * Returns a random integer between min and max (inclusive).
 * Usage: {{random 1 10}}
 */
return function ($min, $max) {
  $lo = (int) $min;
  $hi = (int) $max;

  return mt_rand($lo, $hi);
};
