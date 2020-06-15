<?php


namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProfileController extends AbstractController
{
    public function myHistoryAction(OrderRepository $orderR){
        $allOrder = $orderR->findBy(["user"=>$this->getUser()]);
        return $this->render('public/pages/myhistory.html.twig',[
            "allOrder"=>$allOrder
        ]);
    }
}