<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/blog")
 * Class BlogController
 * @package App\Controller
 */
class BlogController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/{page}", name="blog_list", defaults={"page": 5}, requirements={"page"="\d+"}, methods={"GET"})
     *
     * @param Request $request
     * @param int     $page
     *
     * @return JsonResponse
     */
    public function list(Request $request, int $page)
    {
        $limit = $request->get('limit', 10);
        $repo  = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repo->findAll();

        return $this->json([
            'page'  => $page,
            'limit' => $limit,
            'data'  => array_map(function (BlogPost $item) {
                return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
            }, $items),
        ]);
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"})
     * @param BlogPost $post
     *
     * @return JsonResponse
     */
    public function post(BlogPost $post)
    {
        return $this->json($post);
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
     * @param BlogPost $post
     *
     * @return JsonResponse
     */
    public function postBySlug(BlogPost $post)
    {
        return $this->json($post);
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $blogPost = $this->serializer->deserialize($request->getContent(), BlogPost::class, JsonEncoder::FORMAT);

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }

    /**
     * @Route("/post{id}", name="blog_delete", methods={"DELETE"})
     * @param BlogPost $post
     *
     * @return JsonResponse
     */
    public function delete(BlogPost $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
