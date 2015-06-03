<?php

require 'vendor/autoload.php';
require 'src/ajenjo/ajenjo.php';


use \ajenjo\ajenjo;


/**
 * Define los valores que obtiene el cliente como valores de demostración.
 */
putenv('AJENJO_CLI_CONTROLDATA=DEMO');


class ajenjoTest extends \PHPUnit_Framework_TestCase
{
  public $url = 'http://ajenjo/ajenjo';
  public $key = 'abcd';

  /**
   * Valida las variables introducidas al objecto ajenjo.
   */
  public function testConfirmPutValuesToAjenjo() {
    $sessionAjenjo = new ajenjo($this->url, $this->key);

    $this->assertEquals($sessionAjenjo->getUrl(), $this->url);
    $this->assertEquals($sessionAjenjo->getKeyToken(), $this->key);
  }

  /**
   * Prueba la conexión con el servidor que contiene el servicio de ajenjo.
   */
  public function testConnectionToAjenjoServer() {
    $sessionAjenjo = ajenjo::createSession($this->url, $this->key);

    $this->assertEquals($sessionAjenjo->getUrl(), $this->url);
    $this->assertEquals($sessionAjenjo->getKeyToken(), $this->key);
  }

  public function testCreateSessionWithValuesEnviroment() {
    putenv('AJENJO_CLI_URL='.$this->url);
    putenv('AJENJO_CLI_TOKEN='.$this->key);

    $sessionAjenjo = ajenjo::createSession();

    $this->assertEquals($sessionAjenjo->getUrl(), $this->url);
    $this->assertEquals($sessionAjenjo->getKeyToken(), $this->key);
  }

  public function testCreateSessionReturnAjenjoType() {
    $sessionAjenjo = ajenjo::createSession();

    $this->assertInstanceOf('ajenjo\ajenjo', $sessionAjenjo);
  }

  /**
   * @covers ajenjo::DefaultUrl()
   */
  public function testDefaultUrl()
  {
    putenv('AJENJO_CLI_URL=');
    putenv('AJENJO_CLI_TOKEN=');

    $sessionAjenjo = ajenjo::createSession();

    $this->assertEquals($sessionAjenjo->getUrl(), 'http:///');

    // $this->markTestIncomplete('Not yet implemented');
  }

  /**
   * @covers ajenjo\ajenjo::createSession()->getLogin()
   */
  public function testGetUrlLogin()
  {
    putenv('AJENJO_CLI_URL=');
    putenv('AJENJO_CLI_TOKEN=');

    $sessionAjenjo = ajenjo::createSession();

    $this->assertEquals($sessionAjenjo->getLogin('http://example.com/'), 'http:///login/http://example.com/');

    // $this->markTestIncomplete('Not yet implemented');
  }

  /**
   * @covers ajenjo\ajenjo::createSession()->getStatus()
   */
  public function testGetStatusSession()
  {
    putenv('AJENJO_CLI_URL=');
    putenv('AJENJO_CLI_TOKEN=');

    echo "\n\n----------------------\n\n";

    $sessionAjenjo = ajenjo::createSession();

    print_r($sessionAjenjo->getStatus());

    echo "\n----------------------\n\n";


    echo "\n\n----------------------\n\n";

    print_r($sessionAjenjo::aget());

    echo "\n----------------------\n\n";

    $this->markTestIncomplete('Not yet implemented');
  }

}
