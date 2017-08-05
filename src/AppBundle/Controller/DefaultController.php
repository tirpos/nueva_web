<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
     public function loginAction(Request $request){
        
        $helpers = $this->get("app.helpers");
        $jwt_auth = $this->get("app.jwt_auth");
        
        //recibir json por POST
        $json = $request->get("json",null);
       
        if($json != null){
        
            $params = json_decode($json);
            
            $correo = (isset($params->correo)) ? $params->correo: null;
            $password = (isset($params->password)) ? $params->password: null;
            $getHash = (isset($params->gethash)) ? $params->gethash: null;
            
            $emailContraint = new Assert\Email();
            $emailContraint-> message = "this email is not valid!!";
            
            $validate_email = $this->get("validator")-> validate($correo,$emailContraint);
            
            $pwd = hash("sha256",$password);
                
            if(count($validate_email) == 0 && $pwd != null){
                
                if($getHash == null || $getHash == "false"){
                    $signup = $jwt_auth->signup($correo,$pwd);
                    
                }else{
                    $signup = $jwt_auth->signup($correo,$pwd,true);
                }
                return new JsonResponse($signup); 
            }else{
                return $helpers->json(array(
                        "status"=>"error",
                        "data"=>"Login not valid!!" 
                ));
            }
        }else{
            return $helpers->json(array(
                        "status"=>"error",
                        "data"=>"Send json with post!!" 
                ));
        }        
        die(); 
    }
    
    public function PruebasAction(Request $request)
    {
      $helpers = $this->get("app.helpers");
      $hash = $request->get("authorization",null);
      $check = $helpers->authCheck($hash,true);
      
      var_dump($check); 
      die();
     /* $em= $this->getDoctrine()->getManager();
      $users = $em ->getRepository('BackendBundle:User')->findAll();
      * 
      
      return $helpers-> json($users); */
    }

    public function ImbaAction(Request $request)
    {
      
      
    echo 'Mashable holi';
      

    }
}
