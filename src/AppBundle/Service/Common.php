<?php
namespace AppBundle\Service;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\UserOtp;

class Common{
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
	public function generateRandomString(){
	    $chars ="1234567890";
	    $final_rand='';
	    for($i=0;$i<6; $i++){
		    $final_rand .= $chars[ rand(0,strlen($chars)-1)];
	    }
	    return $final_rand;
	}
	public function getSerializer(){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        return $serializer;
    }
	public function getTableList($table, $em){
        $sql ="select id, name from $table order by id";
        $stmt   = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $dataArray = $stmt->fetchAll();
		
        $returnArray =array();
        if(count($dataArray)>0){
            $i=0;
            foreach($dataArray as $data){
                $returnArray[$i]['id'] 		= $data['id'];
                $returnArray[$i]['name'] 	= $data['name'];
                $i++;
            }
        }
        return $returnArray;
    }
	public function checkUserExist($array, $userRepository){
		$user 	= $userRepository->findOneBy($array);
		$return = true;
		if(count($user)>0){
			$return=false;
		}
		return $return;
    }
	function checkCredentials($user_name, $user_password, $em){
        ini_set('memory_limit', '-1');
        if(empty($user_password)){
            $query = $em->createQueryBuilder()
                ->select('u') 
                ->from('AppBundle:User', 'u')
                ->where('u.username = :user_name'  )
                ->setParameter('user_name', $user_name)
                ->getQuery();
            $user = $query->getOneOrNullResult();
            if(count($user)===1){
                return $user;
            }else{
				$query = $em->createQueryBuilder()
                ->select('u') 
                ->from('AppBundle:User', 'u')
                ->where('u.mobileNumber = :mobileNumber'  )
                ->setParameter('mobileNumber', $user_name)
                ->getQuery();
		$user = $query->getOneOrNullResult();
		if(count($user)===1){
		   return $user;
		}
                return false;
            }
        }elseif(!empty($user_password)){
            $query = $em->createQueryBuilder()
                ->select('u') 
                ->from('AppBundle:User', 'u')
                ->where('u.username = :user_name AND u.password= :password AND u.enabled=1')
                ->setParameter('user_name', $user_name)
                ->setParameter('password', $user_password)
                ->getQuery();
            $users = $query->getResult();
            if(count($users)===1){
                /* Fetch Unread notifications, messages, activity */
                
                return $users;
            }else{
                return false;
            }
        }
    }
	function setSecurityEncoder($userArray, $password, $controllerObj){  
          $userArray->setPassword($password);                          
          $factory = $controllerObj->get('security.encoder_factory');  
          $encoder = $factory->getEncoder($userArray);                 
          $password = $encoder->encodePassword($userArray->getPassword(), $userArray->getSalt());
          $userPassword = $userArray->setPassword($password);          
          return $userPassword;                                        
    }
	function sendMail($mailer, $transport, $userarray, $subject, $body){
        $mailUserName = $this->container->getParameter('mailer_user');
		$message = $mailer->createMessage()
						  ->setSubject($subject)
						  ->setFrom($mailUserName,'Yours App')
						  ->setTo($userarray['email'], $userarray['name'])
						  ->setContentType('text/html')
						  ->setBody($body);
		$mailer->send($message);
		$spool = $mailer->getTransport()->getSpool();
		$spool->flushQueue($transport);
	}
	
	function saveOTP($userArray, $em){
		$token = $this->generateRandomString();
		$userOtp = new UserOtp();
		$userOtp->setUser($userArray);
		$userOtp->setOtp($token);
		$userOtp->setCreatedDate(new \DateTime("now"));
		$em->persist($userOtp);
		$em->flush();
		return $token;
    }
	function checkOTP($userId, $otp, $em){
		$sql ="select otp from user_otp  where user_id ='".$userId."' and otp ='".$otp."' order by created_at asc LIMIT 0,1";
		$stmt = $em->getConnection()->prepare($sql);
		$stmt->execute();
		$count = $stmt->fetch();
		if(count($count)>0 && is_array($count)){
			return true;
		}else{
			return false;
		}
    }
	public function checkProfilePicMimeType($type){
		$returnArray = array();
        switch(strtolower($type))
        {
            case 'image/png':
                $returnArray['type'] = 'valid';
				$returnArray['ext'] = '.png';
                break;
            case 'image/jpeg':
                $returnArray['type'] = 'valid';
				$returnArray['ext'] = '.jpeg';
                break;
            case 'image/gif':
                $returnArray['type'] = 'valid';
				$returnArray['ext'] = '.gif';
                break;
            default:
				$returnArray['type'] = 'invalid';
        }
        return $returnArray;
    }
	
	public function checkEntity($column, $id, $entityRepository){
		$entityObj = $entityRepository->findOneBy(array($column => $id));
		return $entityObj;
    }
	
	public function getAllEntity($column, $id, $entityRepository){
		$entityObj = $entityRepository->findAll(array($column => $id));
		return $entityObj;
    }
}
