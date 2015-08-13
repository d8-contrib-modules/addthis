<?php
/**
 * @file
 * A class containing utility methods for json-related functionality.
 */

namespace Drupal\addthis\Util;

use Drupal\Component\Serialization\Json;

class AddThisJson {

  public function decode($url) {

    // Clients will only throw exceptions that are a subclass of GuzzleHttp\Exception\RequestException.
    try {
      // Create a HTTP client.
      $response = \Drupal::httpClient()
        ->get($url)
        ->getBody(TRUE);
    }
    catch (RequestException $e) {
      // Do some stuff in case of the error.
    }

    // If successful HTTP query.
    if ($response) {
      $data = $response->getContents();
      return Json::decode($data);
    }
    else {
      return NULL;
    }

  }
}
