<?php

namespace AppBundle\Controller;

/**
 * Description of UsuarioController
 *
 * @author Fabio
 */

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use BackendBundle\Entity\Usuario;

class UsuarioController extends Controller
{
     public function newAction(Request $request) {
        $helpers = $this->get("app.helpers");

        $json = $request->get("json", null);
        $params = json_decode($json);

        $data = array(
            "status" => "error",
            "code" => 400,
            "message" => "User not created"
        );

        if ($json != null) {
          
            $role = "usuario";
            $correo = (isset($params->correo)) ? $params->correo : null;
            $nombre = (isset($params->nombre) && ctype_alpha($params->nombre)) ? $params->nombre : null;
            $apellido = (isset($params->apellido) && ctype_alpha($params->apellido)) ? $params->apellido : null;
            $password = (isset($params->password)) ? $params->password : null;
            $celular = (isset($params->celular)) ? $params->celular : null;
            $direccion = (isset($params->direccion)) ? $params->direccion : null;
            $ciudad = (isset($params->ciudad) && ctype_alpha($params->ciudad)) ? $params->ciudad : null;
            $visible = (isset($params->visible)) ? $params->visible : 1;
    
            $emailContraint = new Assert\Email();
            $emailContraint->message = "this email is not valid!!";
            $validate_email = $this->get("validator")->validate($correo, $emailContraint);

            if ($correo != null && count($validate_email) == 0 && $password != null && $nombre != null && 
                $apellido != null && $celular != null && $direccion != null && $ciudad != null) {

                $usuario = new Usuario();
                $usuario->setRole($role);
                $usuario->setCorreo($correo);
                $usuario->setNombre($nombre);
                $usuario->setApellido($apellido);
                $usuario->setCelular($celular);
                $usuario->setDireccion($direccion);
                $usuario->setCiudad($ciudad);
                $usuario->setVisible($visible);

                $pwd = hash("sha256", $password);
                $usuario->setPassword($pwd);

                $em = $this->getDoctrine()->getManager();
                $isset_usuario = $em->getRepository("BackendBundle:Usuario")->findBy(
                        array(
                            "correo" => $correo
                ));

                if (count($isset_usuario) == 0) {
                    $em->persist($usuario);
                    $em->flush();

                    $data["status"] = 'success';
                    $data["code"] = 200;
                    $data["message"] = 'New user created';
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "User not created,duplicate"
                    );
                }
            }
        }
        return $helpers->json($data);
    }
    
    public function editAction(Request $request) {
        $helpers = $this->get("app.helpers");
         
        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        if ($authCheck == true) {
            //obtenemos datos del usuario
            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();
            $usuario = $em->getRepository("BackendBundle:Usuario")->findOneBy(
                    array(
                        "id" => $identity->sub
            ));

            $json = $request->get("json", null);
            $params = json_decode($json);

            $data = array(
                "status" => "error",
                "code" => 400,
                "message" => "User not updated"
            );

            if ($json != null) {
               
                //$role = "usuario";
                //$role = (isset($params->role)) ? $params->role : "usuario";
                $correo = (isset($params->correo)) ? $params->correo : null;
                $nombre = (isset($params->nombre) && ctype_alpha($params->nombre)) ? $params->nombre : null;
                $apellido = (isset($params->apellido) && ctype_alpha($params->apellido)) ? $params->apellido : null;
                $password = (isset($params->password)) ? $params->password : null;
                $celular = (isset($params->celular)) ? $params->celular : null;
                $direccion = (isset($params->direccion)) ? $params->direccion : null;
                $ciudad = (isset($params->ciudad) && ctype_alpha($params->ciudad)) ? $params->ciudad : null;
                
                //var_dump($role);
                
                $emailContraint = new Assert\Email();
                $emailContraint->message = "this email is not valid!!";
                $validate_email = $this->get("validator")->validate($correo, $emailContraint);

                if ($correo != null && count($validate_email) == 0 && $nombre != null && 
                    $apellido != null && $celular != null && $direccion != null && $ciudad != null) {

                    //$usuario->setRole($role);
                    $usuario->setCorreo($correo);
                    $usuario->setNombre($nombre);
                    $usuario->setApellido($apellido);
                    $usuario->setCelular($celular);
                    $usuario->setDireccion($direccion);
                    $usuario->setCiudad($ciudad);

                    if ($password != null && !empty($password)) {
                        $pwd = hash("sha256", $password);
                        $usuario->setPassword($pwd);
                    }

                    $em = $this->getDoctrine()->getManager();
                    $isset_usuario = $em->getRepository("BackendBundle:Usuario")->findBy(
                            array(
                                "correo" => $correo
                    ));

                    if (count($isset_usuario) == 0 || $identity->correo == $correo) {
                        $em->persist($usuario);
                        $em->flush();

                        $data["status"] = 'success';
                        $data["code"] = 200;
                        $data["message"] = 'User updated';
                    } else {
                        $data = array(
                            "status" => "error",
                            "code" => 400,
                            "message" => "User not updated,duplicate"
                        );
                    }
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "Authorization not valid!"
                );
            }
        }
        return $helpers->json($data);
    }

    public function deleteAction(Request $request, $id = null) {

        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        if ($authCheck == true) {
            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();

            $usuario = $em->getRepository("BackendBundle:Usuario")->findOneBy(array(
                "id" => $id
            ));

            if (is_object($usuario) && $identity->role != 'usuario') {

                $visible = 2;
                $usuario->setVisible($visible);
                
                $em->persist($usuario);
                $em->flush();
                
                $data = array(
                    "status" => "success",
                    "code" => 200,
                    "message" => "Producto Delete success!"
                );      
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "Producto not delete!"
                );
            }
        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "message" => "Authentication not Valid! "
            );
        }
        return $helpers->json($data);
    }

    public function listAction(Request $request){
        
        $helpers = $this->get("app.helpers");
        $visible = 1;
        $em = $this->getDoctrine()->getManager();
        
        $dql = "SELECT v FROM BackendBundle:Usuario v WHERE  v.role = 'usuario' AND v.visible = '1' ORDER BY v.id DESC";
        $query = $em->createQuery($dql);
        //v.visible = $visible AND  falta eso xd
        $page = $request->query->getInt("page",1);
        $paginator = $this->get("knp_paginator");
        $items_per_page = 12;
        
        $pagination = $paginator->paginate($query,$page,$items_per_page);
        $total_items_count = $pagination->getTotalItemCount();
        
        $data = array(
            "status"=>"success",
            "total_items_count"=> $total_items_count,
            "page_actual"=>$page,
            "items_per_page"=>$items_per_page,
            "total_pages"=> ceil($total_items_count / $items_per_page),
            "data" => $pagination
        );
        
        return $helpers->json($data);
        
    }
}
