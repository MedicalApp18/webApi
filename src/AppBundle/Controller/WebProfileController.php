<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Components\CssSelector\Parser;
use Symfony\Component\HttpFoundation\Session\Session;
use Lsw\ApiCallerBundle\Call\HttpGetJson;
use AppBundle\Service\Jwtauth;
use AppBundle\Service\Common;
use AppBundle\Service\Curl;
use AppBundle\Entity\User;
use FOS\RestBundle\View\View;


class WebProfileController extends Controller
{
	/**
     * @Route("/profile", name="user_profile")
     */
    public function profileAction(){
		$token = $this->get('session')->get('token');
		$Jwtauth    = $this->get(Jwtauth::class);
		$em     	= $this->getDoctrine()->getManager();
		$checkToken = $Jwtauth->checkToken($token, $this->getDoctrine()->getRepository('AppBundle:User'));
        if($checkToken['verify'] == 1){
			$curl   = $this->get(Curl::class);
			$profileData = $curl->curlGetData("api/v0.1/profile/details", $token);
			$data['menu_select']  = 'home';
			$data['profileData']  = $profileData['data'];
			//print'<pre>';
			//print_r($this->get('session')->get('token'));die;
			return $this->render('dashboard/profile.html.twig', $data);	
		}else{
			$uri  = $this->get('router')->generate('user_login');
			return $this->redirect($uri);
		}
    }
	/**
     * @Route("/fetch/education", name="user_education")
     */
    public function educationAction(){
		$token = $this->get('session')->get('token');
		$Jwtauth    = $this->get(Jwtauth::class);
		$em     	= $this->getDoctrine()->getManager();
		$checkToken = $Jwtauth->checkToken($token, $this->getDoctrine()->getRepository('AppBundle:User'));
        if($checkToken['verify'] == 1){
			$curl   = $this->get(Curl::class);
			$educationData = $curl->curlGetData("api/v0.1/education", $token);
			$eduData = array();
			$eduData['eduLists'] = $educationData['data'];
			$fileContent = $this->render('dashboard/education.html.twig', $eduData);
            $updateData = $fileContent->getContent();
			$responseContent['data'] = $fileContent->getContent();
			$common     = $this->get(Common::class);
			$serializer = $common->getSerializer();
			$response = $serializer->serialize($responseContent, 'json');
			return new Response($response);
		}else{
			$uri  = $this->get('router')->generate('user_login');
			return $this->redirect($uri);
		}
    }
}
