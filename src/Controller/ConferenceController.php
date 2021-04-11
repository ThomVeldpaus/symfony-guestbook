<?php

/**
 * This controller will demo the usage of annotations to configure
 * the route to the right controller action
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
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

    private $bus;

    /**
     * ConferenceController constructor.
     *
     * I need Twig in every method and to save some room by not injecting it to every method
     * as a parameter, I can set it in the constructor and use it everywhere.
     *
     * ConferenceController constructor.
     * @param Environment $twig
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(Environment $twig, EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }

    /**
     * The Conference index() method
     *
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
     * @param ConferenceRepository $conferenceRepository
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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
     * The Conference show() method
     *
     * This method has a particular behaviour, we inject the Entity Conference in tbe method
     * and Symfony is smart enough to load the corresponding Conference by {slug}
     *
     * In this method, we handle the $request with an form, we validate the data and if all is valid,
     * the comment will be added to the conference.
     *
     * Also we fetch the optionally uploaded image the person added and save it to a image directory,
     * the image will be shown with the comment in the frontend.
     *
     * In the Response the CommentRepository will find all matching comments by Conference
     *
     * To make pages of the comments you give the Paginator to twig
     *
     * @param Request $request
     * @param Conference $conference
     * @param CommentRepository $commentRepository
     * @param string $photoDir
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route('/conference/{slug}', name: 'conference')]
    public function show(Request $request, Conference $conference, CommentRepository $commentRepository, string $photoDir): Response
    {
        /**
         * create a new comment entity object and use this model class as parameter for the createForm
         * function (extended from AbstractController)
         */
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);

        // handleRequest() inspects the request and calls submit() when the form was submitted.
        $form->handleRequest($request);

        $filename = '';

        /**
         * The form knows if the form was submitted from a request parameter
         * Also will the form check if all submitted formdata is valid data
         * to forward to the Entity Manager
         */
        if ($form->isSubmitted() && $form->isValid()) {

            // If all data is valid, link the comment with the right conference
            $comment->setConference($conference);

            // If there is data in the formfield 'photo' get the data and save it to $photo
            if ($photo = $form['photo']->getData()) {

                // render a hexadecimal string + extension as the photo's filename
                $filename = bin2hex(random_bytes(6)).'.'.$photo->guessExtension();

                try {
                    // try to put the file in the photo dir
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    //unable to upload the photo
                }
            }

            // it's possible this will get flushed by a async messenger and then you will see the current state
            $comment->setState('message queued');

            // get the Comment entity ready for db and fetch id
            $this->entityManager->persist($comment);

            // dispatch the CommentMessage with comment id and some context
            $this->bus->dispatch(new CommentMessage($comment->getId(), [
                'photoFilename' => $filename,
                'clientIp' => $request->getClientIp(),
            ]));

            // Redirect to the same page, when everything has gone correct, the new comment should be in the list
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
