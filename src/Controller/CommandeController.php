<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeDetail;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/orders', name: 'app_commande_')]

class CommandeController extends AbstractController
{
    #[Route('/ajout', name: 'add')]
    public function add(SessionInterface $session, ProduitRepository $produitRepository, EntityManagerInterface $em ): Response
    {
       $this->denyAccessUnlessGranted('ROLE_USER');

       $panier=$session->get('panier', []);
       if($panier=== []){
        $this->addFlash('message', 'votre panier est vide');
       return  $this->redirectToRoute('app_home');




       }

       //le panier n'est pas vide, on cree la commande

       $commande = new Commande();
       //on remplit la commande
       $commande->setUtilisateur($this->getUser());
    //    $commande->setReference(uniqid());
       //on parcourt le panier pour ceer les details de commande
       foreach($panier as $item => $quantity){
        $commandeDetails = new CommandeDetail();
        //on va chercher le produit
        $produit = $produitRepository->find($item);
        $prix = $produit->getPrix();
        //on cree le details de commmande
        $commandeDetails->setPro($produit);
        $commandeDetails->setPrix($prix);
        $commandeDetails->setQuantite($quantity);

        $commande->addCommandeDetail($commandeDetails);

        

       }
       //on persiste et on flush
       $em->persist($commande);
       $em->flush();


        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }
}
