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
use BackendBundle\Entity\Linea;
use BackendBundle\Entity\Reserva;

use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;


class LineaController extends Controller {
    
    //corregido :P 
     public function detailAction (Request $request , $id = null){
        
       $helpers = $this->get("app.helpers");
       $em = $this->getDoctrine()->getManager();
       
       $linea = $em->getRepository("BackendBundle:Linea")->findBy(array(
            "reserva" => $id
       ));
       $reserva = $em->getRepository("BackendBundle:Reserva")->findOneBy(array(
            "id" => $id
       ));
       
       if($linea){
           
           $data["status"] ='success';
           $data["code"]=200;
           $data["data"] = $reserva;
           $data["data1"] = $linea;
           
       }else{
           $data = array(
           "status" => "error",
           "code" => 400,
           "message" => "Reservas con sus lineas not exist"
        );
       }
       return $helpers->json($data);
   }

   public function indeoAction(Request $request){

        $helpers = $this->get("app.helpers");
        $po = 'Fabio';
        $pe = 'Hola '.$po.' dasds';
        $message = \Swift_Message:: newInstance()
            ->setSubject('WENA QLO')
            ->setFrom('ventas.jabon@gmail.com')
            ->setTo('fabio.alderete00@gmail.com')
            ->setBody(
                            $this->renderView('Emails/email.html.twig',
                            array('id' => $po ,'contenido' => '<br><p>'.$pe.' </p>','text/html')//,
                            //array('contenido' => $pe)

                            ),'text/html'
                        )
        ;
        $this->get('mailer')->send($message);
        $data = array(
           "status" => "imba",
           "code" => 200,
           "message" => "correo enviado"
        );
        //var_dump($message);
        return $helpers->json($data);
   }

}
