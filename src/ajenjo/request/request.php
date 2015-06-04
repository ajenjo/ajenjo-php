<?php

namespace ajenjo\request;

use \Httpful\Request;

/**
* Obtiene los datos del servidor.
*/
class request
{
  public static get ($url) {
    $request = Request::get($url)->send();

    return $request->body;
  }
}
