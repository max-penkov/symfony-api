<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\User;
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
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        /** @var User $user */
        $user = $this->getReference('user_admin');

        $blogPost = new BlogPost();
        $blogPost
            ->setTitle('A new post')
            ->setPublished(new \DateTime('2020-04-01 14:00:00'))
            ->setContent('body')
            ->setAuthor($user)
            ->setSlug('fuck');

        $manager->persist($blogPost);
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('Max');
        $user->setEmail('loco@list.ru');
        $user->setPassword('passs');
        $user->setUsername('locord');

        $this->setReference('user_admin', $user);

        $manager->persist($user);
        $manager->flush();
    }
}
