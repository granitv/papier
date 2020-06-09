<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function indexAction(){
        return $this->render('public/pages/home.html.twig');
    }

    public function papierpeintAction(){
        return $this->render('public/pages/papierpeint.html.twig');
    }

    public function murtenduAction(){
        return $this->render('public/pages/murtendu.html.twig');
    }

    public function collectionbaseAction(){
        return $this->render('public/pages/collectionbase.html.twig');
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
}