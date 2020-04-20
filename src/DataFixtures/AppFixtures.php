<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $blogPost = new BlogPost();
        $blogPost
            ->setTitle('A new post')
            ->setPublished(new \DateTime('2020-04-01 14:00:00'))
            ->setContent('body')
            ->setAuthor('author')
            ->setSlug('fuck');

        $manager->persist($blogPost);
        $manager->flush();
    }
}
