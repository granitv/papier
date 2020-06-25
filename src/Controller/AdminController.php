<?php

namespace App\Controller;


use App\Entity\Coll;
use App\Entity\Image;
use App\Entity\Order;
use App\Form\CollType;
use App\Form\ImageType;
use App\Repository\CollRepository;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
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
}