<?php

namespace App\Packages;

use App\Constants\EbayConstant;
use App\Constants\EbayErrorMessageConstant;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Ebay
{
    private string $environment;
    private array $returnMsg;
    private string $eBayToken;
    private Client $client;
    private string $domain;

    public function __construct()
    {
        $this->eBayToken    = env("EBAY_TOKEN");
        $this->environment  = env("EBAY_ENVIRONMENT", EbayConstant::SANDBOX);
        $this->returnMsg    = helpers_fail_message();
        $this->client       = new Client();

        if( $this->environment == EbayConstant::PRODUCTION ){
            $this->domain = "https://api.ebay.com";
        } else {
            $this->domain = "https://api.sandbox.ebay.com";
        }
    }

    public function getCategories()
    {
        $returnMsg = $this->returnMsg;
        
        try {
            $endPoint =  $this->domain . '/ws/api.dll';
            $headers = [
                'X-EBAY-API-SITEID'              => '0',
                'X-EBAY-API-COMPATIBILITY-LEVEL' => EbayConstant::API_VERSION,
                'X-EBAY-API-CALL-NAME'           => 'GetCategories',
                'Content-Type'                   => 'text/xml',
            ];

            $xml  = '<?xml version="1.0" encoding="utf-8"?>';
            $xml .= '<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $xml .= '<RequesterCredentials>';
            $xml .= '<eBayAuthToken>' . $this->eBayToken . '</eBayAuthToken>';
            $xml .= '</RequesterCredentials>';
            $xml .= '<ErrorLanguage>en_US</ErrorLanguage>';
            $xml .= '<WarningLevel>High</WarningLevel>';
            $xml .= '<DetailLevel>ReturnAll</DetailLevel>';
            $xml .= '<ViewAllNodes>true</ViewAllNodes>';
            $xml .= '<CategorySiteID>0</CategorySiteID>'; // US site
            $xml .= '</GetCategoriesRequest>';

            $response = $this->client->post($endPoint, [
                'headers' => $headers,
                'body'    => $xml
            ]);

            $result   = $response->getBody()->getContents();
            $parseArr = $this->parseXmlResponse($result);

            if( !isset($parseArr["CategoryArray"]["Category"]) ){
                throw new Exception(EbayErrorMessageConstant::getNotHaveErrorMessage("CATEGORIES"));
            }

            $returnMsg = helpers_success_message($parseArr["CategoryArray"]["Category"]);
        } catch (GuzzleException $e) {
            $returnMsg = helpers_fail_message('GuzzleException: ' . $e->getMessage());
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message('Exception: ' . $e->getMessage());
        }

        return $returnMsg;
    }

    private function parseXmlResponse($xmlString): array
    {
        $xml   = simplexml_load_string($xmlString);
        $json  = json_encode($xml);

        return json_decode($json, true);
    }
}
