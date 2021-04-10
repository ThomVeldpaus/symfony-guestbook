<?php

namespace App\EntityListener;

use App\Entity\Conference;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class ConferenceEntityListener
 *
 * Will listen to Entity events to execute the generation of the slug
 * when the Entity is newly created and when the Entity is updated
 *
 * App/EntityListener is the package that provide Listening options to
 * Doctrine Entity evens and provides a way to inject objects in the methods
 * or assign them in the constructor to prevent repetition
 *
 * @package App\EntityListener
 */
class ConferenceEntityListener
{
    /**
     * @var SluggerInterface
     */
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * This function will execute the first time the row is saved to the database
     *
     * @param Conference $conference
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Conference $conference, LifecycleEventArgs $event)
    {
        $conference->computeSlug($this->slugger);
    }

    /**
     * This function will execute before the updated row is saved to the database
     *
     * @param Conference $conference
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(Conference $conference, LifecycleEventArgs $event)
    {
        $conference->computeSlug($this->slugger);
    }




}