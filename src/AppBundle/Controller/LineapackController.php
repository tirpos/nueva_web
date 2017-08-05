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
use BackendBundle\Entity\Lineapack;


class LineapackController extends Controller {

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

                $usuario_role = ($identity->role) ? $identity->role : 'usuario';
                $cantidad = (isset($params->cantidad)) ? $params->cantidad : null;
               
                $pack_id = (isset($params->pack)) ? $params->pack : null;
                $producto_id = (isset($params->producto)) ? $params->producto : null;

                if ($usuario_role != 'usuario' && $cantidad != null && $pack_id != null
                    && $producto_id != null) {
                    $em = $this->getDoctrine()->getManager();

                    $pack = $em->getRepository("BackendBundle:Pack")->findOneBy(
                            array(
                                "id" => $pack_id
                    ));
                    $producto = $em->getRepository("BackendBundle:Producto")->findOneBy(
                            array(
                                "id" => $producto_id
                    ));

                    $lineapack = new Lineapack();
                   
                    $lineapack->setCantidad($cantidad);
                    $lineapack->setProducto($producto);
                    $lineapack->setPack($pack);

                    $em->persist($lineapack);
                    $em->flush();

                    $lineapack = $em->getRepository("BackendBundle:Lineapack")->findOneBy(
                            array(
                                "cantidad" => $cantidad,
                                "producto" => $producto_id,
                                "pack" => $pack_id
                    ));

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "data" => $lineapack
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "message" => "Lineapack not created"
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "Lineapack not created, params failed"
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
    // no se ocupa ctm xd xd
    public function deleteAction(Request $request, $id = null) {

        $helpers = $this->get("app.helpers");

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        if ($authCheck == true) {
            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();

            $lineapack = $em->getRepository("BackendBundle:Lineapack")->findOneBy(array(
                "id" => $id
            ));

            if (is_object($lineapack) && $identity->role != 'usuario') {

                $em->remove($lineapack);
                $em->flush();
                
                $data = array(
                    "status" => "success",
                    "code" => 200,
                    "message" => "Linea Delete success!"
                );      
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "Lineapack not delete!"
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
    //wea 
    public function detailAction (Request $request , $id = null){
        
       $helpers = $this->get("app.helpers");
       $em = $this->getDoctrine()->getManager();
       
       $lineapack = $em->getRepository("BackendBundle:Lineapack")->findBy(array(
            "pack" => $id
       ));
       $pack = $em->getRepository("BackendBundle:Pack")->findOneBy(array(
            "id" => $id
       ));
       
       $largo = count($lineapack);
       for($i = 0; $i < $largo; $i++){
           $producto[$i] = $lineapack[$i]->getProducto(); 
       }
       if($lineapack){
           
           $data["status"] ='success';
           $data["code"]=200;
           $data["data"] = $pack;
           $data["data2"] = $lineapack;
           $data["data3"] = $producto;
       }else{
           $data = array(
           "status" => "error",
           "code" => 400,
           "message" => "lineapack not exist"
        );
       }
        
       return $helpers->json($data);
   }
}
