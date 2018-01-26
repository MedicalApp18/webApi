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
use AppBundle\Entity\UserDocuments;
use FOS\RestBundle\View\View;


class ProfileApiController extends Controller
{
    /**
     * @Rest\Get("/api/v0.1/profile/details")
     */
    public function profiledetails(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$em     	= $this->getDoctrine()->getManager();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
        $serializer = $common->getSerializer();
		if($checkToken['verify'] == 1){
			$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
			$photo= $checkToken['user']->getProfilePhotoPath();
			$result['photoURL'] = $baseurl.'/'.$photo;
			$result['data']     = $checkToken['user'];
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
     * @Rest\Post("/api/v0.1/update/profile")
     */
    public function updatedetails(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$serializer = $common->getSerializer();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
		if($checkToken['verify'] == 1){
			$requestParameters = $request->request->all();
			$userObj = $this->getDoctrine()->getRepository('AppBundle:User')->find($checkToken['user']->getId());
			$em     	= $this->getDoctrine()->getManager();
			$user = new User();
			$user->updateData($requestParameters, $userObj, $em, $this);
			$result['data']     = "user info update successfully.";
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
     * @Rest\Post("/api/v0.1/update/profile/pic")
     */
    public function updateProfilePic(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$serializer = $common->getSerializer();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
		if($checkToken['verify'] == 1){
			$uploadedFile =  $request->files->get('fileData');
			$checkFileType = $common->checkProfilePicMimeType($uploadedFile->getClientMimeType());
			if($checkFileType['type'] =='valid'){
				$dir = $this->get('kernel')->getRootDir() . '/../web/uploads/images/';
				$name = uniqid() .$checkFileType['ext'];
				$uploadedFile->move($dir, $name);
				$em     	= $this->getDoctrine()->getManager();
				$userObj = $this->getDoctrine()->getRepository('AppBundle:User')->find($checkToken['user']->getId());
				$data['fileData'] = $name;
				$user = new User();
				$user->updateData($data, $userObj, $em, $this);
				$result['message'] = "user profile pic update successfully.";
				$result['status']   = "200";
			}else{
				$result['message'] = "invalid format";
				$result['status']   = "205";
			}
		}else{
			$result['message'] = "user not found";
            $result['status'] = "201";
		}
		$result['send_at'] = date(DATE_ISO8601, strtotime(date("m/d/Y H:i:s")));
		$response = $serializer->serialize($result, 'json');
        return new Response($response);
    }
	
	/**
     * @Rest\Get("/api/v0.1/get/profile/pic")
     */
    public function getProfilePic(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$serializer = $common->getSerializer();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
		if($checkToken['verify'] == 1){
			$userObj = $this->getDoctrine()->getRepository('AppBundle:User')->find($checkToken['user']->getId());
			$profilePhoto = $userObj->getprofilePhoto();
			$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
			$dir = '/uploads/images/';
			if(!empty($profilePhoto)){
				$result['data']     = $baseurl.$dir.$profilePhoto;
			}else{
				$result['data']     = $baseurl.$dir.'avatar.png';
			}
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
     * @Rest\Post("/api/v0.1/upload/document")
     */
    public function userDocumentsUpload(Request $request){
		$common     = $this->get(Common::class);
		$Jwtauth    = $this->get(Jwtauth::class);
		$serializer = $common->getSerializer();
		$checkToken = $Jwtauth->checkToken($request->headers->get('Authorization'), $this->getDoctrine()->getRepository('AppBundle:User'));
		if($checkToken['verify'] == 1){
			$uploadedFile =  $request->files->get('fileData');
			$dir 	= $this->get('kernel')->getRootDir() . '/../web/uploads/documents/';
			$name 	= uniqid() .'.'.$uploadedFile->getClientOriginalExtension();
			$uploadedFile->move($dir, $name);
			$em     = $this->getDoctrine()->getManager();
			$userObj= $this->getDoctrine()->getRepository('AppBundle:User')->find($checkToken['user']->getId());
			$data['fileData']    = $name;
			$data['orgFileName'] = $uploadedFile->getClientOriginalName();
			$data['fileName'] 	 = $name;
			$data['fileType'] 	 = $uploadedFile->getClientMimeType();
			$user = new UserDocuments();
			$user->saveFileData($data, $userObj, $em, $this);
			$result['message'] = "Document uploded successfully.";
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
