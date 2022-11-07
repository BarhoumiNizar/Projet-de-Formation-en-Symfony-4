<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Panier;
use App\Repository\ClientsRepository;
use App\Repository\ProduitsRepository;
class PaniersController extends AbstractController
{
    /**
     * @Route("/paniers", name="paniers")
     */
    public function index(): Response
    {
        return $this->render('paniers/index.html.twig', [
            'controller_name' => 'PaniersController',
        ]);
    }


    /**
     * @Route("/AddCommande",name="commandeAjout")
     */
    public function AddCommande(Request $request,ClientsRepository $clt,ProduitsRepository $prd){

        $qte=$request->get('qteCommande');
        $idProduit=$request->get('produit');
        // Recupération de l'id de client connecté 
           // Recup 1 : avec la session 
        $session= new session();
        $idClientS=$session->get('client')->getId();
        // Recup 2 : avec la valeur de champ de form
        $idClient=$request->get('client');
        $client=$clt->find($idClientS);
        $produit=$prd->find($idProduit);
        // instance de la classe Panier
        $cmd=new Panier();
        $cmd->setQteCommande($qte);
        $cmd->setDateCommande(date('Y-m-d'));
        $cmd->setEtat('en cours');
        $cmd->setClient($client);//???????????????????
        $cmd->setProduit($produit);//??????????????
        // Connexion avec Doctrine
        $cnx=$this->getDoctrine()->getManager();
        $cnx->persist($cmd);
        $cnx->flush();
        return new Response('Commande ajoutée');



    }
}
