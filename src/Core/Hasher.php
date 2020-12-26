<?php

namespace AspectOverride\Core;

class Hasher
{
  public function getHash(string $content, ?string $prefix = null)
  {
    $hash = hash('sha256', $content);
    if($prefix) {
      $hash = $prefix . '-' . $hash;
    }
    return $hash;
  }
}