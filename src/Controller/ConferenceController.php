<?php

/**
 * This controller will demo the usage of annotations to configure
 * the route to the right controller action
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ConferenceController constructor.
     *
     * I need Twig in every method and to save some room by not injecting it to every method
     * as a parameter, I can set it in the constructor and use it everywhere.
     *
     * @param Environment $twig
     */
    public function __construct(Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
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
    #[Route('/conference/{slug}', name: 'conference')]
    public function show(Request $request, Conference $conference, CommentRepository $commentRepository): Response
    {
        /**
         * create a new comment entity object and use this model class as parameter for the createForm
         * function (extended from AbstractController)
         */
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);

        /**
         * handleRequest() inspects the request and calls submit() when the form was submitted.
         */
        $form->handleRequest($request);
        /**
         * The form knows if the form was submitted from a request parameter
         * Also will the form check if all submitted formdata is valid data
         * to forward to the Entity Manager
         */
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * If all data is valid, connect the comment with the right conference
             */
            $comment->setConference($conference);
            /**
             * Use the Entity Manager to queue a persist task to create a new comment row in the database
             */
            $this->entityManager->persist($comment);
            /**
             * All queued tasks of the EntityManager are written to te database in Bulk
             */
            $this->entityManager->flush();

            /**
             * Redirect to the same page, when everything has gone correct, the new comment should be in the list
             */
            return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
        }

        // the offset is the (int) GET parameter that will be given in the Request, or default 0
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
            'comment_form' => $form->createView(), // assign a form view to Twig as 'comment_form'
        ]));
    }
}
