<?php

namespace AppBundle\Controller;

/**
 * Description of CategoriaController
 *
 * @author Fabio
 */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use BackendBundle\Entity\Usuario;
use BackendBundle\Entity\Producto;
use BackendBundle\Entity\Categoria;

class CategoriaController extends Controller {

    public function newAction(Request $request) {

        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);
        
        if ($authCheck == true) {
            //datos del usuario --- creo q no es necesario xd
            $identity = $helpers->authCheck($hash, true);
            //var_dump($identity->role);
            $json = $request->get("json", null);

            if ($json != null) {

                $params = json_decode("$json");

                $usuario_role = ($identity->role) ? $identity->role : 'usuario';
                $nombre = (isset($params->nombre)) ? $params->nombre : null;
                $descripcion = (isset($params->descripcion)) ? $params->descripcion : null;

                //var_dump($categoria_id);
                if ($usuario_role != 'usuario' && $nombre != null && $descripcion != null) {

                    $em = $this->getDoctrine()->getManager();

                    $categoria = new Categoria();
                    $categoria->setNombre($nombre);
                    $categoria->setDescripcion($descripcion);

                    $em->persist($categoria);
                    $em->flush();

                    $categoria = $em->getRepository("BackendBundle:Categoria")->findOneBy(
                            array(
                                "nombre" => $nombre,
                                "descripcion" => $descripcion
                    ));

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "data" => $categoria
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "Categoria not created"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "Categoria not created, params failed"
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

    //ver si restringir si no pasa un $id :P , osea controlar ese asunto , no se usa todavia
    public function editAction(Request $request, $id = null) {

        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        if ($authCheck == true) {

            $identity = $helpers->authCheck($hash, true);

            $json = $request->get("json", null);

            if ($json != null) {

                $params = json_decode("$json");

                $categoria_id = $id;

                $usuario_role = ($identity->role) ? $identity->role : 'usuario';
                $nombre = (isset($params->nombre)) ? $params->nombre : null;
                $descripcion = (isset($params->descripcion)) ? $params->descripcion : null;


                if ($usuario_role != 'usuario' && $nombre != null && $descripcion != null) {

                    $em = $this->getDoctrine()->getManager();
                    $categoria = $em->getRepository("BackendBundle:Categoria")->findOneBy(
                            array(
                                "id" => $categoria_id
                    ));

                    $categoria->setNombre($nombre);
                    $categoria->setDescripcion($descripcion);

                    $em->persist($categoria);
                    $em->flush();
                    var_dump($categoria);

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "message" => "Categoria updated success!!"
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "Categoria updated error"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "Categoria not update, params failed"
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

    public function listAction(Request $request) {

        $helpers = $this->get("app.helpers");

        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT v FROM BackendBundle:Categoria v ORDER BY v.id DESC";
        $query = $em->createQuery($dql);

        $page = $request->query->getInt("page", 1);
        $paginator = $this->get("knp_paginator");
        $items_per_page = 6;

        $pagination = $paginator->paginate($query, $page, $items_per_page);
        $total_items_count = $pagination->getTotalItemCount();

        $data = array(
            "status" => "success",
            "total_items_count" => $total_items_count,
            "page_actual" => $page,
            "items_per_page" => $items_per_page,
            "total_pages" => ceil($total_items_count / $items_per_page),
            "data" => $pagination
        );

        return $helpers->json($data);
    }

    public function detailAction(Request $request, $id = null) {

        $helpers = $this->get("app.helpers");

        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT v FROM BackendBundle:Producto v WHERE v.categoria = $id AND v.visible = '1' ORDER BY v.id DESC";
        $query = $em->createQuery($dql);

        $page = $request->query->getInt("page", 1);
        $paginator = $this->get("knp_paginator");
        $items_per_page = 6;

        $pagination = $paginator->paginate($query, $page, $items_per_page);
        $total_items_count = $pagination->getTotalItemCount();

        $data = array(
            "status" => "success",
            "total_items_count" => $total_items_count,
            "page_actual" => $page,
            "items_per_page" => $items_per_page,
            "total_pages" => ceil($total_items_count / $items_per_page),
            "data" => $pagination
        );

        return $helpers->json($data);
    }

    public function detailPackAction(Request $request, $id = null) {

        $helpers = $this->get("app.helpers");

        $em = $this->getDoctrine()->getManager();
        //$visible = 1;

        $dql = "SELECT v FROM BackendBundle:Pack v WHERE v.categoria = $id AND v.visible = '1' ORDER BY v.id DESC";
        $query = $em->createQuery($dql);

        $page = $request->query->getInt("page", 1);
        $paginator = $this->get("knp_paginator");
        $items_per_page = 12;

        $pagination = $paginator->paginate($query, $page, $items_per_page);
        $total_items_count = $pagination->getTotalItemCount();

        $data = array(
            "status" => "success",
            "total_items_count" => $total_items_count,
            "page_actual" => $page,
            "items_per_page" => $items_per_page,
            "total_pages" => ceil($total_items_count / $items_per_page),
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

            $categoria = $em->getRepository("BackendBundle:Categoria")->findOneBy(array(
                "id" => $id
            ));
            
            if (is_object($categoria) && $identity->role != 'usuario') {

                $categoria_producto = "General";
                $categorianew = $em->getRepository("BackendBundle:Categoria")->findOneBy(array(
                    "nombre" => $categoria_producto
                ));
                //id new categoria
               
                $producto1 = $em->getRepository("BackendBundle:Producto")->findBy(array(
                    "categoria" => $id
                ));
                
                $largo = count($producto1);
                //var_dump($largo);
               for($i = 0; $i<$largo; $i++){
                    
                    $producto = $producto1[$i];
                    $producto->setCategoria($categorianew);
                    $em->persist($producto);
                    $em->flush();
               }
               //var_dump($largo);
               $em->remove($categoria);
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
    //wea para mostrar para crear producto , ya no sirve wn xd
    public function listtAction(Request $request) {

        $helpers = $this->get("app.helpers");

        $em = $this->getDoctrine()->getManager();

        $categoria = $em->getRepository("BackendBundle:Categoria")->findAll();

        $data = array(
                "status" => "success",
                "code" => 200,
                "data" => $categoria
            );

        return $helpers->json($data);
    }
}
