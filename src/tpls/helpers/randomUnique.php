<?php

/**
 * Picks one value at random without repeats within a single template render.
 * When the pool is exhausted, reshuffles and starts over.
 *
 * Usage: {{randomUnique a b c}}
 *
 * Note: pools are kept in a static (not @root) because LightnCandy
 * restores sp_vars inside {{#each}}, so root mutations do not persist.
 */
return function () {
  static $pools = [];

  $args = func_get_args();
  $items = array_values(array_filter(array_slice($args, 0, -1), function ($v) {
    return $v !== null && $v !== '';
  }));

  if (!count($items)) {
    return '';
  }

  $key = implode("\0", $items);

  if (!isset($pools[$key]) || count($pools[$key]) === 0) {
    $pool = $items;
    shuffle($pool);
    $pools[$key] = $pool;
  }

  return array_shift($pools[$key]);
};
