<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Repository\PanierRepository;
use App\Repository\ProduitsRepository;
class AdminController extends AbstractController
{
    /**
     * @Route("/login", name="admin")
     */
    public function login(Request $req): Response
    {
        if($req->isMethod('POST'))
        {
            $email=$req->get('email');$pwd=$req->get('mdp');
            if($email=='admin2021@gmail.com' and $pwd='admin123')
               {
                // demarrer une session 
                $session=new session();  
                // creation d'une session avec le name 'role' et de valeur 'admin' 
                $session->set('role','admin');
                return $this->redirectToRoute('homeAdmin');}
               else {
                $erreur="Vous n'êtes pas l'admin !!!";
               }
        }

        return $this->render('admin/login.html.twig',array('error'=>@$erreur));
    }

    /**
     * @Route("/HomeAdmin",name="homeAdmin")
     */
    public function AccueilAdmin()
    {
        return $this->render('admin/home.html.twig');
    }

    /**
     * @Route("/getPaniers",name="Paniers")
     */
    public function getPaniers(PanierRepository $panier,ProduitsRepository $produit,Request $request)
    {
        $session=new session();
        

        $role=$session->get('role');
        if($role=='client')
        {return $this->redirectToRoute('home');}
        else if($role=='admin'){

        $paniers=$panier->findBy(array('etat'=>'en cours'));
        $resultatRecherche=null;
        if($request->isMethod('POST')){// l'orsqu'on click sur le bouton de type submit
            $name=$request->get('recherche');
            $resultatRecherche=$produit->findBy(array('categorie'=>$name));
            $paniers=null;

        }
        return $this->render('admin/paniers.html.twig',array('paniers'=>$paniers,'resRecherche'=>$resultatRecherche));
        }
    }

    /**
     * @Route("/TraiterPanier/{etat}/{id}",name="TraiterPanier")
     */
    public function TraiterPanier($etat,$id,PanierRepository $panier)
    {
        $paniers=$panier->find($id);
        $paniers->setEtat($etat);
        $cnx=$this->getDoctrine()->getManager();
        $cnx->persist($paniers);
        $cnx->flush();
        return $this->redirectToRoute('Paniers');
    }
}
