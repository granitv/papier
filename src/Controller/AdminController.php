<?php

namespace App\Controller;


use App\Entity\Coll;
use App\Entity\Image;
use App\Entity\Order;
use App\Entity\Slider;
use App\Form\CollType;
use App\Form\ImageType;
use App\Form\SliderType;
use App\Repository\CollRepository;
use App\Repository\ImageRepository;
use App\Repository\SliderRepository;
use App\Repository\UserRepository;
use App\Services\FormsManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class AdminController extends AbstractController
{
    public function homeAction(UserRepository $userR,CollRepository $collR){

        $allUsers = $userR->findAll();
        $allColls = $collR->findAll();
        return $this->render( 'admin/pages/home.html.twig' ,[
            "allUsers"=>$allUsers,
            "allColls"=>$allColls,

        ] );
    }

    public function addcollectionBaseAction(Request $request,CollRepository $collR,ImageRepository $imageR){
        $coll = new Coll();
        $collForm = $this->createForm(CollType::class, $coll);
        $collForm->handleRequest($request);
        if ($collForm->isSubmitted() && $collForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($coll);
            $entityManager->flush();
            return $this->redirect('/admin');
        }

        //test
        $img = new Image();
        $imgForm = $this->createForm(ImageType::class, $img);
        $imgForm->handleRequest($request);
        if ($imgForm->isSubmitted() && $imgForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($img);
            $entityManager->flush();
            return $this->redirect('/admin');
        }
        //test

        return $this->render( 'admin/pages/addcollectionBase.html.twig',[
            "collForm"=>$collForm->createView(),
            "imgForm"=>$imgForm->createView()
        ]);
    }
    public function addcollectionpersonnaliserAction(){

        return $this->render( 'admin/pages/addcollectionpersonnaliserAction.html.twig');
    }
    public function sliderAction($id,SliderRepository $sliderR,Request $request){
        $allSilder = $sliderR->findAll();

     if($id == 0){
         $newSlideImge = new Slider();
     }else{
         $newSlideImge = $sliderR->findOneBy([
             "id"=>$id
         ]);
     }

        $slideForm = $this->createForm(SliderType::class,$newSlideImge);
        $slideForm->handleRequest($request);
        if($slideForm->isSubmitted() && $slideForm->isValid()){
            $newImge = $slideForm->getData();
            $file = $slideForm->get('img_name')->getData();

            if($file){
                if($id != 0){
                    unlink($this->getParameter('uploads').'/'.$newImge->getImgName());
                }
                $newfilename = FormsManager::handleFileUpload($file,$this->getParameter('uploads'));
                $newImge->setImgName($newfilename);
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($newImge);
            $manager->flush();
            $this->addFlash('success','Slider Image added succesfuly');
            return $this->redirect('/admin/slider/0');

        }
        return $this->render( 'admin/pages/slider.html.twig',[
            "allSilder" => $allSilder,
            "sliderForm" => $slideForm->createView(),
            "id"=>$id
        ]);
    }

    public function delete($repository,$id, $redirect){
        $something = $this->getDoctrine()
                ->getRepository('App\Entity\\' . $repository)
                ->findOneBy(["id"=>$id]);
        $em = $this->getDoctrine()->getManager();
        if($redirect == 'slider'){
            unlink($this->getParameter('uploads').'/'.$something->getImgName());
        }
        $em->remove($something);
        $em->flush();

            $this->addFlash('success', $repository . ' deleted successfully');
            // Suggestion: add a message in the flashbag

            // Redirect to the table page
        if($redirect == 'slider'){
            return $this->redirect('/admin/'.$redirect.'/0');
        }else{
            return $this->redirect('/admin/'.$redirect);
        }

            //http://127.0.0.1:8000/admin/delete/Comment/8/adminHome


    }
}