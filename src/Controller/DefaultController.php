<?php
namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\BasketRepository;
use App\Repository\CategoryCollRepository;
use App\Repository\CollRepository;
use App\Repository\SliderRepository;
use App\Repository\TypeeRepository;
use App\Repository\UserRepository;
use App\Services\FormsManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    public $categorys;

    public function __construct(CategoryCollRepository $categoryCollRepository)
    {
        $this->categorys = $categoryCollRepository->findCatIfPlus1();
    }

    public function indexAction(SliderRepository $sliderR){
        $allSlideImg = $sliderR->findAll();
            return $this->render('public/pages/home.html.twig', [
                "allSlideImg"=>$allSlideImg,
                "categorys" => $this->categorys
            ]);
    }

    public function categoryAction($id,CollRepository $collR,CategoryCollRepository $categoryCollR){
        $allColl = $collR->findAll();
        $cat = $categoryCollR->find($id);
        $selected=[];
      foreach( $allColl as $coll){
          $allcat = $coll->getCategoryColls();
          foreach($allcat as $c){
              if($c == $cat){
                  $selected[]= $coll;
              }
          }
      }
        return $this->render('public/pages/collectionbase.html.twig',[
            "allColl" => $selected,
            "categorys" => $this->categorys,
            "cat"=>$cat
        ]);
    }

    public function papierpeintAction(){
        return $this->render('public/pages/papierpeint.html.twig',[

            "categorys" => $this->categorys
        ]);
    }

    public function collectionbaseAction(CollRepository $collR){
        $allColl = $collR->findAll();
        return $this->render('public/pages/collectionbase.html.twig',[
            "allColl" => $allColl,
            "categorys" => $this->categorys
        ]);
    }

    public function collectionpersonnaliserAction(Request $request,BasketRepository $basketRepository, UserRepository $userRepository,
TypeeRepository $typeeRepository){

        $allType = $typeeRepository->findAll();
        $collBase = new Order();
        $collBaseForm = $this->createForm(OrderType::class,$collBase);
        $collBaseForm->handleRequest($request);
        if($collBaseForm->isSubmitted() && $collBaseForm->isValid()){
            if($this->getUser() == null){
                return $this->redirect('/login');
            }
         $collBase = $collBaseForm->getData();
         $file = $collBaseForm->get('file_url')->getData();
         if($file){
             $newFileName = FormsManager::handleFileUpload($file, $this->getParameter('uploadPdf'));
             $collBase->setFileUrl($newFileName);
         }
            $height = $collBaseForm->get('height')->getData();
            $width = $collBaseForm->get('width')->getData();
            $quantity = $collBaseForm->get('quantity')->getData();
            $typeeInForm = $collBaseForm->get('typee')->getData();


            $total = ((($height*$width)/10000)*$typeeInForm->getPrice())*$quantity*100;
            $collBase->setUser($this->getUser());
            $collBase->setCreatedAt(new \DateTime());
            $collBase->setTotalPrice($total);
            $this->insertInDB($collBase);
            $this->addFlash('success','Order added successfully');

            $user = $userRepository->find($this->getUser());
            if ($user->getBasket() == null){
                $basket = new Basket();
                $user->setBasket($basket);
            }
            $basket = $user->getBasket();
            $prices = [];
            $basket->addOrder1($collBase);
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
        return $this->render('public/pages/collectionpersonnaliser.html.twig',[
            'collBaseForm'=> $collBaseForm->createView(),
            'allType'=>$allType,
            "categorys" => $this->categorys
        ]);
    }

    public function aboutAction(){
        return $this->render('public/pages/about.html.twig',[

            "categorys" => $this->categorys
        ]);
    }

    public function portfolioAction(){
        return $this->render('public/pages/portfolio.html.twig',[

            "categorys" => $this->categorys
        ]);
    }

    public function contactAction(){
        return $this->render('public/pages/contact.html.twig',[
                "categorys" => $this->categorys
        ]);
    }

    public function registerAction(){
        return $this->render('public/pages/register.html.twig',[

            "categorys" => $this->categorys
        ]);
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
            $user = $userRepository->find($this->getUser());
            if ($user->getBasket() == null){
                $basket = new Basket();
                $user->setBasket($basket);
            }
            $basket = $user->getBasket();
            $basket->addOrder1($order1);
            $basket->setUser($this->getUser());
            $this->insertInDB($basket);
            return $this->redirect('/basket');
        }
        return $this->render('public/pages/collection.html.twig',[
            'coll'=>$coll,
            "orderForm" => $orderForm->createView(),
            "allType"=>$allType,
            "categorys" => $this->categorys
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