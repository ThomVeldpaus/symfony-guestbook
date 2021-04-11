<?php

namespace App\DataFixtures;


use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $comment = new Comment();
        $comment1->setAuthor('Thom Test');
        $comment1->setEmail('degroetjes@example.com');
        $comment1->setText('Het was geweldig');
        $comment1->setState('published');
        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2->setConference($amsterdam);
        $comment2->setAuthor('Thom Test2');
        $comment2->setEmail('d@mn.com');
        $comment2->setText('Stukkie tekst altijd leuk.');
        $manager->persist($comment2);

        $manager->flush();
    }
}
