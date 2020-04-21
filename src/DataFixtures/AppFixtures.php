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
    private const USERS = [
        [
            'username' => 'admin',
            'email'    => 'admin@blog.com',
            'name'     => 'Piotr Jura',
            'password' => 'secret123#',
        ],
        [
            'username' => 'john_doe',
            'email'    => 'john@blog.com',
            'name'     => 'John Doe',
            'password' => 'secret123#',
        ],
        [
            'username' => 'rob_smith',
            'email'    => 'rob@blog.com',
            'name'     => 'Rob Smith',
            'password' => 'secret123#',
        ],
        [
            'username' => 'jenny_rowling',
            'email'    => 'jenny@blog.com',
            'name'     => 'Jenny Rowling',
            'password' => 'secret123#',
        ],
    ];

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
        for ($i = 0; $i < 100; ++$i) {
            $blogPost = new BlogPost();
            $blogPost
                ->setTitle($this->faker->realText(30))
                ->setPublished($this->faker->dateTime)
                ->setContent($this->faker->realText());

            $authorReference = $this->getRandomUserReference();
            $blogPost->setAuthor($authorReference);

            $blogPost->setSlug($this->faker->slug);

            $this->setReference("blog_post_$i", $blogPost);

            $manager->persist($blogPost);
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setEmail($userFixture['email']);
            $user->setName($userFixture['name']);

            $user->setPassword($this->userPasswordEncoder->encodePassword(
                $user,
                $userFixture['password']
            ));

            $this->addReference('user_' . $userFixture['username'], $user);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        /** @var User $author */
        $author = $this->getReference('user_admin');

        for ($i = 0; $i < 100; ++$i) {
            /** @var BlogPost $blogPost */
            $blogPost = $this->getReference("blog_post_$i");
            for ($j = 0; $j < rand(1, 10); ++$j) {
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);
                $comment->setAuthor($author);
                $comment->setBlogPost($blogPost);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    /**
     * @return User
     */
    protected function getRandomUserReference(): User
    {
        return $this->getReference('user_' . self::USERS[rand(0, 3)]['username']);
    }
}
