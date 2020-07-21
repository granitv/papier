<?php


namespace App\Controller;

use App\Entity\UserInfo;
use App\Form\OrderType;
use App\Repository\CategoryCollRepository;
use App\Repository\FactureRepository;
use App\Repository\OrderRepository;
use App\Repository\TypeeRepository;
use App\Services\FormsManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Dompdf\Options;


class ProfileController extends AbstractController
{
    public $categorys;
    public function __construct(CategoryCollRepository $categoryCollRepository)
    {
        $this->categorys = $categoryCollRepository->findCatIfPlus1();
    }

    public function myHistoryAction(OrderRepository $orderR){
        $allOrder = $orderR->findBy(["user"=>$this->getUser()]);
        return $this->render('public/pages/myhistory.html.twig',[
            "allOrder"=>$allOrder,
            "categorys" => $this->categorys
        ]);
    }

    public function editorderAction($id,OrderRepository $orderR,Request $request,TypeeRepository $typeeR){
        $allType = $typeeR->findAll();
        $oneEditOrder = $orderR->findOneBy(["id"=>$id]);
        if($oneEditOrder->getUser() !== $this->getUser() || $oneEditOrder->getBasket() == null){
            return $this->redirect('/myhistory');
        }

        $orderForm = $this->createForm(OrderType::class,$oneEditOrder);
        $orderForm->handleRequest($request);
        $coll = $oneEditOrder->getColl();
        if($orderForm->isSubmitted() && $orderForm->isValid()) {
            $order1 = $orderForm->getData();

            if ($this->getUser() == null) {
                return $this->redirect('/login');
            }
            $file = $orderForm->get('file_url')->getData();
            if($file){
                unlink($this->getParameter('uploadPdf').'/'.$oneEditOrder->getFileUrl());
                $newFileName = FormsManager::handleFileUpload($file, $this->getParameter('uploadPdf'));
                $order1->setFileUrl($newFileName);
            }
            $order1->setUser($this->getUser());
            $order1->setLastModified(new \DateTime());
            $height = $orderForm->get('height')->getData();
            $width = $orderForm->get('width')->getData();
            $quantity = $orderForm->get('quantity')->getData();
            $typeeInForm = $orderForm->get('typee')->getData();
            $coll = $oneEditOrder->getColl();
            if ($coll == null) {
                $pricePerM2 = $typeeInForm->getPrice()+10;
            }else{
                $pricePerM2 = $typeeInForm->getPrice();
            }
            $total = ((($height * $width) / 10000) * $pricePerM2) * $quantity * 100;
            $order1->setTotalPrice($total);
            $this->insertInDB($order1);
            $this->addFlash('success', 'Your order has been edited');
            return $this->redirect('/basket');
        }
        return $this->render('public/pages/editorder.html.twig',[
            "oneEditOrder" => $oneEditOrder,
            "editOrderForm"=>$orderForm->createView(),
            "allType"=>$allType,
            'coll'=>$coll,
            "categorys" => $this->categorys

        ]);
    }

    public function updateinfoAction(Request $request){
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
            $this->addFlash('success', 'Your address has been edited');
            return $this->redirect('/basket');
        }
        return $this->render('public/pages/updateinfo.html.twig',[
            'updateinfoForm'=> $updateinfoForm->createView(),
            "categorys" => $this->categorys
        ]);
    }

    public function factureAction($id,FactureRepository $factureR){
        $selectedFacture = $factureR->findOneBy(['id'=>$id]);
        $user = $this->getUser();
        if($user !== $selectedFacture->getUser()){
            return $this->redirect('/myhistory');
        }

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('public/pages/facture.html.twig', [
            'facture' => $selectedFacture
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'landscape'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        $fatureID = $selectedFacture->getId();
        // Output the generated PDF to Browser (force download)
        $dompdf->stream("facture-$fatureID.pdf", [
            "Attachment" => true
        ]);
        return $this->render('public/pages/facture.html.twig', [
            'facture' => $selectedFacture,
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