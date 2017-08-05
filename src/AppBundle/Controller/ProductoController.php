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

class ProductoController extends Controller {

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
                $tamano = (isset($params->tamano)) ? $params->tamano : null;
                $precio = (isset($params->precio)) ? $params->precio : null;
                $descuento = (isset($params->descuento)) ? $params->descuento : null;
                $visible = (isset($params->visible)) ? $params->visible : 1;
                //revisar****
                $categoria_id = (isset($params->categoria)) ? $params->categoria : 'General';
             
                //var_dump($categoria_id);
                //var_dump($visible);
      
                if ($usuario_role != 'usuario' && $nombre != null && $categoria_id != null) {
                    $em = $this->getDoctrine()->getManager();

                    $categoria = $em->getRepository("BackendBundle:Categoria")->findOneBy(
                            array(
                                "nombre" => $categoria_id
                    ));

                    $producto = new Producto();
                    $producto->setImagen($imagen);
                    $producto->setNombre($nombre);
                    $producto->setDescripcion($descripcion);
                    $producto->setTamano($tamano);
                    $producto->setPrecio($precio);
                    $producto->setDescuento($descuento);
                    $producto->setVisible($visible);
                    $producto->setCategoria($categoria);

                    $em->persist($producto);
                    $em->flush();

                    $producto = $em->getRepository("BackendBundle:Producto")->findOneBy(
                            array(
                                "nombre" => $nombre,
                                "tamano" => $tamano,
                                "precio" => $precio,
                                "descripcion" => $descripcion
                    ));

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "data" => $producto
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "Producto not created"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "Producto not created, params failed"
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

    //ver si restringir si no pasa un $id :P
    public function editAction(Request $request, $id = null) {

        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        if ($authCheck == true) {

            $identity = $helpers->authCheck($hash, true);

            $json = $request->get("json", null);

            if ($json != null) {

                $params = json_decode("$json");

                $producto_id = $id;

                $imagen = null;
                $usuario_role = ($identity->role) ? $identity->role : 'usuario';
                $nombre = (isset($params->nombre)) ? $params->nombre : null;
                $descripcion = (isset($params->descripcion)) ? $params->descripcion : null;
                $tamano = (isset($params->tamano)) ? $params->tamano : null;
                $precio = (isset($params->precio)) ? $params->precio : null;
                $descuento = (isset($params->descuento)) ? $params->descuento : null;
                $visible = (isset($params->visible)) ? $params->visible : 1;
                
                $categoria_id = (isset($params->categoria)) ? $params->categoria : 'General';
                if(is_object($categoria_id)){
                    $ca = $categoria_id->nombre;
                    $em = $this->getDoctrine()->getManager();
                    $categoria = $em->getRepository("BackendBundle:Categoria")->findOneBy(
                            array(
                                "nombre" => $ca
                    ));
                }else{
                    $em = $this->getDoctrine()->getManager();
                    $categoria = $em->getRepository("BackendBundle:Categoria")->findOneBy(
                            array(
                                "nombre" => $categoria_id
                ));
                }
                //var_dump($ca);

                if ($usuario_role != 'usuario' && $nombre != null && $categoria_id != null) {
                    $em = $this->getDoctrine()->getManager();

                    $producto = $em->getRepository("BackendBundle:Producto")->findOneBy(
                            array(
                                "id" => $producto_id
                    ));
                
                    //$producto->setImagen($imagen);
                    $producto->setNombre($nombre);
                    $producto->setDescripcion($descripcion);
                    $producto->setTamano($tamano);
                    $producto->setPrecio($precio);
                    $producto->setDescuento($descuento);
                    $producto->setVisible($visible);
                    $producto->setCategoria($categoria);

                    $em->persist($producto);
                    $em->flush();

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "message" => "Producto updated success!!"
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "Producto updated error"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "Produto not update, params failed"
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
        //var_dump($authCheck);
        if ($authCheck == true) {

            $identity = $helpers->authCheck($hash, true);

            $producto_id = $id;

            $em = $this->getDoctrine()->getManager();
            $producto = $em->getRepository("BackendBundle:Producto")->findOneBy(
                    array(
                        "id" => $producto_id
            ));
            if ($producto_id != null && isset($identity->role) && $identity->role == 'admin') {

                $file = $request->files->get('imagen', null);

                if ($file != null && !empty($file)) {
                    $ext = $file->guessExtension();

                    if ($ext == "jpeg" || $ext == "jpg" || $ext == "png") {
                        $file_name = time() . "." . $ext;
                        $path_of_file = "uploads/producto_images/producto_" . $producto_id;
                        $file->move($path_of_file, $file_name);

                        $producto->setImagen($file_name);
                        $em->persist($producto);
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
                "message" => "Authorization no valida!"
            );
        }
        return $helpers->json($data);
    }
    //user
    public function listAction(Request $request){
        
        $helpers = $this->get("app.helpers");
        $visible = 1;
        $em = $this->getDoctrine()->getManager();
        
        $dql = "SELECT v FROM BackendBundle:Producto v WHERE v.visible = '1' ORDER BY v.id DESC";
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
    
    public function detailAction (Request $request , $id = null){
        
       $helpers = $this->get("app.helpers");
       $em = $this->getDoctrine()->getManager();
       
       $producto = $em->getRepository("BackendBundle:Producto")->findOneBy(array(
            "id" => $id
       ));
        
       if($producto){
           
           $data["status"] ='success';
           $data["code"]=200;
           $data["data"] = $producto;
       }else{
           $data = array(
           "status" => "error",
           "code" => 400,
           "message" => "producto not exist"
        );
       }
        
       return $helpers->json($data);
   }
   
    public function searchAction(Request $request, $search){
        
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        if($search != null){
            $dql = "SELECT v FROM BackendBundle:Producto v ".
                    "WHERE  v.nombre LIKE '%$search%' OR ".
                    " v.descripcion like '%$search%' ORDER BY v.id DESC";
        }else{
            $dql = "SELECT v FROM BackendBundle:Producto v ORDER BY v.id DESC";
        }

        $query = $em->createQuery($dql);
        
        $page = $request->query->getInt("page",1);
        $paginator = $this->get("knp_paginator");
        $items_per_page = 6;
        
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
    
    public function deleteAction(Request $request, $id = null) {

        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        if ($authCheck == true) {
            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();

            $producto = $em->getRepository("BackendBundle:Producto")->findOneBy(array(
                "id" => $id
            ));

            if (is_object($producto) && $identity->role != 'usuario') {

                $visible = 2;
                $producto->setVisible($visible);
                
                $em->persist($producto);
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
    
    public function ofertasAction(Request $request){
        
        $helpers = $this->get("app.helpers");
        $visible = 1;
        $em = $this->getDoctrine()->getManager();
        
        $dql = "SELECT v FROM BackendBundle:Producto v WHERE v.visible = '1' AND v.descuento IS NOT NULL ORDER BY v.descuento DESC";
        $query = $em->createQuery($dql)->setMaxResults(3);
        $productos = $query->getResult();

        $dql = "SELECT v FROM BackendBundle:Producto v WHERE v.visible = '1' ORDER BY v.id DESC";
        $query = $em->createQuery($dql)->setMaxResults(4);
        $productos2 = $query->getResult();
        
        $data = array(
            "status"=>"success",
            "data"=>$productos,
            "data2"=>$productos2
        );
        
        return $helpers->json($data);
        
    }

    public function recientesAction(Request $request){
        
        $helpers = $this->get("app.helpers");
        $em = $this->getDoctrine()->getManager();
        
        $dql = "SELECT v FROM BackendBundle:Producto v WHERE v.visible = '1' ORDER BY v.id DESC";
        $query = $em->createQuery($dql)->setMaxResults(4);
        $productos = $query->getResult();
        
        $data = array(
            "status"=>"success",
            "data"=>$productos
        );
        
        return $helpers->json($data);
        
    }
}
