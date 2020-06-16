<?php


namespace App\Controller;

use App\Entity\Order;
use App\Entity\UserInfo;
use App\Repository\BasketRepository;
use App\Repository\OrderRepository;
use App\Repository\TypeeRepository;
use App\Repository\UserInfoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class ProfileController extends AbstractController
{
    public function myHistoryAction(OrderRepository $orderR){
        $allOrder = $orderR->findBy(["user"=>$this->getUser()]);
        return $this->render('public/pages/myhistory.html.twig',[
            "allOrder"=>$allOrder
        ]);
    }

    public function editorderAction($id,OrderRepository $orderR,Request $request,TypeeRepository $typeeR){
        $allType = $typeeR->findAll();
        $oneEditOrder = $orderR->findOneBy(["id"=>$id]);
        if($oneEditOrder->getUser() !== $this->getUser() || $oneEditOrder->getBasket() == null){
            return $this->redirect('/myhistory');
        }

        $orderForm = $this->createForm('App\Form\OrderType',$oneEditOrder);
        $orderForm->handleRequest($request);
        if($orderForm->isSubmitted()) {
            $order1 = $orderForm->getData();
            if ($this->getUser() == null) {
                return $this->redirect('/login');
            }
            $order1->setUser($this->getUser());
            $order1->setLastModified(new \DateTime());
            $height = $orderForm->get('height')->getData();
            $width = $orderForm->get('width')->getData();
            $quantity = $orderForm->get('quantity')->getData();
            $typeeInForm = $orderForm->get('typee')->getData();


            $total = ((($height * $width) / 10000) * $typeeInForm->getPrice()) * $quantity * 100;

            $order1->setTotalPrice($total);

            $this->insertInDB($order1);
            //test

            $user = $this->getUser();
            $basket = $user->getBasket();
            $basketPrice = $basket->getTotal();
            $allOrderByThisUser = $orderR->findBy(['user'=> $this->getUser()]);
            $newTotal=0;
            foreach($allOrderByThisUser as $a){
                if($a->getBasket() !== null){
                    $newTotal += $a->getTotalPrice();
                }
            }
                $basket->setTotal($newTotal);
            $this->insertInDB($basket);


            //Test
            $this->addFlash('success', 'Your order has been edited');
            return $this->redirect('/basket');
        }
        return $this->render('public/pages/editorder.html.twig',[
            "oneEditOrder" => $oneEditOrder,
            "editOrderForm"=>$orderForm->createView(),
            "allType"=>$allType
        ]);
    }

    public function updateinfoAction(Request $request,UserInfoRepository $userInfoR,BasketRepository $basketR){
        $user = $this->getUser();
        if($user->getUserinfo() == null){
            $updateinfo = new UserInfo();
        }else{
            $updateinfo = $user->getUserinfo();
        }
        $updateinfoForm = $this->createForm('App\Form\UserInfoType',$updateinfo);
        $updateinfoForm->handleRequest($request);
        if($updateinfoForm->isSubmitted() && $updateinfoForm->isValid()){
            $updateInfo = $updateinfoForm->getData();
            $this->insertInDB($updateInfo);
            $userupp = $user->setUserinfo($updateInfo);
            $this->insertInDB($userupp);
           //teestt

            //testtt
        }
        return $this->render('public/pages/updateinfo.html.twig',[
            'updateinfoForm'=> $updateinfoForm->createView()
        ]);
    }

    public function removeFromDB($removethis){
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($removethis);
        $manager->flush();
    }

    public function insertInDB($insertThis){
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($insertThis);
        $manager->flush();
    }
}