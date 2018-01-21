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
use AppBundle\Entity\Education;
use FOS\RestBundle\View\View;


class EducationApiController extends Controller
{
    /**
     * @Rest\Get("/api/v0.1/education")
     */
    public function education(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$em     	= $this->getDoctrine()->getManager();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
        $serializer = $common->getSerializer();
		if($checkToken['verify'] == 1){
			$result['data'] = $common->getAllEntity('user',$checkToken['user']->getId(), $this->getDoctrine()->getRepository('AppBundle:Education'));
            $result['status'] = "200";
		}else{
			$result['message'] = "user not found";
            $result['status'] = "401";
		}
		$result['send_at'] = date(DATE_ISO8601, strtotime(date("m/d/Y H:i:s")));
		$response = $serializer->serialize($result, 'json');
        return new Response($response);
    }
	
	/**
     * @Rest\Post("/api/v0.1/add/education")
     */
    public function addeducation(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$serializer = $common->getSerializer();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
		if($checkToken['verify'] == 1){
			$requestParameters = $request->request->all();
			$userObj  = $this->getDoctrine()->getRepository('AppBundle:User')->find($checkToken['user']->getId());
			$degreeObj= $common->checkEntity('id',$requestParameters['degree_id'], $this->getDoctrine()->getRepository('AppBundle:Degree'));
			$yearObj  = $common->checkEntity('id',$requestParameters['year_id'], $this->getDoctrine()->getRepository('AppBundle:Year'));
			$collegeObj	= $common->checkEntity('id',$requestParameters['college_id'], $this->getDoctrine()->getRepository('AppBundle:College'));
			$em     = $this->getDoctrine()->getManager();
			$education = new Education();
			$education->saveEducationData($requestParameters, $userObj, $degreeObj, $yearObj, $collegeObj, $em, $this);
			$result['data']     = 'Education added successfully';
            $result['status']   = "200";
		}else{
			$result['message'] = "user not found";
            $result['status'] = "401";
		}
		$result['send_at'] = date(DATE_ISO8601, strtotime(date("m/d/Y H:i:s")));
		$response = $serializer->serialize($result, 'json');
        return new Response($response);
    }
	
	/**
     * @Rest\Post("/api/v0.1/update/education")
     */
    public function updateeducation(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$serializer = $common->getSerializer();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
		if($checkToken['verify'] == 1){
			$requestParameters = $request->request->all();
			
			$degreeObj 	= array();
			$yearObj 	= array();
			$collegeObj = array();
			if(isset($requestParameters['degree_id'])){
				$degreeObj= $common->checkEntity('id',$requestParameters['degree_id'], $this->getDoctrine()->getRepository('AppBundle:Degree'));	
			}
			
			if(isset($requestParameters['year_id'])){
				$yearObj  = $common->checkEntity('id',$requestParameters['year_id'], $this->getDoctrine()->getRepository('AppBundle:Year'));	
			}
			if(isset($requestParameters['college_id'])){
				$collegeObj	= $common->checkEntity('id',$requestParameters['college_id'], $this->getDoctrine()->getRepository('AppBundle:College'));	
			}
			$eduObj	= $common->checkEntity('id',$requestParameters['id'], $this->getDoctrine()->getRepository('AppBundle:Education'));
			$em     = $this->getDoctrine()->getManager();
			$education = new Education();
			$education->updateEducationData($eduObj, $degreeObj, $yearObj, $collegeObj, $em, $this);
			$result['data']     = 'Education added successfully';
            $result['status']   = "200";
		}else{
			$result['message'] = "user not found";
            $result['status'] = "401";
		}
		$result['send_at'] = date(DATE_ISO8601, strtotime(date("m/d/Y H:i:s")));
		$response = $serializer->serialize($result, 'json');
        return new Response($response);
    }
}
