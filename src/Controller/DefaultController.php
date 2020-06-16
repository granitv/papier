<?php
namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Order;
use App\Repository\BasketRepository;
use App\Repository\CollRepository;
use App\Repository\TypeeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{

    public function indexAction(){

            return $this->render('public/pages/home.html.twig', [

            ]);
    }

    public function papierpeintAction(){
        return $this->render('public/pages/papierpeint.html.twig');
    }

    public function murtenduAction(){
        return $this->render('public/pages/murtendu.html.twig');
    }

    public function collectionbaseAction(CollRepository $collR){
        $allColl = $collR->findAll();
        return $this->render('public/pages/collectionbase.html.twig',[
            "allColl" => $allColl
        ]);
    }

    public function collectionpersonnaliserAction(){
        return $this->render('public/pages/collectionpersonnaliser.html.twig');
    }

    public function aboutAction(){
        return $this->render('public/pages/about.html.twig');
    }

    public function portfolioAction(){
        return $this->render('public/pages/portfolio.html.twig');
    }

    public function contactAction(){
        return $this->render('public/pages/contact.html.twig');
    }

    public function registerAction(){
        return $this->render('public/pages/register.html.twig');
    }

    public function collectionAction($id,CollRepository $collR,Request $request,UserRepository $userRepository,BasketRepository $basketRepository, CollRepository $collRepository,TypeeRepository $typeeR){
$allType = $typeeR->findAll();
        $coll = $collR->find($id);
        $order= new Order();
        $orderForm = $this->createForm('App\Form\OrderType',$order);
        $orderForm->handleRequest($request);
        if($orderForm->isSubmitted()){
                $order1 = $orderForm->getData();
                if($this->getUser() == null){
                    return $this->redirect('/login');
                }
                $order1->setUser($this->getUser());
                $order1->setColl($coll);

                $height = $orderForm->get('height')->getData();
                $width = $orderForm->get('width')->getData();
                $quantity = $orderForm->get('quantity')->getData();
                $typeeInForm = $orderForm->get('typee')->getData();


                $total = ((($height*$width)/10000)*$typeeInForm->getPrice())*$quantity*100;

                $order1->setTotalPrice($total);
                $order1->setCreatedAt(new \DateTime());
                $this->insertInDB($order1);
                $this->addFlash('success','Your order has been submitted');

            //test
    //prova

            $user = $userRepository->find($this->getUser());
            if ($user->getBasket() == null){
                $basket = new Basket();
                $user->setBasket($basket);
            }
            $basket = $user->getBasket();
            $prices = [];
            $basket->addOrder1($order1);
            $basket->setUser($this->getUser());
            //    dump($basket->getVolumes()->count());
            $this->insertInDB($basket);
            $this->userBasket = $basketRepository->findBy(['id' => $basket->getId() ]);
            foreach ($this->userBasket as $basket){
                foreach ($basket->getOrder1() as $orderss){
                    array_push($prices, $orderss->getTotalPrice());
                }
            }

            if ($basket->getOrder1()->count() == 0){
                $basket->setTotal(0);
            }else{
                $basket->setTotal(array_sum($prices));
                $this->insertInDB($basket);
            }

            return $this->redirect('/basket');

        }
    //prova
        return $this->render('public/pages/collection.html.twig',[
            'coll'=>$coll,
            "orderForm" => $orderForm->createView(),
            "allType"=>$allType
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