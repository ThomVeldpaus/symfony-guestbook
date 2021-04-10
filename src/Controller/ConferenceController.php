<?php

/**
 * This controller will demo the usage of annotations to configure
 * the route to the right controller action
 */

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


/**
 * Class ConferenceController
 * @package App\Controller
 *
 * The ConferenceController will show you the conferences that have
 * been registerd, and the comments linked to that.
 */
class ConferenceController extends AbstractController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * ConferenceController constructor.
     *
     * I need Twig in every method and to save some room by not injecting it to every method
     * as a parameter, I can set it in the constructor and use it everywhere.
     *
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * This will route / to the controller action below the annotation
     * The controller action responds basic html body with an image
     *
     * In Response this Controller action will render a twig template
     * which will render all conferences, because the twig variable
     * is filled with the data from $conferenceRepository->findAll()
     *
     * The $twig Environment will automatically be added to the function
     * by suggesting the \Twig\Environment type in the Controller method
     *
     * @param \App\Repository\ConferenceRepository $conferenceRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/', name: 'homepage')]
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        return new Response($this->twig->render('conference/index.html.twig', [
            /**
             * here I can assign template variables
             * the 'conference variable will be filled with all results
             * from the ConferenceRepository
             */
            'conferences' => $conferenceRepository->findAll(),
        ]));
    }

    /**
     * This method has a particular behaviour, we inject the Entity Conference in tbe method
     * and Symfony is smart enough to load the corresponding Conference with the {id}
     *
     * In the Response the CommentRepository will find all matching comments by Conference
     *
     * To make pages of the comments you give the Paginator to twig
     *
     * @param Conference $conference
     * @param CommentRepository $commentRepository
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route('/conference/{id}', name: 'conference')]
    public function show(Request $request, Conference $conference, CommentRepository $commentRepository): Response
    {
        // the offset is the Int parameter that will given in the Request, or default 0
        $offset = max(0, $request->query->getInt('offset', 0));
        // the paginator is the returned Object of my custom method to fetch one in CommentRepository
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        /**
         * Now the paginator is given trough to Twig and also is a next and previous value that is calculated
         * from the current Requests $offset
         */
        return new Response($this->twig->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        ]));
    }
}
