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

  public function __construct($config){
    $config = (Object) $config;

    if (!$config->mode) {
      $config->mode = getenv('AJENJO_CLI_MODE') ? getenv('AJENJO_CLI_MODE') : "production";
    }

    if (!$config->mode_status) {
      $config->mode_status = getenv('AJENJO_CLI_MODE_STATUS') ? getenv('AJENJO_CLI_MODE_STATUS') : 'online';
    }

    // Put mode status
    switch ($config->mode) {
      case 'develop':
      case 'dev':
      case 'deve':
        $config->mode = 'develop';
        break;

      case 'dem':
      case 'demo':
      case 'demostration':
      case 'test':
      case 'exp':
      case 'experiment':
        $config->mode = 'demo';
        break;

      default:
        $config->mode = 'production';
        break;
    }

    switch ($config->mode_status) {
      case 'off':
      case 'offline':
        $config->mode_status = "offline";
        break;

      case 'on':
      case 'online':
      default:
        $config->mode_status = "online";
        break;
    }

    if (!$config->URLConnect) {
      $config->URLConnect = getenv('AJENJO_CLI_URL_CONNECT') ? getenv('AJENJO_CLI_URL_CONNECT') : 'http:///';
    }

    if (!$config->URLlocal) {
      $config->URLlocal = getenv('AJENJO_CLI_URL_CONNECT_LOCAL') ? getenv('AJENJO_CLI_URL_CONNECT_LOCAL') : $config->URLConnect;
    }

    if (!$config->token) {
      $config->token = getenv('AJENJO_CLI_TOKEN') ? getenv('AJENJO_CLI_TOKEN') : null;
    }

    if (!$config->localURL) {
      $config->localURL = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }


    // var_dump($config);
    // exit();


    $this->data_cookie      = new cookie();
    $this->data_session     = new stdClass;
    $this->key_token        = $config->token;
    $this->localURL         = $config->localURL;
    $this->mode             = $config->mode;
    $this->mode_status      = $config->mode_status;
    $this->url_ajenjo       = $config->URLConnect;
    $this->url_ajenjo_local = $config->URLlocal;
    $this->urls             = new stdClass;

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

    if ($cookieObtenido) {
      $this->setSesionKeyOfCookie($cookieObtenido);
    }

    $this->refreshURLs();

    return $this;
  }

  public function send() {
    $this->getSesionKeyOfCookie();

    $reqMp = Request::get($this->urls->status);

    if ($this->data_cookie) {
      $reqMp = $reqMp->addHeader('Cookie',$this->data_cookie->toStringOnlyCookie());
    }

    // Send message
    if ($this->mode == "production") {
      $req = $reqMp->send();
    } else {
      if ($this->mode_status == 'online') {
        $req = (Object) [
          "body" => [
            "login" => true,
            "momory" => [
                "sesionactive" => true
            ],
            "user" => [
                "groups" => [
                    [
                        "name" => "ADMIN",
                        "title" => "ADMIN",
                        "description" => null,
                    ]
                ],
                "permissions" => [],
                "user" => "adm",
                "name" => "admin",
                "lastname" => null,
                "secondLastName" => null,
                "email" => "em@il.com",
                "status" => true,
                "other" => [],
            ],
          ],
          "headers" => [],
        ];
      } else {
        $req = (Object) [
          "body" => [
            "login" => false,
          ],
          "headers" => [
            "set-cookie" => "ajenjo.session=s%3A0000.0000; Path=/; Expires=Tue, 14 Jul 2015 04:14:31 GMT; HttpOnly",
          ],
        ];
      }
    }

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
