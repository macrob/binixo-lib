<?php

/**
 * Splits offers into first N and the rest for sequential rendering.
 * Usage: {{#firstRest offers count=3}} ... {{/firstRest}}
 * Context: { first: [...], rest: [...] }
 */
return function ($items, $options) {
  $count = isset($options['hash']['count']) ? (int) $options['hash']['count'] : 3;
  $list = $items ?: [];

  return $options['fn']([
    'first' => array_slice($list, 0, $count),
    'rest' => array_slice($list, $count),
  ]);
};
