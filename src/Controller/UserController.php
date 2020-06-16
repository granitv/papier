<?php


namespace App\Controller;

use App\Entity\Basket;

use App\Entity\Facture;
use App\Entity\User;

use App\Entity\UserInfo;
use App\Repository\BasketRepository;

use App\Repository\CollRepository;
use App\Repository\FactureRepository;
use App\Repository\OrderRepository;
use App\Repository\UserInfoRepository;
use App\Repository\UserRepository;
use Plasticbrain\FlashMessages\FlashMessages;
use Doctrine\DBAL\Schema\View;

use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;

class UserController extends AbstractController
{
    public $session;
    public $intent;
    public $categorys;
    public $userBasket;
    public $intentSecret;
    public $paymethod;
    public $basket;
    public $fail;
    public $volumes;
    public $lib;
    public $userLib;
    public $inLib;


    public function addBasket(UserRepository $userRepository,BasketRepository $basketRepository, CollRepository $collRepository, $id){
        $coll = $collRepository->find($id);
        $user = $userRepository->find($this->getUser());
        if ($user->getBasket() == null){
            $basket = new Basket();
            $user->setBasket($basket);
        }
        $basket = $user->getBasket();
        $prices = [];
        $basket->addColl($coll);
        $basket->setUser($this->getUser());
        //    dump($basket->getVolumes()->count());
        $this->insertInDB($basket);
        $this->userBasket = $basketRepository->findBy(['id' => $basket->getId() ]);
        foreach ($this->userBasket as $basket){
            foreach ($basket->getColl() as $colls){
                array_push($prices, $colls->getPrice());
            }
        }
        if ($basket->getColl()->count() == 0){
            $basket->setTotal(0);
        }else{
            $basket->setTotal(array_sum($prices));
            $this->insertInDB($basket);
        }
        return $this->render('public/pages/basket.html.twig', [
            'userBasket' => $this->userBasket
        ]);
    }

