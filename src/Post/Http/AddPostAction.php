<?php

namespace App\Post\Http;


use App\Core\Http\Annotation\ResponseCode;
use App\Core\Http\Annotation\ResponseGroups;
use App\Core\Service\Flusher;
use App\Post\Entity\Post;
use App\Post\Entity\Posts;
use App\Post\Entity\TagExtractor;
use App\Security\Entity\Credential;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/posts")
 */
class AddPostAction
{
    /**
     * @var Posts
     */
    private $posts;

    /**
     * @var TagExtractor
     */
    private $tagExtractor;

    public function __construct(Posts $posts, TagExtractor $tagExtractor)
    {
        $this->posts = $posts;
        $this->tagExtractor = $tagExtractor;
    }

    /**
     * @Method({"POST"})
     * @ResponseGroups({"api_post"})
     * @ResponseCode(201)
     *
     * @param AddPostRequest $request
     * @param Credential $credential
     * @param Flusher $flusher
     *
     * @return Post
     */
    public function __invoke(AddPostRequest $request, Credential $credential, Flusher $flusher)
    {
        $post = Post::builder()
            ->setAuthor($credential->id())
            ->setDescription($request->description, $this->tagExtractor)
            ->setMedia($request->media)
            ->build();

        $this->posts->add($post);

        $flusher->flush();

        return $post;
    }
}