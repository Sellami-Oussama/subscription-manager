<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Form\ProviderType;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/provider')]
class ProviderController extends AbstractController
{
    #[Route('/', name: 'app_provider_index', methods: ['GET'])]
    public function index(ProviderRepository $providerRepository): Response
    {
        return $this->render('provider/index.html.twig', [
            'providers' => $providerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_provider_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $provider = new Provider();
        //Création du formulaire
        $form = $this->createForm(ProviderType::class, $provider);
        
        //traitement des données
        $form->handleRequest($request);

        //Verification et sauvegarde si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($provider);
            $entityManager->flush();
            // Message flash
            $this->addFlash('success', 'Provider created successfully');
            return $this->redirectToRoute('app_provider_index');
        }

        //Affichage du formulaire
        return $this->render('provider/new.html.twig', [
            'provider' => $provider,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_provider_show', methods: ['GET'])]
    public function show(Provider $provider): Response
    {
        return $this->render('provider/show.html.twig', [
            'provider' => $provider,
            'activeSubscriptions' => $provider->getActiveSubscriptions(),
            'totalAmount' => $provider->getTotalActiveSubscriptionsAmount(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_provider_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Provider $provider, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Provider updated successfully');
            return $this->redirectToRoute('app_provider_index');
        }
        return $this->render('provider/edit.html.twig', [
            'provider' => $provider,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_provider_delete', methods: ['POST'])]
    public function delete(Request $request, Provider $provider, EntityManagerInterface $entityManager): Response
    {
        //Vérification du token CSRF pour prévenir les attaques CSRF
        if ($this->isCsrfTokenValid('delete'.$provider->getId(), $request->request->get('_token'))) {
            //on ne peut pas supprimer un fournisseur qui a des abonnements
            if (!$provider->getSubscriptions()->isEmpty()) {
                $this->addFlash('error', 'Cannot delete provider: it still has active subscriptions');
            } else {
                //Si pas d'abonnements on peut supprimer
                $entityManager->remove($provider);
                $entityManager->flush();
                $this->addFlash('success', 'Provider deleted successfully');
            }
        }
        return $this->redirectToRoute('app_provider_index');
    }
}