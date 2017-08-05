<?php
namespace AppBundle\Services;

use Firebase\JWT\JWT;
/**
 * Description of JwtAuth
 *
 * @author Fabio
 */
class JwtAuth {
    
    public $manager;
    public $key;
    
    public function __construct($manager) {
        $this->manager = $manager;
        $this->key = "clave-secreta";
    }
    
    public function signup($email,$password ,$getHash = NULL){
        
        $key = $this->key;
        
        $usuario = $this->manager->getRepository('BackendBundle:Usuario')->findOneBy(
                array(
                    "correo" => $email,
                    "password" => $password
                )
            );
            
        $signup = false; 
        $visible = $usuario->getVisible();
        //var_dump($visible);
        if(is_object($usuario) && $visible == 1){ 
            $signup = true;
        }
        
        if($signup == true){
            $token = array(
                "sub"=>$usuario->getId(),
                "nombre"=>$usuario->getNombre(),
                "apellido"=>$usuario->getApellido(),
                "celular"=>$usuario->getCelular(),
                "correo"=>$usuario->getCorreo(),
                "password"=>$usuario->getPassword(),
                "role"=>$usuario->getRole(),
                "visible"=>$usuario->getVisible(),
                "direccion"=>$usuario->getDireccion(),
                "ciudad"=>$usuario->getCiudad(),
                "iat"=>time(),
                "exp"=>time()+(7*24*60*60)    
            );
            $jwt = JWT::encode($token, $key, 'HS256');
            $decoded = JWT::decode($jwt,$key, array('HS256'));
            
            if($getHash != null){
                return $jwt;
            }else{
                return $decoded;
            }
        }else{
            return array("status"=>"error","data"=>"login failed");
        }
    }
    public function checkToken($jwt, $getIdentity = false){
        
        $key = $this->key;
        $auth = false;
        try{
            $decoded = JWT::decode($jwt,$key, array('HS256'));
            
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e){
            $auth = false;
        }
        
        if(isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }
        if($getIdentity == true){
            return $decoded;
        }else{
            return $auth;
        }
        
    }
    
}
