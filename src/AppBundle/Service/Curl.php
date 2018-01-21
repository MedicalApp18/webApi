<?php
namespace AppBundle\Service;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Curl{
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
	public function curlPostData($url, $postData, $token){
        $besUrl =$this->container->getParameter('api_url').$url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $besUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$token, 'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json_response  = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($json_response, true);
        return $response;
    }
	public function curlGetData($url, $token){
        $besUrl =$this->container->getParameter('api_url').$url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $besUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$token, 'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json_response  = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($json_response, true);
        return $response;
    }
	
}
