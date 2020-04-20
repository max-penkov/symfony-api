<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var Factory
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->faker               = Factory::create();
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        /** @var User $user */
        $user = $this->getReference('user_admin');

        for ($i = 0; $i < 100; ++$i) {
            $blogPost = new BlogPost();
            $blogPost
                ->setTitle($this->faker->realText(30))
                ->setPublished($this->faker->dateTime)
                ->setContent($this->faker->realText())
                ->setAuthor($user)
                ->setSlug($this->faker->slug);

            $this->setReference("blog_post_$i", $blogPost);
            $manager->persist($blogPost);
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setName($this->faker->name);
        $user->setEmail($this->faker->email);
        $user->setUsername($this->faker->userName);
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, 'password'));

        $this->setReference('user_admin', $user);

        $manager->persist($user);
        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        /** @var User $author */
        $author = $this->getReference('user_admin');

        for ($i = 0; $i < 100; ++$i) {
            for ($j = 0; $j < rand(1, 10); ++$j) {
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);
                $comment->setAuthor($author);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
