<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\SubscriptionType;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/subscription')]
class SubscriptionController extends AbstractController
{
    #[Route('/', name: 'app_subscription_index', methods: ['GET'])]
    public function index(SubscriptionRepository $subscriptionRepository): Response
    {
        // Utilisation du Repository pour récupérer les différentes listes d'abonnements
        return $this->render('subscription/index.html.twig', [
            'subscriptions' => $subscriptionRepository->findAll(),
            'activeSubscriptions' => $subscriptionRepository->findActiveSubscriptions(),
            'upcomingRenewals' => $subscriptionRepository->findUpcomingRenewals(),
        ]);
    }

    #[Route('/dashboard', name: 'app_subscription_dashboard', methods: ['GET'])]
    public function dashboard(SubscriptionRepository $subscriptionRepository): Response
    {
        // Collecte de toutes les statistiques via le repository
        return $this->render('subscription/dashboard.html.twig', [
            'totalActive' => $subscriptionRepository->count(['status' => Subscription::STATUS_ACTIVE]),
            'upcomingRenewals' => $subscriptionRepository->findUpcomingRenewals(),
            'monthlyTotal' => $subscriptionRepository->getTotalMonthlyAmount(),
            'yearlyTotal' => $subscriptionRepository->getTotalYearlyAmount(),
            'byCategory' => $subscriptionRepository->getSubscriptionsByCategory(),
            'byProvider' => $subscriptionRepository->getSubscriptionsByProvider(),
        ]);
    }

    #[Route('/new', name: 'app_subscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création d'un nouvel objet Subscription vide
        $subscription = new Subscription();
        
        // Création du formulaire
        $form = $this->createForm(SubscriptionType::class, $subscription);
        
        // Traitement des données soumises
        $form->handleRequest($request);

        // Vérification et sauvegarde si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subscription);  // Préparation de la sauvegarde
            $entityManager->flush();  // Exécution de la sauvegarde

            // Message de confirmation pour l'utilisateur
            $this->addFlash('success', 'Subscription created successfully');
            
            // Redirection vers la liste des abonnements
            return $this->redirectToRoute('app_subscription_index');
        }
        // Affichage du formulaire
        return $this->render('subscription/new.html.twig', [
            'subscription' => $subscription,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_subscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Subscription $subscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubscriptionType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pas besoin de persist()
            $entityManager->flush();

            $this->addFlash('success', 'Subscription updated successfully');
            return $this->redirectToRoute('app_subscription_index');
        }

        return $this->render('subscription/edit.html.twig', [
            'subscription' => $subscription,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/cancel', name: 'app_subscription_cancel', methods: ['POST'])]
    public function cancel(Request $request, Subscription $subscription, EntityManagerInterface $entityManager): Response
    {
        // Vérification du token CSRF pour la sécurité
        if ($this->isCsrfTokenValid('cancel'.$subscription->getId(), $request->request->get('_token'))) {
            $subscription->setStatus(Subscription::STATUS_CANCELLED);
            $entityManager->flush();
            
            $this->addFlash('success', 'Subscription cancelled successfully');
        }

        return $this->redirectToRoute('app_subscription_index', ['id' => $subscription->getId()]);
    }

    #[Route('/{id}', name: 'app_subscription_show', methods: ['GET'])]
    public function show(Subscription $subscription): Response
    {
        return $this->render('subscription/show.html.twig', [
            'subscription' => $subscription,
        ]);
    }

    #[Route('/{id}', name: 'app_subscription_delete', methods: ['POST'])]
    public function delete(Request $request, Subscription $subscription, EntityManagerInterface $entityManager): Response
    {
        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete'.$subscription->getId(), $request->request->get('_token'))) {
            $entityManager->remove($subscription);  // marquage pour suppression
            $entityManager->flush();  // Exécution de la suppression
            
            $this->addFlash('success', 'Subscription deleted successfully');
        }
        return $this->redirectToRoute('app_subscription_index');
    }
}