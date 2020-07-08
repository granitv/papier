<?php

namespace App\Controller;


use App\Entity\Coll;
use App\Entity\Image;
use App\Entity\Order;
use App\Entity\Slider;
use App\Entity\User;
use App\Form\AdminCollType;
use App\Form\AdminUserType;
use App\Form\CollType;
use App\Form\ImageType;
use App\Form\SliderType;
use App\Form\UserType;
use App\Repository\CollRepository;
use App\Repository\ImageRepository;
use App\Repository\SliderRepository;
use App\Repository\UserRepository;
use App\Services\FormsManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class AdminController extends AbstractController
{
    public function homeAction(CollRepository $collR){
        $allColls = $collR->findAll();
        return $this->render( 'admin/pages/home.html.twig' ,[
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
            ->find($id);
        $em = $this->getDoctrine()->getManager();



        if($redirect == 'slider' ){
            unlink($this->getParameter('uploads').'/'.$something->getImgName());
        }
        /*  if($redirect == 'collBaseAdmin'){
              unlink($this->getParameter('uploadPdf').'/'.$something->getFileUrl());
          }*/
        $em->remove($something);
        $em->flush();

        $this->addFlash('success', $repository . ' deleted successfully');
        if($redirect == 'slider' || $redirect == 'allUsers' || $redirect == 'collBaseAdmin'){
            return $this->redirect('/admin/'.$redirect.'/0');
        }else{
            return $this->redirect('/admin/'.$redirect);
        }
    }

    public function allUsersAction($id,UserRepository $userR,Request $request){
            $allUsers = $userR->findAll();
            if($id == 0 ){
                $newUser = new User();
                $userForm = $this->createForm(UserType::class,$newUser);
            }else{
                $newUser = $userR->find($id);
                $userForm = $this->createForm(AdminUserType::class,$newUser);
            }

            $userForm->handleRequest($request);
            if($userForm->isSubmitted() && $userForm->isValid()){
                $newUserData = $userForm->getData();
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($newUserData);
                $manager->flush();
                $this->addFlash('success','User Added Succesfuly');
                return $this->redirect('/admin/allUsers/0');
            }
        return $this->render('admin/pages/allusers.html.twig',[
            "allUsers"=>$allUsers,
            "userForm"=>$userForm->createView()
        ]);
    }
    public function collBaseAdminAction($id,CollRepository $collR,Request $request){
        $allColls = $collR->findAll();
        if($id == 0) {
            $newcoll = new Coll();
        }else{
            $newcoll = $collR->find($id);
        }
        $collBaseForm = $this->createForm(AdminCollType::class,$newcoll);
        $collBaseForm->handleRequest($request);
        if($collBaseForm->isSubmitted() && $collBaseForm->isValid()){
            $newCollData = $collBaseForm->getData();
            $file = $collBaseForm->get('file_url')->getData();
            if($file){
                if($id != 0){
                    unlink($this->getParameter('uploadPdf').'/'.$newCollData->getFileUrl());
                }
                $newFileName = FormsManager::handleFileUpload($file, $this->getParameter('uploadPdf'));
                $newCollData->setFileUrl($newFileName);
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($newCollData);
            $manager->flush();
            $this->addFlash('success','Collection added Successfuly');
            return $this->redirect('/admin/collBaseAdmin/0');
        }
    return $this->render('admin/pages/collBase.html.twig',[
        "allColls"=>$allColls,
        "collBaseForm"=> $collBaseForm->createView()
    ]);
    }
}