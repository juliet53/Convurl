<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UrlRepository;

class UrlsControllerTest extends WebTestCase
{
    public function testIndexRedirectsOnValidUrl(): void
    {
        $client = static::createClient();

        // Récupération de la page form
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[name="url[original]"]');

        // Remplissage du formulaire
        $form = $crawler->selectButton('Raccourcir')->form([
            'url[original]' => 'https://www.symfony.com',
        ]);

        $client->submit($form);

        // Vérifie la redirection vers app_preview
        $this->assertResponseRedirects();
        $client->followRedirect();

        // Vérifie que la page preview contient l'URL originale
        $this->assertSelectorTextContains('body', 'https://www.symfony.com');
    }
}
