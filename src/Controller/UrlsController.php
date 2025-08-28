<?php

namespace App\Controller;

use App\Repository\UrlRepository;
use App\Entity\Url;
use App\Form\UrlType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UrlsController extends AbstractController
{
   #[Route('/', name: 'app_urls')]
    public function index(Request $request, UrlRepository $urlsRepository, EntityManagerInterface $entityManager): Response
    {
        // nouvelle url ->
        $url = new Url();
        $form = $this->createForm(UrlType::class, $url);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'URL existe déjà
            $existing = $urlsRepository->findOneBy(['original' => $url->getOriginal()]);

            if ($existing) {
                return $this->redirectToRoute('app_preview', [
                    'shortened' => $existing->getShortened()
                ]);
            }

            // Génération d'un code court unique
            $shortened = substr(md5(uniqid()), 0, 6);
            $url->setShortened($shortened);

            $entityManager->persist($url);
            $entityManager->flush();

            return $this->redirectToRoute('app_preview', [
                'shortened' => $shortened
            ]);
        }

        return $this->render('urls/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/preview/{shortened}', name: 'app_preview')]
    public function preview(UrlRepository $urlsRepository, string $shortened): Response
    {
        $url = $urlsRepository->findOneBy(['shortened' => $shortened]);

        if (!$url) {
            throw $this->createNotFoundException('URL non trouvée');
        }

        return $this->render('urls/preview.html.twig', [
            'url' => $url,
        ]);
    }

    #[Route('/{shortened}', name: 'app_show')]
    public function show(UrlRepository $urlsRepository, string $shortened): Response
    {
        $url = $urlsRepository->findOneBy(['shortened' => $shortened]);

        if (!$url) {
            throw $this->createNotFoundException('URL non trouvée');
        }

        // Redirection vers l'URL originale
        return $this->redirect($url->getOriginal());
    }
}
