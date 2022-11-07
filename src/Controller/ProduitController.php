<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitsRepository;
use App\Repository\StockRepository;
use App\Form\ProduitFormType;
use App\Form\StockFormType;
use App\Entity\Produits;
class ProduitController extends AbstractController
{
    /**
     * @Route("/Produit/Afficher", name="produitAfficher")
     */
    public function Affichage(ProduitsRepository $prds): Response
    {
            $produits=$prds->findALl();
        return $this->render('produit/Affichage.html.twig', [
            'produits' => $produits,
        ]);
    }

    /**
     * @Route("Produit/Ajout",name="ProduitAjout")
     */
    public function Ajout(Request $request){
        $prd=new Produits();
        $form=$this->createForm(ProduitFormType::class, $prd);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            // l'upload
            $files=$form->get('photo')->getData();
            // on va récupérer le nom de piece jointe 
            // getClientOriginalName() <==>$_FILES['photo']['name'] ==>retourne le name de piece jointe sans extension 
            $img=pathinfo($files->getClientOriginalName(),PATHINFO_FILENAME);
            $extension=$files->guessExtension();// l'extension de piece jointe sans .
            if($extension=='jpeg' or $extension=='png'){
           $photo=$img.'.'.$extension;
            //return new response('piece jointe '.$photo);
            // Connexion avec Doctrine
            $cnx=$this->getDoctrine()->getManager();
            $prd->setPhoto($photo);
            // on va sauvgarder le piece jointe dans un dossier = upload un parameter dans le services.yaml ??? 
            // le nom de parameter : ImagesProduits
            $files->move($this->getParameter('ImagesProduits'),$photo);
            //Appeller la fonction persist
            $cnx->persist($prd);
            // Execution avec flush()
            $cnx->flush();
            }
            else {
                return new Response('Extension inacceptable ');
            }
            // Redirection vers l'affichage après l'ajout
           // return $this->redirectToRoute('produitAfficher');
        }
        return $this->render('produit/Ajout.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    // Affichage de Form de Table stock

     /**
     * @Route("Stock",name="Stock")
     */
    public function Stock(Request $request){

        $form=$this->createForm(StockFormType::class);
        return $this->render('produit/Stock.html.twig', [
            'form' => $form->createView(),
        ]);
    }
  /**
     * @Route("GetStock",name="GetStock")
     */
    public function GetStock(StockRepository $stock){

        $stocks=$stock->findAll();
        return $this->render('produit/GetStock.html.twig', [
            'stocks' => $stocks,
        ]);
    }

}
