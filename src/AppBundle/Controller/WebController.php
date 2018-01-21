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


class WebController extends Controller
{
    /**
     * @Route("/user/login", name="user_login")
     */
    public function loginAction()
    {
		$token = $this->get('session')->get('token');
		if(!empty($token)){
			$Jwtauth    = $this->get(Jwtauth::class);
			$em     	= $this->getDoctrine()->getManager();
			$checkToken = $Jwtauth->checkToken($token, $this->getDoctrine()->getRepository('AppBundle:User'));
			if($checkToken['verify'] == 1){
				$uri  = $this->get('router')->generate('dashboard');
				return $this->redirect($uri);
			}	
		}else{
			return $this->render('default/login.html.twig');	
		}
    }
	
	/**
     * @Route("/set/token/{token}", name="settoken")
     */
    public function settokenAction($token)
    {
		$Jwtauth    = $this->get(Jwtauth::class);
		$em     	= $this->getDoctrine()->getManager();
		$checkToken = $Jwtauth->checkToken($token, $this->getDoctrine()->getRepository('AppBundle:User'));
		$curl    	= $this->get(Curl::class);
		$cityData 	= $curl->curlGetData("api/v0.1/list/city", $token);
		$specializationData 	= $curl->curlGetData("api/v0.1/list/specialization", $token);
		$collegeData= $curl->curlGetData("api/v0.1/list/college", $token);
		$degreeData = $curl->curlGetData("api/v0.1/list/degree", $token);
		$eduYearData= $curl->curlGetData("api/v0.1/list/edu_year", $token);
		$this->get('session')->set('cityArray', $cityData['data']);
		$this->get('session')->set('specializationData', $specializationData['data']);
		$this->get('session')->set('collegeData', $collegeData['data']);
		$this->get('session')->set('degreeData', $degreeData['data']);
		$this->get('session')->set('eduYearData', $eduYearData['data']);
		$this->get('session')->set('token', $token);
		$this->get('session')->set('login_user_name', $checkToken['user']->getFullName());
        $uri    = $this->get('router')->generate('dashboard');
        return $this->redirect($uri);
    }
	/**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboardAction(){
		$token 		= $this->get('session')->get('token');
		$Jwtauth    = $this->get(Jwtauth::class);
		$em     	= $this->getDoctrine()->getManager();
		$checkToken = $Jwtauth->checkToken($token, $this->getDoctrine()->getRepository('AppBundle:User'));
		if($checkToken['verify'] == 1){
			$data['menu_select'] = 'home';
			return $this->render('dashboard/dashboard.html.twig', $data);	
		}else{
			$uri  = $this->get('router')->generate('user_login');
			return $this->redirect($uri);
		}
    }
	/**
     * @Route("/user/logout", name="user_logout")
     */
    public function userlogoutAction()
    {
		$this->get('session')->clear();
        $uri    = $this->get('router')->generate('user_login');
        return $this->redirect($uri);
    }
	/**
     * @Route("/test/mail", name="test_mail")
     */
    public function sendtestmail()
    {
		echo "121212";die;
        $common 	= $this->get(Common::class);
		$senderArray['email']   = 'ranjan.wind88@gmail.com';
		$senderArray['name']    = 'Rakesh Ranjan';
        $body  		= "Please use to login";
        $mailer  	= $this->get('mailer');
		$transport 	= $this->get('swiftmailer.transport.real');
		$common->sendMail($mailer, $transport, $senderArray, 'OTP for login', $body);
        echo "Test Mail";die;
    }
}
