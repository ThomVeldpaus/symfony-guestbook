<?php

/**
 * This controller will demo the usage of annotations to configure
 * the route to the right controller action
 */

namespace App\Controller;

use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController extends AbstractController
{
    /**
     * This will route / to the controller action below the annotation
     * The controller action responds basic html body with an image
     *
     * In Response this Controller action will render a twig template
     * which will render all conferences, because the twig variable
     * is filled with the data from $conferenceRepository->findAll()
     *
     * @param \Twig\Environment $twig
     * @param \App\Repository\ConferenceRepository $conferenceRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/', name: 'homepage')]
    public function index(Environment $twig, ConferenceRepository $conferenceRepository): Response
    {
        return new Response($twig->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
        ]));
    }
}
