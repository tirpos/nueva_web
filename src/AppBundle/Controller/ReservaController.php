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
use BackendBundle\Entity\Reserva;
use BackendBundle\Entity\Linea;


class ReservaController extends Controller {
    
    public function newAction(Request $request) {

        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);
        $precio = $request->get("precio", null);

        if ($authCheck == true) {
            //datos del usuario --- creo q no es necesario xd
            $identity = $helpers->authCheck($hash, true);
            $json = $request->get("json", null);
            $params = json_decode("$json");
                $usuario_id = ($identity->sub) ? $identity->sub : 'null';
                $estado = 'pendiente';
                $comprobante = null;
                $fecha_emision =  new \Datetime('now');
                $fecha_actualizacion =  new \Datetime('now');

                if ($usuario_id != 'null' && $json != null ) {

                    $em = $this->getDoctrine()->getManager();
                   
                    $usuario = $em->getRepository("BackendBundle:Usuario")->findOneBy(
                            array(
                                "id" => $usuario_id
                    ));
                    
                    $reserva = new Reserva();
                    $reserva->setFechaEmision($fecha_emision);
                    $reserva->setFechaActualizacion($fecha_actualizacion);
                    $reserva->setComprobante($comprobante);
                    $reserva->setEstado($estado);
                    $reserva->setUsuario($usuario);
                    $reserva->setPrecioTotal($precio);
                    
                    $em->persist($reserva);
                    $em->flush();

                    $reserva = $em->getRepository("BackendBundle:Reserva")->findOneBy(
                            array(
                                "fechaEmision" => $fecha_emision,
                                "precioTotal" => $precio,
                                "usuario" => $usuario_id
                    ));
                    
                    //crear lineas de producto
                    
                    $largo = count($params);
                    $mensaje = "";
                    for($i = 0; $i<$largo; $i++){
                        
                        $linea = new Linea();
                        $carrito = $params[$i];
                        if($carrito->producto == ''){
                             $producto_id = null;
                             $linea->setProducto($producto_id);
                        }else{
                            $producto_id = $carrito->producto->id;
                            $producto = $em->getRepository("BackendBundle:Producto")->findOneBy(
                            array(
                                "id" => $producto_id
                            ));
                            $linea->setProducto($producto);
                            $cosa = 'producto(s)';
                            $namepro = $producto->getNombre();
                        }
                        if($carrito->pack == ''){
                             $pack_id = null;
                             $linea->setPack($pack_id);
                        }else{
                            $pack_id = $carrito->pack->id;
                            $pack = $em->getRepository("BackendBundle:Pack")->findOneBy(
                            array(
                                "id" => $pack_id   
                            ));
                            $linea->setPack($pack);
                            $cosa = 'pack(s)';
                            $namepro = $pack->getNombre();
                        }
                        /*$pack = $em->getRepository("BackendBundle:Pack")->findOneBy(
                            array(
                                "id" => $pack_id
                        ));*/
                        /*$producto = $em->getRepository("BackendBundle:Producto")->findOneBy(
                            array(
                                "id" => $producto_id
                        ));*/
                        $cantidad = (isset($carrito->cantidad)) ? $carrito->cantidad : null;
                        $preciolinea = (isset($carrito->preciolinea)) ? $carrito->preciolinea : null;

                        $linea->setPreciolinea($preciolinea);
                        $linea->setCantidad($cantidad);
                        $linea->setReserva($reserva);

                        $em->persist($linea);
                        $em->flush();
                        $nuevo = ' '.$cantidad. ' ' .$cosa. ' de ' .$namepro. ' con un precio de linea de: $'.$preciolinea.' , ';
                        $mensaje = $mensaje . $nuevo;
                    }   

                    $rese = $reserva->getId();
                    $precio = $reserva->getPreciototal();

                        $message = \Swift_Message:: newInstance()
                        ->setSubject('Nueva Reserva')
                        ->setFrom('ventas.jabon@gmail.com')
                        ->setTo('ivyjnana@gmail.com')
                        ->setBody(
                            $this->renderView('Emails/email.html.twig',
                            array('id'=> $rese,'contenido'=> $mensaje, 'precio' => $precio)

                            ),'text/html'
                        )
                        ;
                        $this->get('mailer')->send($message);

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "data" => $linea
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "Reserva not created"
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

            $reserva_id = $id;

            $em = $this->getDoctrine()->getManager();
            $reserva = $em->getRepository("BackendBundle:Reserva")->findOneBy(
                    array(
                        "id" => $reserva_id
            ));
            if ($reserva_id != null && isset($identity->role) && $identity->role == 'usuario') {

                $file = $request->files->get('imagen', null);

                if ($file != null && !empty($file)) {
                    $ext = $file->guessExtension();

                    if ($ext == "jpeg" || $ext == "jpg" || $ext == "png") {
                        $file_name = time() . "." . $ext;
                        $path_of_file = "uploads/reserva_images/reserva_" . $reserva_id;
                        $file->move($path_of_file, $file_name);
                        
                        $fecha_actualizacion =  new \Datetime('now');

                        $reserva->setFechaActualizacion($fecha_actualizacion);
                        $reserva->setComprobante($file_name);
                        $em->persist($reserva);
                        $em->flush();

                        $rese = $reserva->getId();

                        $message = \Swift_Message:: newInstance()
                        ->setSubject('Confirmación de Comprobante')
                        ->setFrom('ventas.jabon@gmail.com')
                        ->setTo('fabio.alderete00@gmail.com')
                        ->setBody('<p> Se ha Subido el comprobante de la reserva con el Identificador '.$rese.'</p><br>
                        <p> Reserva a espera de confirmación </p>', 'text/html');
                        $this->get('mailer')->send($message);

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
    //mejorado ya :P
    public function editAction(Request $request, $id = null) {

        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        if ($authCheck == true) {

            $identity = $helpers->authCheck($hash, true);

                $reserva_id = $id;

                if ($identity->role != 'usuario' && $reserva_id != null) {
                    $em = $this->getDoctrine()->getManager();

                    $reserva = $em->getRepository("BackendBundle:Reserva")->findOneBy(
                            array(
                                "id" => $reserva_id
                    ));
                    $estado = 'aprobado';
                    $reserva->setEstado($estado);

                    $fecha_actualizacion =  new \Datetime('now');
                    $reserva->setFechaActualizacion($fecha_actualizacion);

                    $em->persist($reserva);
                    $em->flush();

                    
                    
                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "message" => "Reserva updated success!!"
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "Reserva updated error"
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

    //lista de reservas sin comprobante ADMIN
    public function list1Action(Request $request , $ide = null){
        
        $helpers = $this->get("app.helpers");
        $comprobante = 'null';
        $em = $this->getDoctrine()->getManager();

        if($ide == 1){
            //pendiente
             $dql = "SELECT v FROM BackendBundle:Reserva v WHERE v.comprobante IS NULL AND v.estado = 'pendiente' ORDER BY v.id DESC";
        }else if($ide == 2){
            //con comprobante
             $dql = "SELECT v FROM BackendBundle:Reserva v WHERE v.comprobante IS NOT NULL AND v.estado = 'pendiente' ORDER BY v.id DESC";
        }else if($ide == 3){
            //aprobados
            $dql = "SELECT v FROM BackendBundle:Reserva v WHERE v.estado = 'aprobado' ORDER BY v.id DESC";
        }
        
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
     //list USER  confirmadas
    public function list4Action(Request $request , $id = null , $ide = null){
        
        $helpers = $this->get("app.helpers");
        $id_usuario = $id;
        $em = $this->getDoctrine()->getManager();
         if($ide == 4){
            $dql = "SELECT v FROM BackendBundle:Reserva v WHERE v.estado = 'pendiente' AND v.comprobante IS NULL AND v.usuario = $id_usuario ORDER BY v.id DESC";
        }else if($ide == 5){
             $dql = "SELECT v FROM BackendBundle:Reserva v WHERE v.estado = 'pendiente' AND v.comprobante IS NOT NULL AND v.usuario = $id_usuario ORDER BY v.id DESC";
        }
        else if($ide == 6){
             $dql = "SELECT v FROM BackendBundle:Reserva v WHERE v.estado = 'aprobado' AND v.usuario = $id_usuario ORDER BY v.id DESC";
        }
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
