<?php

namespace App;


use SimpleXMLElement;
use SoapClient;

class NotamApi extends Api
{
    public function __construct($request, $response, $config)
    {
        parent::__construct($request, $response, $config);
        $this->client = new SoapClient($this->config->notamEndpoint);
    }

    protected function getResults()
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
}