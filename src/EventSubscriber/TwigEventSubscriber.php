<?php

namespace App\EventSubscriber;

use App\Repository\ConferenceRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var ConferenceRepository
     */
    private $conferenceRepository;

    /**
     * TwigEventSubscriber constructor.
     *
     * Preventing injection every method with the same objects
     * @param Environment $twig
     * @param ConferenceRepository $conferenceRepository
     */
    public function __construct(Environment $twig, ConferenceRepository $conferenceRepository)
    {
        $this->twig = $twig;
        $this->conferenceRepository = $conferenceRepository;
    }

    /**
     * Everytime when a controller is called before rendering of the
     * Twig templates, add all conferences to the twig Environment and
     * all conferences will be available in the templates globally
     *
     * When the variable is globally available, I don't have to add it
     * in every controller method Response
     *
     * @param ControllerEvent $event
     */
    public function onControllerEvent(ControllerEvent $event)
    {
        $this->twig->addGlobal('conferences', $this->conferenceRepository->findAll());
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
