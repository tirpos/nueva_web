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
use BackendBundle\Entity\Producto;
use BackendBundle\Entity\Pack;


class PackController extends Controller {

    public function newAction(Request $request) {

        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        if ($authCheck == true) {
            //datos del usuario --- creo q no es necesario xd
            $identity = $helpers->authCheck($hash, true);

            $json = $request->get("json", null);

            if ($json != null) {

                $params = json_decode("$json");

                $imagen = null;
                $usuario_role = ($identity->role) ? $identity->role : 'usuario';
                $nombre = (isset($params->nombre)) ? $params->nombre : null;
                $descripcion = (isset($params->descripcion)) ? $params->descripcion : null;
                $precio = (isset($params->precio)) ? $params->precio : null;
                $visible = (isset($params->visible)) ? $params->visible : 1;
                //revisar****
                $categoria_id = (isset($params->categoria)) ? $params->categoria : 'Pack';

                if ($usuario_role != 'usuario' && $nombre != null && $categoria_id != null) {
                    $em = $this->getDoctrine()->getManager();

                    $categoria = $em->getRepository("BackendBundle:Categoria")->findOneBy(
                            array(
                                "nombre" => $categoria_id
                    ));

                    $pack = new Pack();
                    $pack->setImagen($imagen);
                    $pack->setNombre($nombre);
                    $pack->setDescripcion($descripcion);
                    $pack->setPrecio($precio);
                    $pack->setVisible($visible);
                    $pack->setCategoria($categoria);

                    $em->persist($pack);
                    $em->flush();

                    $pack = $em->getRepository("BackendBundle:Pack")->findOneBy(
                            array(
                                "nombre" => $nombre,
                                "precio" => $precio,
                                "descripcion" => $descripcion
                    ));

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "data" => $pack
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "Pack not created"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "Packnot created, params failed"
                );
            }
        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "message" => "Authorization not valid!"
            );
        }
        return $helpers->json($data);
    }
    //no se usa y no esta arreglado
    public function editAction(Request $request, $id = null) {

        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        if ($authCheck == true) {

            $identity = $helpers->authCheck($hash, true);

            $json = $request->get("json", null);

            if ($json != null) {

                $params = json_decode("$json");

                $pack_id = $id;

                $imagen = null;
                $usuario_role = ($identity->role) ? $identity->role : 'usuario';
                $nombre = (isset($params->nombre)) ? $params->nombre : null;
                $descripcion = (isset($params->descripcion)) ? $params->descripcion : null;
                $precio = (isset($params->precio)) ? $params->precio : null;
                $visible = (isset($params->visible)) ? $params->visible : 1;
                //revisar****
                $categoria_id = (isset($params->categoria)) ? $params->categoria : 'Pack';


                if ($usuario_role != 'usuario' && $nombre != null && $descripcion != null && $precio != null) {

                    $em = $this->getDoctrine()->getManager();
                    $pack = $em->getRepository("BackendBundle:Pack")->findOneBy(
                            array(
                                "id" => $pack_id
                    ));
                    
                    $categoria = $em->getRepository("BackendBundle:Categoria")->findOneBy(
                            array(
                                "nombre" => $categoria_id
                    ));

                    $pack->setImagen($imagen);
                    $pack->setNombre($nombre);
                    $pack->setDescripcion($descripcion);
                    $pack->setPrecio($precio);
                    $pack->setVisible($visible);
                    $pack->setCategoria($categoria);

                    $em->persist($pack);
                    $em->flush();
                    //var_dump($pack);

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "message" => "Pack updated success!!"
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "Pack updated error"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "Pack not update, params failed"
                );
            }
        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "message" => "Authorization not valid!"
            );
        }
        return $helpers->json($data);
    }
    
    public function uploadAction(Request $request, $id) {

        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        if ($authCheck == true) {

            $identity = $helpers->authCheck($hash, true);

            $pack_id = $id;

            $em = $this->getDoctrine()->getManager();
            $pack = $em->getRepository("BackendBundle:Pack")->findOneBy(
                    array(
                        "id" => $pack_id
            ));
            if ($pack_id != null && isset($identity->role) && $identity->role == 'admin') {

                $file = $request->files->get('imagen', null);

                if ($file != null && !empty($file)) {
                    $ext = $file->guessExtension();

                    if ($ext == "jpeg" || $ext == "jpg" || $ext == "png") {
                        $file_name = time() . "." . $ext;
                        $path_of_file = "uploads/pack_images/pack_" . $pack_id;
                        $file->move($path_of_file, $file_name);

                        $pack->setImagen($file_name);
                        $em->persist($pack);
                        $em->flush();

                        $data = array(
                            "status" => "success",
                            "code" => 200,
                            "message" => "Image file upload!!"
                        );
                    } else {
                        $data = array(
                            "status" => "error",
                            "code" => 400,
                            "message" => "Format for image not valid!!"
                        );
                    }
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "Image not loader!"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "Authorization not valid!2"
                );
            }
        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "message" => "Authorization not valid!1"
            );
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

            $pack = $em->getRepository("BackendBundle:Pack")->findOneBy(array(
                "id" => $id
            ));

            if (is_object($pack) && $identity->role != 'usuario') {

                $visible = 2;
                $pack->setVisible($visible);
                $em->persist($pack);
                $em->flush();
                
                $data = array(
                    "status" => "success",
                    "code" => 200,
                    "message" => "Pack Delete success!"
                );      
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "Pack not delete!"
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
    //no se usa todavia xd
    public function listAction(Request $request){
        
        $helpers = $this->get("app.helpers");
        $visible = 1;
        $em = $this->getDoctrine()->getManager();
        
        $dql = "SELECT v FROM BackendBundle:Pack v WHERE v.visible = '1' ORDER BY v.id DESC";
        $query = $em->createQuery($dql);
        
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