<?php

namespace App\Controller;

use App\Repository\UrlRepository;
use App\Entity\Url;
use App\Form\UrlType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UrlsController extends AbstractController
{
   #[Route('/', name: 'app_urls')]
    public function index(Request $request, UrlRepository $urlsRepository, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        try {
            $url = new Url();
            $form = $this->createForm(UrlType::class, $url);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $logger->info('Form submitted', ['url' => $url->getOriginal()]);

                $existing = $urlsRepository->findOneBy(['original' => $url->getOriginal()]);

                if ($existing) {
                    $logger->info('URL déjà existante', ['shortened' => $existing->getShortened()]);
                    return $this->redirectToRoute('app_preview', [
                        'shortened' => $existing->getShortened()
                    ]);
                }

                $shortened = substr(md5(uniqid()), 0, 6);
                $url->setShortened($shortened);

                $entityManager->persist($url);
                $entityManager->flush();

                return $this->redirectToRoute('app_preview', ['shortened' => $shortened], 303);
            }

            return $this->render('urls/index.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Throwable $e) {
            // Affiche directement dans la console Docker
            error_log("=== EXCEPTION ===");
            error_log("Message: " . $e->getMessage());
            error_log("Fichier: " . $e->getFile() . " (ligne " . $e->getLine() . ")");
            error_log("Trace: " . $e->getTraceAsString());

            // Optionnel : retourne une réponse minimaliste
            return new Response('Une erreur est survenue (voir logs console).', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/preview/{shortened}', name: 'app_preview')]
    public function preview(UrlRepository $urlsRepository, string $shortened): Response
    {
        try {
            $url = $urlsRepository->findOneBy(['shortened' => $shortened]);

            if (!$url) {
                throw $this->createNotFoundException('URL non trouvée');
            }

            return $this->render('urls/preview.html.twig', [
                'url' => $url,
            ]);
        } catch (\Throwable $e) {
            error_log("=== EXCEPTION ===");
            error_log("Message: " . $e->getMessage());
            error_log("Fichier: " . $e->getFile() . " (ligne " . $e->getLine() . ")");
            error_log("Trace: " . $e->getTraceAsString());

            return new Response('Une erreur est survenue (voir logs console).', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{shortened}', name: 'app_show')]
    public function show(UrlRepository $urlsRepository, string $shortened): Response
    {
        try {
            $url = $urlsRepository->findOneBy(['shortened' => $shortened]);

            if (!$url) {
                throw $this->createNotFoundException('URL non trouvée');
            }

            return $this->redirect($url->getOriginal());
        } catch (\Throwable $e) {
            error_log("=== EXCEPTION ===");
            error_log("Message: " . $e->getMessage());
            error_log("Fichier: " . $e->getFile() . " (ligne " . $e->getLine() . ")");
            error_log("Trace: " . $e->getTraceAsString());

            return new Response('Une erreur est survenue (voir logs console).', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