    public function basketAction(OrderRepository $orderR,UserRepository $userRepository,BasketRepository $basketRepository, CollRepository $collRepository,UserInfoRepository $userInfoR,
                                 Request $request){
        if($this->getUser() == null){
            return $this->redirect('/login');
        }
        $user = $userRepository->find($this->getUser());
        if ($user->getBasket() == null) {
            $basket = new Basket();
            $user->setBasket($basket);
        }
        $basket = $user->getBasket();

        if ($basket->getOrder1()->count() == 0){
            $basket->setTotal(0);
        }
        $allOrderByThisUser = $orderR->findBy(['user'=> $this->getUser()]);

        $newTotal=0;
        foreach($allOrderByThisUser as $a){
            if($a->getBasket() !== null){
                $newTotal += $a->getTotalPrice();
            }
        }
        $basket->setTotal($newTotal);
        $this->insertInDB($basket);

        $this->userBasket = $basketRepository->findOneBy(['id' => $basket->getId() ]);
//test

        $this->basket = $basketRepository->findOneBy(['id' => $basket->getId()]);
        $basketPrice = $this->basket;
        $user = $this->getUser();
        if($basketPrice->getUser() !== $this->getUser()){
            return $this->redirect('/basket');
        }
        $thisUserAddress = null;
        if($user->getUserinfo() == null){
            $address = false;
        }else{
            $thisUserAddress = $user->getUserinfo();
            $address = true;
        }
        $total = $basketPrice->getTotal();

        //test
        return $this->render('public/pages/basket.html.twig', [
            'categorys' => $this->categorys,
            'intent' => $this->intent,
            'userBasket' => $this->userBasket,
            'address' => $address,
            'thisUserAddress' =>$thisUserAddress
        ]);
    }
    public function goToPaymentAction(UserInfoRepository $userInfoR,
        Request $request, BasketRepository $basketRepository, $id)
    {
        $this->basket = $basketRepository->findOneBy(['id' => $id]);
        $basketPrice = $this->basket;
        $user = $this->getUser();
        if($basketPrice->getUser() !== $this->getUser()){
            return $this->redirect('/basket');
        }
        if($basketPrice->getUserinfo() == null){
            //Test
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
                    $baskupp = $basketPrice->setUserinfo($updateInfo);
                    $this->insertInDB($baskupp);
                    //teestt
                    return $this->render('public/pages/payment.html.twig', [
                        'categorys' => $this->categorys,
                        'intent' => $this->intent,
                        'userBasket' => $this->basket
                    ]);
                    //testtt
                }
                return $this->render('public/pages/updateinfo.html.twig',[
                    'updateinfoForm'=> $updateinfoForm->createView()
                ]);
            //test
            //return $this->redirect('/updateinfo');
        }
        $total = $basketPrice->getTotal();
        return $this->render('public/pages/payment.html.twig', [
            'categorys' => $this->categorys,
            'intent' => $this->intent,
            'userBasket' => $this->basket,

        ]);
    }
    public function stripeAction(Request $request, $id, BasketRepository $basketRepository, UserRepository $userRepository, FactureRepository $factureRepository){
        $user = $userRepository->find($this->getUser());
        $this->fail = false;
        $basket = $user->getBasket();
        $basketPrice = $basket->getTotal();
        $total = $basketPrice;
        \Stripe\Stripe::setApiKey('sk_test_oZ41JW4bx1BzklgENDZAChFP00Spk89Qzt');
        $json_str = file_get_contents('php://input');
        $json_obj = json_decode($json_str);
        try{
            $intent =  \Stripe\PaymentIntent::create([

                'payment_method_data' => [
                    'type' => 'card',
                    'card' => ['token' => $request->request->get('stripeToken')],
                ],
                'amount' => $total,
                'currency' => 'usd',
                'confirm' => true,
            ]);
        } catch( CardException $e){
            $this->fail = true;
            $flsh = new FlashMessages();
            $flsh->error('Votre payement a été refusé');
            $flsh->display();

            return $this->render('public/pages/home.html.twig', [
                'categorys'=> $this->categorys,

            ]);

        }

        if ($this->fail != true) {
            if ($intent['status'] === 'succeeded') {
                $factures = new Facture();
                $orderInbasket = $basket->getOrder1();
                foreach ($orderInbasket as $oneOrder) {

                    $factures->addOrder1($oneOrder);
                    $basket->removeOrder1($oneOrder);
                }
                $factures->setTotal($basket->getTotal());
                $factures->setUser($user);
                //	fullname	country	street	postcode	city	tel	note
                $factures->setFullname($user->getUserinfo()->getFullname());
                $factures->setCountry($user->getUserinfo()->getCountry());
                $factures->setStreet($user->getUserinfo()->getStreet());
                $factures->setPostcode($user->getUserinfo()->getPostcode());
                $factures->setCity($user->getUserinfo()->getCity());
                $factures->setTel($user->getUserinfo()->getTel());
                $factures->setNote($user->getUserinfo()->getNote());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($factures);
                $entityManager->persist($basket);
                $entityManager->flush();
                $this->volumes = $user->getOrders();
            }
        }
        return $this->render('public/pages/home.html.twig');
    }
    public function libAction(UserRepository $userRepository){

        $user = $userRepository->find($this->getUser());
        $this->volumes = $user->getVolumes();
        return $this->render('library.html.twig',
            [
                'categorys' => $this->categorys,
                'volumes' => $this->volumes
            ]);
    }
    public function comicAction(volumeRepository $volumeRepository, $id, UserRepository $userRepository){
        $user = $userRepository->find($this->getUser());
        $volume = $volumeRepository->findOneBy([ "id" => $id]);
        $userWiVolume =  $volume->getUser();
// 1- 5
        //3 - 5
        if(sizeof($userWiVolume)){
            foreach ($userWiVolume as $uv){
                if($uv == $user){

                    $this->inLib = true;

                }else{
                    $this->inLib = false;
                }
            }
        }else{
            if($userWiVolume === $user){

                $this->inLib = true;

            }else{
                $this->inLib = false;
            }
        }


        /*   $this->lib = $user->getVolumes();
           $check = false;
          foreach ($this->lib as $library) {
              foreach ($volinlib as $vol) {
                  if ($vol == $library){
                      dd(e)
                  }
              }
          } */







        //  dd($volume->getUser());



        return $this->render('public/pages/comic.html.twig',
            ['categorys' => $this->categorys,
                'volume' => $volume,
                'lib'  => $this->userLib,
                'inLib' => $this->inLib
            ]);



    }

    public function addToLib(UserRepository $userRepository, VolumeRepository $volumeRepository, $id){

        $volume= $volumeRepository->findBy(['id' => $id]);
        $volumeAdded = $volume['0'];


        $user = $userRepository->find($this->getUser());
        $factures = new Factures();
        $factures->setTotal(0);
        $factures->addVolumesFacture($volumeAdded);
        $factures->setUsersFactures($user);
        $user->addVolume($volumeAdded);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($factures);
        $entityManager->persist($user);

        $entityManager->flush();
        return $this->render('library.html.twig', [
            'categorys' => $this->categorys,
            'volumes' => $user->getVolumes()
        ]);

    }
    public function deleteOrder($id,OrderRepository $orderR,UserRepository $userR,BasketRepository $basketR){
        $selectOrder = $orderR->findOneBy(["id"=>$id]);

        if($this->getUser() == $selectOrder->getUser() && $selectOrder->getBasket() !== null){
            //   $user = $userRepository->find($this->getUser());
            $user = $this->getUser();
            $basket = $user->getBasket();
            $basketPrice = $basket->getTotal();
            $basket->setTotal($basketPrice-$selectOrder->getTotalPrice());
            $this->insertInDB($basket);
            $this->removeFromDB($selectOrder);
        }
        return $this->redirect('/basket');
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