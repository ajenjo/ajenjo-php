<?php
namespace ajenjo;

use \Httpful\Request;
use \cookieConverter\cookie;
use \stdClass;


/**
 * CLIENTE AJENJO
 *
 * Controla el flujo de la sesión con ajenjo.
 *
 * Abrir Sesión, Consultar Sesión Actual, Cerrar Sesión.
 */
class ajenjo {

  /**
   * URL conexión publica del servidor ajenjo.
   *
   * @example https://sessions.ajenjo/service/
   * @var string
   * @access private
   */
  private $url_ajenjo = null;

  /**
   * URL de conexión interna del servidor ajenjo.
   *
   * @var string
   * @access private
   */
  private $url_ajenjo_local = null;

  /**
   * Contiene una cadena que permite que se puede validar la comunicación con el servidor del servicio.
   *
   * @example abc123
   * @var string
   * @access private
   */
  private $key_token = null;

  /**
   * Mantiene la memoria de la sesión que se ha estado guardando constantemente.
   *
   * @var \stdClass
   **/
  public $data_session = null;

  /**
   * Mantiene la memoria de la cookie´, que mantiene la sesión.
   *
   * @var \cookieConverter\cookie
   */
  public $data_cookie = null;

  /**
   * Configuración del los request
   */
  protected $request_paths = [
    'login'       => 'login',
    'loginReturn' => 'login/r',
    'status'      => 'api/status',
  ];

  public $urls = null;

  private $localURL = "";

  protected $session_key = null;

  public $forse_send = true;

  public $name_cookie_session_key_name = "ajenjo.session";

  /**
   * @example b.session.ajenjo
   */
  public $name_session_key = "b_session_ajenjo";

  public function __construct($URL = null, $token = null, $localURL = null, $URLlocal = null) {

    if ($URL == null) {
      $URL = isset(getenv('AJENJO_CLI_URL')) ? getenv('AJENJO_CLI_URL') : 'http:///';
    }

    if ($URLlocal == null) {
      $URLlocal = isset(getenv('AJENJO_CLI_URL_LOCAL')) ? getenv('AJENJO_CLI_URL_LOCAL') : $URL ;
    }

    if ($token == null) {
      $token = isset(getenv('AJENJO_CLI_TOKEN')) ? getenv('AJENJO_CLI_TOKEN') : null;
    }

    if ($localURL == null) {
      $this->localURL = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }

    $this->data_cookie = new cookie();
    $this->data_session = new stdClass;
    $this->urls = new stdClass;
    $this->url_ajenjo = $URL;
    $this->url_ajenjo_local = $URLlocal;
    $this->key_token = $token;

    $this->getSesionKeyOfCookie();
    $this->refreshURLs();
    if ($this->forse_send) {
      $this->send();
    }
  }

  /**
   * Genera las urls de consultas.
   *
   * @example $ajenjo->refreshURLs()->urls->login;
   */
  public function refreshURLs() {

    $this->urls->login = $this->url_ajenjo . $this->request_paths["loginReturn"] . "?" . implode("&", [
      implode("=", ["s",urlencode($this->getKeyAppAjenjoByCookie())]),
      implode("=", ["r",urlencode($this->localURL)]),
      ]);

    $this->urls->status = $this->url_ajenjo_local . $this->request_paths["status"];

    return $this;
  }

  private function getKeyAppAjenjoByCookie() {
    $ajenjoSessionID = "";

    if (isset($this->data_cookie->cookies[$this->name_cookie_session_key_name])) {
      $encodeFullIDCookie = urldecode($this->data_cookie->cookies[$this->name_cookie_session_key_name]);

      $re = "/s\\:(.+)?\\..+/i";
      $str = $encodeFullIDCookie;

      if (preg_match($re, $str, $matches) == 1){
        if (isset($matches[1])) {
          $ajenjoSessionID = $matches[1];
        }
      };
    }

    return $ajenjoSessionID;
  }

  /**
   * Busca si existe una sesión en el browser.
   */
  private function getSesionKeyOfCookie($name_session_key = null, $cookies = null) {
    if ($name_session_key == null) {
      $name_session_key = $this->name_session_key;
    }
    if ($cookies == null) {
      $cookies = $_COOKIE;
    }

    // var_dump($cookies);

    if (isset($cookies[$name_session_key])) {
      $this->setSesionKeyOfCookie($cookies[$name_session_key]);
    }

    return $this;
  }

  /**
   * Establece la nueva sesión.
   */
  private function setSesionKeyOfCookie($value, $name_cookie = null) {
    if ($name_cookie == null) {
      $name_cookie = $this->name_session_key;
    }

    $this->data_cookie = new cookie($value);

    \setcookie($name_cookie, $value, $this->data_cookie->expires, "/");

    return $this;
  }

  private function catchCookie($request_parameters, $time_live_session = 0) {
    $cookieObtenido = $request_parameters->headers["set-cookie"];

    if (isset($cookieObtenido)) {
      $this->setSesionKeyOfCookie($cookieObtenido);
    }

    $this->refreshURLs();

    return $this;
  }

  public function send() {
    $this->getSesionKeyOfCookie();

    $reqMp = Request::get($this->urls->status);

    if (isset($this->data_cookie)) {
      $reqMp = $reqMp->addHeader('Cookie',$this->data_cookie->toStringOnlyCookie());
    }

    $req = $reqMp->send();
    $this->catchCookie($req);
    $this->data_session = $req;

    return $this;
  }

  public function getUrl() {
    return $this->url_ajenjo;
  }

  public function getKeyToken() {
    return $this->key_token;
  }

  static public function createSession($URL = null, $token = null) {
    return new ajenjo($URL, $token);
  }

  // public function aget(){
  //   return [class_exists('Session')? 't':'f'];
  // }

}
