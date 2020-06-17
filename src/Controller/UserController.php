<?php


namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Facture;
use App\Repository\BasketRepository;
use App\Repository\CollRepository;
use App\Repository\FactureRepository;
use App\Repository\OrderRepository;
use App\Repository\UserInfoRepository;
use App\Repository\UserRepository;
use Plasticbrain\FlashMessages\FlashMessages;
use Stripe\Exception\CardException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class UserController extends AbstractController
{
    public $session;
    public $intent;
    public $categorys;
    public $userBasket;
    public $basket;
    public $fail;
    public $volumes;
    public $lib;

    public function basketAction(OrderRepository $orderR,UserRepository $userRepository,BasketRepository $basketRepository){
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


        return $this->render('public/pages/basket.html.twig', [
            'categorys' => $this->categorys,
            'intent' => $this->intent,
            'userBasket' => $this->userBasket,
            'address' => $address,
            'thisUserAddress' =>$thisUserAddress
        ]);
    }

    public function stripeAction(Request $request, UserRepository $userRepository){
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

                $factures->setFullname($user->getUserinfo()->getFullname());
                $factures->setCountry($user->getUserinfo()->getCountry());
                $factures->setStreet($user->getUserinfo()->getStreet());
                $factures->setPostcode($user->getUserinfo()->getPostcode());
                $factures->setCity($user->getUserinfo()->getCity());
                $factures->setTel($user->getUserinfo()->getTel());
                $factures->setNote($user->getUserinfo()->getNote());
                $factures->setCreatedAt(new \DateTime());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($factures);
                $entityManager->persist($basket);
                $entityManager->flush();
                $this->volumes = $user->getOrders();
                $this->addFlash('success', 'Your order has been submitted');
            }
        }
        return $this->redirect('/myhistory');
    }

    public function deleteOrder($id,OrderRepository $orderR){
        $selectOrder = $orderR->findOneBy(["id"=>$id]);

        if($this->getUser() == $selectOrder->getUser() && $selectOrder->getBasket() !== null){

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