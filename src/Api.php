<?php
namespace App;

use SimpleXMLElement;
use SoapClient;

class Api
{
    private $code;
    private $response;
    private $request;
    private $config;
    private $client;

    public function __construct($request, $response, $config)
    {
        $this->request = $request;
        $this->response = $response;
        $this->config = $config;
        $this->client = new SoapClient($this->config->notamEndpoint);
    }

    public function prepareResponse($code)
    {
        $this->code = $code;
        $this->response->headers->set('Content-Type', 'application/json');
        $results = $this->getResults();
        $this->response->setContent(json_encode($results));
    }

    private function getResults()
    {
        $notamSet = $this->getNotamSet();
        if($notamSet->NOTAMSET->NOTAM->ItemQ !== null){
            $results = $this->notamResult($notamSet);
            $response = [
                'status' => 'OK',
                'results' => $results,
            ];
        }else{
            $response = [
                'status' => 'ERROR',
                'message' => $this->getErrorMessage(),
            ];
        }
        return $response;
    }

    private function notamResult($notamSet)
    {
        $results = [];
        foreach ($notamSet->NOTAMSET->NOTAM as $item){
            $location = (array)$item->ItemQ;
            $description = (array)$item->ItemE;
            array_push($results, ['location' => $location[0], 'description' => $description[0]]);
        }
        return $results;
    }

    private function getNotamSet()
    {
        $xmlResponse = $this->client->getNotam($this->prepareRequest());
        return new SimpleXMLElement($xmlResponse);
    }

    private function getErrorMessage()
    {
        return 'Result not found or ICAO codes is wrong';
    }

    private function prepareRequest()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <REQWX>
              <USR>'.$this->config->user.'</USR>
              <PASSWD>'.$this->getHashPassword().'</PASSWD>
              <ICAO>'.$this->code.'</ICAO>
            </REQWX>';
    }

    private function getHashPassword()
    {
        return md5($this->config->password);
    }

}