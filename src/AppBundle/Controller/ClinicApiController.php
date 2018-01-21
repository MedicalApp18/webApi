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
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Components\CssSelector\Parser;
use Lsw\ApiCallerBundle\Call\HttpGetJson;
use AppBundle\Service\Jwtauth;
use AppBundle\Service\Common;
use AppBundle\Entity\User;
use AppBundle\Entity\Clinic;
use FOS\RestBundle\View\View;


class ClinicApiController extends Controller
{
    /**
     * @Rest\Post("/api/v0.1/add/clinic")
     */
    public function addclinic(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$em     	= $this->getDoctrine()->getManager();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
        $serializer = $common->getSerializer();
		if($checkToken['verify'] == 1){
			$requestParameters = $request->request->all();
			$userObj = $this->getDoctrine()->getRepository('AppBundle:User')->find($checkToken['user']->getId());
			$clinic = new Clinic();
			$clinic->saveClinic($requestParameters, $userObj, $em);
			$result['message'] = "clinic added successfully.";
            $result['status']   = "200";
		}else{
			$result['message'] = "user not found";
            $result['status'] = "201";
		}
		$result['send_at'] = date(DATE_ISO8601, strtotime(date("m/d/Y H:i:s")));
		$response = $serializer->serialize($result, 'json');
        return new Response($response);
    }
	
	/**
     * @Rest\Get("/api/v0.1/get/clinics")
     */
    public function getclinics(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$serializer = $common->getSerializer();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
		if($checkToken['verify'] == 1){
			$requestParameters = $request->request->all();
			$userObj = $this->getDoctrine()->getRepository('AppBundle:User')->find($checkToken['user']->getId());
			$result['data']     = $userObj->getClinics();
            $result['status']   = "200";
		}else{
			$result['message'] = "user not found";
            $result['status'] = "201";
		}
		$result['send_at'] = date(DATE_ISO8601, strtotime(date("m/d/Y H:i:s")));
		$response = $serializer->serialize($result, 'json');
        return new Response($response);
    }
	
	/**
     * @Rest\Get("/api/v0.1/clinic/detail/{id}")
     */
    public function clinicdetails(Request $request, $id){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$serializer = $common->getSerializer();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
		if($checkToken['verify'] == 1){
			$em      = $this->getDoctrine()->getManager();
			$clinic = new Clinic();
			$result['data'] = $clinic->getClinicDetails($checkToken['user']->getId(), $id, $em);
			$result['status']   = "200";
			
		}else{
			$result['message'] = "user not found";
            $result['status'] = "201";
		}
		$result['send_at'] = date(DATE_ISO8601, strtotime(date("m/d/Y H:i:s")));
		$response = $serializer->serialize($result, 'json');
        return new Response($response);
    }
}
