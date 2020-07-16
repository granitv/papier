<?php

namespace App\Controller;


use App\Entity\CategoryColl;
use App\Entity\Coll;
use App\Entity\Image;
use App\Entity\Order;
use App\Entity\Slider;
use App\Entity\Typee;
use App\Entity\User;
use App\Form\AdminCollType;
use App\Form\AdminUserType;
use App\Form\CategoryType;
use App\Form\CollType;
use App\Form\ImageType;
use App\Form\SliderType;
use App\Form\TypeeFormType;
use App\Form\UserType;
use App\Repository\CategoryCollRepository;
use App\Repository\CollRepository;
use App\Repository\ImageRepository;
use App\Repository\SliderRepository;
use App\Repository\TypeeRepository;
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
        if($redirect == 'collBaseCategorys'){
            unlink($this->getParameter('uploads').'/'.$something->getImage());
        }
        /*  if($redirect == 'collBaseAdmin'){
              unlink($this->getParameter('uploadPdf').'/'.$something->getFileUrl());
          }*/
        $em->remove($something);
        $em->flush();

        $this->addFlash('success', $repository . ' deleted successfully');
        if($redirect == 'slider' || $redirect == 'allUsers' || $redirect == 'collBaseAdmin' || $redirect == 'collBaseCategorys'
        || $redirect == 'typePapier'){
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
    public function collBaseAdminAction($id,CollRepository $collR,Request $request,ImageRepository $imageR){
        $allColls = $collR->findAll();
        if($id == 0) {
            $newcoll = new Coll();
        }else{
            $newcoll = $collR->find($id);
            $cat2 = $newcoll->getCategoryColls();
            foreach ($cat2 as $ccc){
                $add=  $ccc->removeColl($newcoll);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($add);
                $manager->flush();
            }
        }
        $collBaseForm = $this->createForm(AdminCollType::class,$newcoll);
        $collBaseForm->handleRequest($request);
        if($collBaseForm->isSubmitted() && $collBaseForm->isValid()){
            $newCollData = $collBaseForm->getData();
            $categorys = $collBaseForm->get('Categorycolls')->getData();
            foreach ($categorys as $cat){
                $add = $cat->addColl($newcoll);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($add);
            }

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
    public function collBaseImageAction($coll,$id,Request $request,ImageRepository $imageR,CollRepository $collR){
        /*image start*/
        if($id == 0) {
            $newImage = new Image();
        }else{
            $newImage = $imageR->find($id);
        }
        if($coll != 0){
            $newImage->setColl($collR->find($coll));
        }
        $newImgForm = $this->createForm(ImageType::class,$newImage);
        $newImgForm->handleRequest($request);
        if($newImgForm->isSubmitted() && $newImgForm->isValid()){
            $newImgData = $newImgForm->getData();
            $file = $newImgForm->get('url')->getData();
            if($file){
                if($id != 0){
                    unlink($this->getParameter('uploads').'/'.$newImgData->getUrl());
                }
                $newFileName = FormsManager::handleFileUpload($file,$this->getParameter('uploads'));
                $newImgData->setUrl($newFileName);
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($newImgData);
            $manager->flush();
            $this->addFlash('success','Img added Successfuly');
            return $this->redirect('/admin/collBaseImage/'.$newImage->getId().'/0');
        }
        /*image end*/
        return $this->render('admin/pages/collBaseImage.html.twig',[
            "imgForm"=>$newImgForm->createView(),
            "newImage"=>$newImage
        ]);
    }
    public function collBaseCategorysAction($id,Request $request,CategoryCollRepository $categoryCollR){
        if($id == 0){
            $newCategory = new CategoryColl();
        }else{
            $newCategory = $categoryCollR->find($id);
        }
        $allCAtegorys = $categoryCollR->findAll();
        $categoryForm = $this->createForm(CategoryType::class,$newCategory);
        $categoryForm->handleRequest($request);
        if($categoryForm->isSubmitted() && $categoryForm->isValid()){
            $newCatData = $categoryForm->getData();
            $file = $categoryForm->get('image')->getData();
            if($file){
                if($id != 0){
                    unlink($this->getParameter('uploads').'/'.$newCatData->getImage());
                }
                $newName = FormsManager::handleFileUpload($file, $this->getParameter('uploads'));
                $newCatData->setImage($newName);
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($newCatData);
            $manager->flush();
            $this->addFlash('success','Category Added Successfuly');
            return $this->redirect('/admin/collBaseCategorys/0');

        }
        return $this->render('admin/pages/collBaseCategorys.html.twig',[
                "categoryForm"=> $categoryForm->createView(),
            "allCategorys"=>$allCAtegorys
        ]);
    }
    public function typePapierAction($id,TypeeRepository $typeeR,Request $request){
        $allTypee = $typeeR->findAll();
        if($id == 0){
            $newType = new Typee();
        }else{
            $newType = $typeeR->find($id);
        }
        $newTypeForm = $this->createForm(TypeeFormType::class,$newType);
        $newTypeForm->handleRequest($request);
        if($newTypeForm->isSubmitted() && $newTypeForm->isValid()){
            $newTypeData = $newTypeForm->getData();
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($newTypeData);
            $manager->flush();
            $this->addFlash('success','Type added successfuly');
            return $this->redirect('/admin/typePapier/0');
        }
        return $this->render('admin/pages/typePapier.html.twig',[
            "allTypee"=>$allTypee,
            "typeeForm" => $newTypeForm->createView()
        ]);
    }
}