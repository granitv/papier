<?php


namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Facture;
use App\Entity\Ship;
use App\Form\ShipType;
use App\Repository\BasketRepository;
use App\Repository\CategoryCollRepository;
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

    public function __construct(CategoryCollRepository $categoryCollRepository)
    {
        $this->categorys = $categoryCollRepository->findCatIfPlus1();
    }

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
            if($basket->getShip()){
                $shipping = $basket->getShip();
                $shipping->setPrice(0);
                $this->insertInDB($shipping);
            }

        }
        $allOrderByThisUser = $orderR->findBy(['user'=> $this->getUser()]);

        $newTotal=0;
        foreach($allOrderByThisUser as $a){
            if($a->getBasket() !== null){
                $newTotal += $a->getTotalPrice();
            }
        }
        if($basket->getShip() !== null){
            $newTotal += $basket->getShip()->getPrice();
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
        $date=[];
        $today= new \DateTime();
        $nextmonday= clone $today;
        $date[]= $nextmonday->modify('next mon');
        $nextTue= clone $today;
        $date[]= $nextTue->modify('next tue');
        $nextthursday = clone $today;
        $date[]= $nextthursday->modify('next thu');

        usort($date, function($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return $a < $b ? -1 : 1;
        });
        $newDay= clone $date[0];
        $newDay1 = $newDay->modify('+7 days');
        $date[]=$newDay1;

        if($basket->getShip() == null){
            $shipping = new Ship();
            $shipping->setStartDate($date[1]);
            $shipping->setPrice(500);
            $this->insertInDB($shipping);
            $bbship = $basket->setShip($shipping);
            $this->insertInDB($bbship);

        }else{
            if ($basket->getOrder1()->count() !== 0){
                if($basket->getShip() && $basket->getShip()->getPrice() == 0){
                    $shipping = $basket->getShip();
                    foreach ($date as $key => $d){
                        if($d == $shipping->getStartDate()){
                           // dd($key);
                            switch ($key){
                                case 1:
                                    $shipping->setPrice(500);
                                    break;
                                case 2:
                                    $shipping->setPrice(400);
                                    break;
                                case 3:
                                    $shipping->setPrice(300);
                                    break;
                                default:
                                    $shipping->setPrice(500);
                                    break;
                            }
                        }
                    }

                    $this->insertInDB($shipping);
                }
            }else{
                $shipping->setPrice(0);
            }
            $diff = $today->diff($basket->getShip()->getStartDate())->format("%r%a");
            if($diff <= 0){
                $shipping = $basket->getShip();
                $shipping->setStartDate($date[1]);
                $shipping->setPrice(500);
                $this->insertInDB($shipping);
            }

        }

        if($newTotal >= 30000){
            $newTotal=0;
            foreach($allOrderByThisUser as $a){
                if($a->getBasket() !== null){
                    $newTotal += $a->getTotalPrice();
                }
            }
            $basket->setTotal($newTotal);
            $this->insertInDB($basket);
            $shipping = $basket->getShip();
            $shipping->setPrice(0);
            $this->insertInDB($shipping);
        }else{
            $newTotal=0;
            foreach($allOrderByThisUser as $a){
                if($a->getBasket() !== null){
                    $newTotal += $a->getTotalPrice();
                }
            }
            $shipping = $basket->getShip();

            $newTotal += $shipping->getPrice();
            $basket->setTotal($newTotal);
            $this->insertInDB($basket);
        }

        return $this->render('public/pages/basket.html.twig', [
            'categorys' => $this->categorys,
            'intent' => $this->intent,
            'userBasket' => $this->userBasket,
            'address' => $address,
            'thisUserAddress' =>$thisUserAddress,

            'date'=>$date

        ]);
    }

    public function updateShipping($id,UserRepository $userRepository,OrderRepository$orderR){

        if($this->getUser() == null){
            return $this->redirect('/login');
        }
        $user = $userRepository->find($this->getUser());
        if ($user->getBasket() == null) {
            return $this->redirect('/basket');
        }
        $basket = $user->getBasket();

        //testtime
        $date=[];
        $sortDate=[];
        $today= new \DateTime();
        $nextmonday= clone $today;
        $nextmonday->modify('next mon');
        $date[]= $nextmonday;
        $nextTue= clone $today;
        $nextTue->modify('next tue');
        $date[]= $nextTue;
        $nextthursday = clone $today;
        $nextthursday->modify('next thu');
        $date[]= $nextthursday;

        usort($date, function($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return $a < $b ? -1 : 1;
        });
        $newDay= clone $date[0];
        $newDay1 = $newDay->modify('+7 days');
        $date[]=$newDay1;
        if($basket->getShip() == null){
            $shipping = new Ship();
            $shipping->setStartDate($date[1]);
            $shipping->setPrice(500);
            $this->insertInDB($shipping);
            $bbship = $basket->setShip($shipping);
            $this->insertInDB($bbship);
        }else{

            $shipping = $basket->getShip();

            switch ($id){
                case 1:
                    $shipping->setStartDate($date[$id]);
                    $shipping->setPrice(500);
                    break;
                case 2:
                    $shipping->setStartDate($date[$id]);
                    $shipping->setPrice(400);
                    break;
                case 3:
                    $shipping->setStartDate($date[$id]);
                    $shipping->setPrice(300);
                    break;
                default:
                    $shipping->setStartDate($date[1]);
                    break;
            }
            $this->insertInDB($shipping);
            $diff = $today->diff($basket->getShip()->getStartDate())->format("%r%a");
            //  dd($diff);
            if($diff <= 0){
                $shipping = $basket->getShip();
                $shipping->setStartDate($date[1]);
                $shipping->setPrice(500);
                $this->insertInDB($shipping);
            }
            $bbship = $basket->setShip($shipping);
            $this->insertInDB($bbship);
            $allOrderByThisUser = $orderR->findBy(['user'=> $this->getUser()]);
            $newTotal=0;
            foreach($allOrderByThisUser as $a){
                if($a->getBasket() !== null){
                    $newTotal += $a->getTotalPrice();
                }
            }

            if($basket->getShip() !== null){
                $newTotal += $basket->getShip()->getPrice();
            }

            $basket->setTotal($newTotal);
            $this->insertInDB($basket);
        }
        if($newTotal >= 30000){
            $newTotal=0;
            foreach($allOrderByThisUser as $a){
                if($a->getBasket() !== null){
                    $newTotal += $a->getTotalPrice();
                }
            }
            $basket->setTotal($newTotal);
            $this->insertInDB($basket);
            $shipping->setPrice(0);
            $this->insertInDB($shipping);
        }
        return $this->redirect('/basket');
    }

    public function stripeAction(Request $request, UserRepository $userRepository,FactureRepository $factureR){
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
                $factures->setShip($basket->getShip()->getStartDate());
                $factures->setShipPrice($basket->getShip()->getPrice());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($factures);
                $entityManager->persist($basket);
                $entityManager->flush();
                $this->volumes = $user->getOrders();
                $this->addFlash('success', 'Your order has been submitted');
            }
        }
        $id = $factures->getId();
        return $this->redirect('/success/'.$id);
    }
    //test
    public function successPayAction($id, FactureRepository $factureR){
        $selectedFacture = $factureR->findOneBy(['id'=>$id]);

        if($selectedFacture->getUser() !== $this->getUser()){
            return $this->redirect('/');
        }
        return $this->render('public/pages/success.html.twig',[
            'facture' => $selectedFacture,
            "categorys" => $this->categorys
        ]);
    }
    //test
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