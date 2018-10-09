<?php
namespace App;

abstract class Api
{
    protected $code;
    protected $response;
    protected $request;
    protected $config;
    protected $client;

    public function __construct($request, $response, $config)
    {
        $this->request = $request;
        $this->response = $response;
        $this->config = $config;
    }

    public function prepareResponse($code)
    {
        $this->code = $code;
        $this->response->headers->set('Content-Type', 'application/json');
        $results = $this->getResults();
        $this->response->setContent(json_encode($results));
    }

    protected function getHashPassword()
    {
        return md5($this->config->password);
    }

    abstract protected function getResults();
}