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
use AppBundle\Entity\UserSpecialization;
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
			/*Check association with user */
			$result['data'] = $common->getAllEntity('user',$checkToken['user']->getId(), $this->getDoctrine()->getRepository('AppBundle:Education'));
			$specializationArray = $common->getUserSpecialization($checkToken['user']->getId(), $em);
			$result['specializationArray'] = explode(',',$specializationArray);
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
			//header('Content-Type: application/json; charset=utf-8');
			$requestParameters = $request->request->all();
			$userObj  = $this->getDoctrine()->getRepository('AppBundle:User')->find($checkToken['user']->getId());
			$degreeObj 	= array();
			$yearObj 	= array();
			$collegeObj = array();
			$em = $this->getDoctrine()->getManager();
			$id ='';
			if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
				$requestData = json_decode($request->getContent(), true);
				if(isset($requestData['educationArray']) && count($requestData['educationArray'])>0){
					$eduArray = $requestData['educationArray'];
					foreach($eduArray as $education){
						if(isset($education['degree_id'])){
							$degreeObj= $common->checkEntity('id',$education['degree_id'], $this->getDoctrine()->getRepository('AppBundle:Degree'));	
						}else{
							$collegeObj	= $common->checkEntity('id','314', $this->getDoctrine()->getRepository('AppBundle:College'));
						}
						if(isset($education['year_id'])){
							$yearObj  = $common->checkEntity('id',$education['year_id'], $this->getDoctrine()->getRepository('AppBundle:Year'));	
						}else{
							$yearObj  = $common->checkEntity('id','3', $this->getDoctrine()->getRepository('AppBundle:Year'));
						}
						if(isset($education['college_id'])){
							$collegeObj	= $common->checkEntity('id',$education['college_id'], $this->getDoctrine()->getRepository('AppBundle:College'));	
						}else{
							$collegeObj	= $common->checkEntity('id','314', $this->getDoctrine()->getRepository('AppBundle:College'));
						}
						$education = new Education();
						$ids = $education->saveEducationData('', $userObj, $degreeObj, $yearObj, $collegeObj, $em, $this);
						$id .=$ids.', ';
					}
				}
			}else{
				if(isset($requestParameters['degree_id'])){
					$degreeObj= $common->checkEntity('id',$requestParameters['degree_id'], $this->getDoctrine()->getRepository('AppBundle:Degree'));	
				}else{
					$collegeObj	= $common->checkEntity('id','314', $this->getDoctrine()->getRepository('AppBundle:College'));
				}
				if(isset($requestParameters['year_id'])){
					$yearObj  = $common->checkEntity('id',$requestParameters['year_id'], $this->getDoctrine()->getRepository('AppBundle:Year'));	
				}else{
					$yearObj  = $common->checkEntity('id','3', $this->getDoctrine()->getRepository('AppBundle:Year'));
				}
				if(isset($requestParameters['college_id'])){
					$collegeObj	= $common->checkEntity('id',$requestParameters['college_id'], $this->getDoctrine()->getRepository('AppBundle:College'));	
				}else{
					$collegeObj	= $common->checkEntity('id','314', $this->getDoctrine()->getRepository('AppBundle:College'));
				}
				$education = new Education();
				$id		   = $education->saveEducationData($requestParameters, $userObj, $degreeObj, $yearObj, $collegeObj, $em, $this);
			}
			
			$result['data']   = 'Education added successfully';
			$result['id']     = $id;
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
			$result['data']     = 'Education updated successfully';
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
     * @Rest\Post("/api/v0.1/add/specialization")
     */
    public function addspecialization(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$serializer = $common->getSerializer();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
		if($checkToken['verify'] == 1){
			$requestParameters = $request->request->all();
			$userObj  = $this->getDoctrine()->getRepository('AppBundle:User')->find($checkToken['user']->getId());
			$selected_specialization = $requestParameters['selected_specialization'];
			if(!empty($selected_specialization)){
				$em = $this->getDoctrine()->getManager();
				$specializationArray = explode(',', $selected_specialization);
				$repository = $this->getDoctrine()->getRepository(UserSpecialization::class);
				foreach($specializationArray as $specialization){
					$user_spec = $repository->findOneBy([
						'user' => $checkToken['user']->getId(),
						'specialization' => $specialization,
					]);
					if(empty($user_spec)){
						$specializationObj= $common->checkEntity('id',$specialization, $this->getDoctrine()->getRepository('AppBundle:Specialization'));
						$specialization = new UserSpecialization();
						$specialization->saveSpecializationData($userObj, $specializationObj, $em);	
					}
					
				}
				$specializationExistArray = $common->getUserSpecialization($checkToken['user']->getId(), $em);
				$dataspecializationExistArray = explode(',',$specializationExistArray);
				foreach($dataspecializationExistArray as $userSpecialization){
					if(!in_array($userSpecialization, $specializationArray)){
						$user_spec = $repository->findOneBy([
						'user' => $checkToken['user']->getId(),
						'specialization' => $userSpecialization,
						]);
						$em->remove($user_spec);
						$em->flush();
					}
				}
			}
			$result['data']   = 'specialization updated successfully';
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
     * @Rest\Post("/api/v0.1/remove/education")
     */
    public function removeeducation(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$serializer = $common->getSerializer();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
		if($checkToken['verify'] == 1){
			$requestParameters = $request->request->all();
			if(isset($requestParameters['id'])){
				$eduObj= $common->checkEntity('id',$requestParameters['id'], $this->getDoctrine()->getRepository('AppBundle:Education'));
				if(count($eduObj)> 0){
					$em = $this->getDoctrine()->getManager();
					$em->remove($eduObj);
					$em->flush();
					$result['data']     = 'Education deleted successfully';
					$result['status']   = "200";
				}else{
					$result['data']     = 'Not Found';
					$result['status']   = "404";
				}
			}else{
				$result['data']     = 'Bad request';
				$result['status']   = "400";
			}
		}else{
			$result['message'] = "user not found";
            $result['status'] = "401";
		}
		$result['send_at'] = date(DATE_ISO8601, strtotime(date("m/d/Y H:i:s")));
		$response = $serializer->serialize($result, 'json');
        return new Response($response);
    }
}
