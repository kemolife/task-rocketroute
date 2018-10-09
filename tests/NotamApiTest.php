<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotamApiTest extends TestCase
{

    public $request;
    public $response;
    public $config;

    protected function setUp()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->configMock();
    }

    public function testGetNotamSet()
    {
        $obj = new \App\NotamApi($this->request, $this->response, $this->config);
        $notamSet = self::callMethod($obj, 'getNotamSet', []);
        $this->assertInstanceOf('SimpleXMLElement', $notamSet);
    }

    private function configMock()
    {
        $this->config = new \StdClass();
        $this->config->notamEndpoint = 'https://apidev.rocketroute.com/notam/v1/service.wsdl';
        $this->config->user = 'test@gmail.com';
        $this->config->password = '12345';
        return $this->config;
    }

    public static function callMethod($obj, $name, array $args)
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

}