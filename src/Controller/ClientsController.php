<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ClientFormType;
use App\Entity\Clients;
use App\Repository\ClientsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Repository\ProduitsRepository;
use App\Repository\PanierRepository;
class ClientsController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscriptions")
     */
    public function Ajout(Request $req,ClientsRepository $select)
    {
        $clt=new Clients();// si on va faire l'insertion on va créer une instance de la classe de l'entity 
        $form=$this->createForm(ClientFormType::class,$clt);//$clt c'est le nom de l'objet
        $form->handleRequest($req);
        if($req->isMethod('POST')) // ou bien $form->isSubmitted() si on a un formulaire généré
        {
            $frms=$req->get('client_form');// client_form c'est le name de form générer
            $email=$frms['email'];
            $res=$select->findOneBy(array('email'=>$email));
            if(!empty($res))
            {
                $error='Email existe déja';
            }
            else{
          // La connexion avec Doctrine
          $cnx=$this->getDoctrine()->getManager();
          $cnx->persist($clt); 
          $cnx->flush();
          return $this->redirectToRoute("affichageClient");
        }
        }
        return $this->render('clients/Ajout.html.twig', [
            'form' => $form->createView(),'msg'=>@$error
        ]);
    }

    /**
     * @Route("/AfficheClients",name="affichageClient")
     */
    public function Affichage(ClientsRepository $repClient,Request $req)
    {
        $client=new session();// en symfony pour récupérer une session il faut appeller la fonction getSession() avec un parameter de type Request
       // echo '<h1> sss '.$client->getId().'</h1>';
      // var_dump($client);
        if(empty($client->get('client')))
        {
            return $this->redirectToRoute('authentification');
            //return new Response('Email '. $req->getSession('client')->get('nom'));
        }
        else{
        $res=$repClient->findAll();// find requete dql(doctrine Query Langage)
        return $this->render('clients/Affichage.html.twig',['resultat'=>$res]);
        }
    }

    /**
     * @Route("/deleteClient/{id_delete}",name="deleteClient")
     */
    public function supprimer(ClientsRepository $repClient,$id_delete){
        // la selection   de client qui a un id =$id_delete
        $client=$repClient->find($id_delete);
        // connexion avec Doctrine
        $cnx=$this->getDoctrine()->getManager();
        $cnx->remove($client);
        $cnx->flush();
        // La redirection vers la page d'affichage
        return $this->redirectToRoute("affichageClient");//affichageClient c'est le name de route

    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function Modifier(Request $req,ClientsRepository $repClient,$id)
    {
        $client=$repClient->find($id);
        $form=$this->createForm(ClientFormType::class,$client);//$clt c'est le nom de l'objet
        $form->handleRequest($req);
        if($req->isMethod('POST')) // ou bien $form->isSubmitted() si on a un formulaire généré
        {
          // La connexion avec Doctrine
          $cnx=$this->getDoctrine()->getManager();
          $cnx->persist($client); 
          $cnx->flush();
          return $this->redirectToRoute("affichageClient");
        }
        return $this->render('clients/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Loginclient",name="authentification")
     */
    public function authentification(Request $request,ClientsRepository $select){
        $form=$this->createForm(ClientFormType::class);
           if($request->isMethod('POST')) 
           {
               $frms=$request->get('client_form');// client_form c'est le name de form générer
               $email=$frms['email'];// la recupération sera avec le resultat de recupération de form $frms
               $mdp=$frms['password'];//
               //echo '<h1> Email '.$email.' Password '.$mdp.'</h1>';
               $res=$select->findOneBy(array('email'=>$email));// , c'est l'équivalent de and en php
               if(empty($res))
               {
                $message='Email n\'existe pas ';
               }
               else { // si l'email existe dans la table clients
                   // il faut vérifier si le password de resultat de find avec email "$res" == $mdp qui est la valeur de champ de formulaire
                   // on va récupérer la valeur de la colonne password de table clients de resultat trouvé
                   $pwd=$res->getPassword();// on peut récupérer la valeur de la colonne pasword de resultat trouvé "$res " si : $res=$select->find() c-a-d selection selon l'id ou bien $select->findOneBy
                  if($pwd == $mdp)
                  {
                      // démarrer la session
                      $session=new session();// $session c'est le nom de l'objet de type session
                      // création de session
                      $session->set('client',$res);// $res de type array()
                      $session->set('role','client');
                      // Redirection vers la page d'affichage 
                      return $this->redirectToRoute('affichageClient');
                    $message='vous êtes membre Votre nom est  : '.$res->getNom(); 
                  }
                  else{
                    $message='Mot de passe '.$mdp.' est incorrect : ';
                  }
               }
           }
        return $this->render("clients/login.html.twig",array('form'=>$form->createView(),'msg'=>@$message));

    }

    /**
     * @Route("/logout/{acteur}",name="deconnexion")
     */
    public function deconnexion($acteur){
        $session=new session();
          $session->clear();
            if($acteur=='client'){
      return $this->redirectToRoute('authentification');
       }
       else if($acteur=='admin'){
        return $this->redirectToRoute('admin');
       }

    }

    /**
     * @Route("/",name="home")
     */
    public function home(ProduitsRepository $produit,PanierRepository $panier)
    {
        $session= new session();
        $panierClient=null;
        if($session->get('client')!=null){
        $idClientS=$session->get('client')->getId();
        $panierClient=$panier->findByClient($idClientS);
        }
          @$nbPanier=count($panierClient);
        $produits=$produit->findAll();
        return $this->render("index.html.twig",['produits'=>$produits,'panier'=>$panierClient,'nbPanier'=>$nbPanier]);
    }
}
