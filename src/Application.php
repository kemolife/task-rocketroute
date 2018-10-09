<?php

namespace App;

use SoapClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application
{
    public $request;
    public $response;
    private $configObj;

    public function __construct($config)
    {
        $this->configObj = new \stdClass();
        $this->request = Request::createFromGlobals();
        $this->response = new Response();
        $this->init($config);
    }

    private function init(&$config)
    {
        $this->setContainer($config);
        unset($config);
    }

    private function setContainer($config)
    {
        foreach ($config as $key => $value)
        {
            $this->configObj->$key = $value;
        }
    }

    public function run()
    {
        $index = $this->request->get('index');
        $code = $this->request->get('code');
        if($index === 'main'){
            $html = file_get_contents('index.html');
            $this->response->setContent($html);
            $this->response->setStatusCode(Response::HTTP_OK);
            $this->response->headers->set('Content-Type', 'text/html');
        }elseif($index === 'api' && $code){
            $api = new NotamApi($this->request, $this->response, $this->configObj);
            $api->prepareResponse($code);
        }

        $this->response->send();
    }
}