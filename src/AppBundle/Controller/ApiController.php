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
use Lsw\ApiCallerBundle\Call\HttpGetJson;
use AppBundle\Service\Jwtauth;
use AppBundle\Service\Common;
use AppBundle\Entity\User;
use FOS\RestBundle\View\View;


class ApiController extends Controller
{
    /**
     * @Rest\Get("/api/v0.1/list/{table}")
     */
    public function fetchTableList(Request $request, $table){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$em     	= $this->getDoctrine()->getManager();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
        $serializer = $common->getSerializer();
		if($checkToken['verify'] == 1){
			$result['data']     = $common->getTableList($table, $em);
			$result['status']   = "200";	
		}elseif($checkToken['verify'] == 2){
			$result['message'] = "Token is expired";
            $result['status'] = "403";
		}else{
			$result['message'] = "user not found";
            $result['status'] = "404";
		}
		$result['send_at'] = date(DATE_ISO8601, strtotime(date("m/d/Y H:i:s")));
		$response = $serializer->serialize($result, 'json');
        return new Response($response);
    }
	
	/**
     * @Rest\Post("/api/v0.1/login")
     */
    public function login(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$serializer = $common->getSerializer();
		$username 	= $request->get('username');
		$password 	= $request->get('password');
		$otp 	  	= $request->get('otp');
		$em     	= $this->getDoctrine()->getManager();
		$userArray  = $common->checkCredentials(trim($request->get('username')),'',$em);
		if(count($userArray)===1 && !empty($userArray)){
			if(empty($otp)){
				$isValid = $this->get('security.password_encoder')
				->isPasswordValid($userArray, $password);
				if($isValid){
					$result['message'] = 'User authenticate successfully';
					$result['token']   = $Jwtauth->createToken($userArray->getId(), $userArray->getMobileNumber());
					$result['status']  = "200";
				}else{
					$result['message'] 	= "password is incorrect.";
					$result['status'] 	= "205";
				}
			}else{
				$checkOtp = $common->checkOTP($userArray->getId(), $otp, $em);
				if($checkOtp){
					$result['message'] = 'User authenticate successfully';
					$result['token']   = $Jwtauth->createToken($userArray->getId(), $userArray->getMobileNumber());
					$result['status']  = "200";
				}else{
					$result['message'] 	= "OTP is incorrect.";
					$result['status'] 	= "205";	
				}
			}
		}else{
			$result['message'] 	= "user not found.";
			$result['status'] 	= "201";
		}
		
		$result['send_at'] = date(DATE_ISO8601, strtotime(date("m/d/Y H:i:s")));
		$response = $serializer->serialize($result, 'json');
        return new Response($response);
    }
	
	/**
     * @Rest\Post("/api/v0.1/register")
     */
    public function register(Request $request){
		$common     = $this->get(Common::class);
		$requestParameters  = $request->request->all();
		$mobileCheck = array('mobileNumber' => $requestParameters['mobileNumber']);
		$emailCheck = array('email' => $requestParameters['email']);
		$userRepositry = $this->getDoctrine()->getRepository('AppBundle:User');
		if($common->checkUserExist($mobileCheck, $userRepositry) && $common->checkUserExist($emailCheck, $userRepositry)){
			$user = new User();
			$user->setFullName($requestParameters['fullName']);
			$user->setMobileNumber($requestParameters['mobileNumber']);
			$user->setEnabled(1);
			$user->setEmail($requestParameters['email']);
			$user->setUsername($requestParameters['email']);
			$user->setDateCreated(new \DateTime("now"));
			$user->setDateModified(new \DateTime("now"));
			$factory = $this->get('security.encoder_factory');
			$encoder = $factory->getEncoder($user);
			$password = $encoder->encodePassword($requestParameters['password'], $user->getSalt());
			$user->setPassword($password);
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
			$token = $common->saveOTP($user, $em);
			$body  		= "Please use ".$token." to login";
			$mailer  	= $this->get('mailer');
			$transport 	= $this->get('swiftmailer.transport.real');
			$senderArray['email']   = $user->getEmail();
			$senderArray['name']    = $user->getFullName();
			$common->sendMail($mailer, $transport, $senderArray, 'OTP for login', $body);
			$result['message'] = 'User register successfuly.';
			$result['status']  = "200";
		}else{
			$result['message'] = "user already registered.";
			$result['status']  = "201";
		}
		$serializer = $common->getSerializer();
		$result['send_at'] = date(DATE_ISO8601, strtotime(date("m/d/Y H:i:s")));
		$response = $serializer->serialize($result, 'json');
        return new Response($response);
    }
	/**
     * @Rest\Post("/api/v0.1/send/otp")
     */
    public function sendotp(Request $request){
		$common 	= $this->get(Common::class);
		$serializer = $common->getSerializer();
		$em     	= $this->getDoctrine()->getManager();
		$userArray  = $common->checkCredentials(trim($request->get('username')),'',$em);
		if(count($userArray)>0){
			$senderArray['email']   = $userArray->getEmail();
			$senderArray['name']    = $userArray->getFullName();
            $token = $common->saveOTP($userArray, $em);
			$body  		= "Please use ".$token." to login";
			$mailer  	= $this->get('mailer');
			$transport 	= $this->get('swiftmailer.transport.real');
			$common->sendMail($mailer, $transport, $senderArray, 'OTP for login', $body);
            $result['message']  = 'OTP send successfully';
            $result['status'] 	= "200";
		}else{
			$result['message'] 	= "user not found.";
			$result['status'] 	= "201";
		}
		$serializer = $common->getSerializer();
		$result['send_at'] = date(DATE_ISO8601, strtotime(date("m/d/Y H:i:s")));
		$response = $serializer->serialize($result, 'json');
        return new Response($response);
    }
}
