<?php

namespace App\Services;

use App\Models\Post;
use App\Exceptions\ServiceExceptions\ServiceException;

/**
 * business logic for posts
 *
 * Class PostService
 */
class PostService extends AbstractService
{
    /** Unable to create post */
    const ERROR_UNABLE_CREATE_POST = 12001;
    /** Unable to find posts */
    const ERROR_UNABLE_UPDATE_POST = 12002;
    /** Post not found */
    const ERROR_POST_NOT_FOUND = 12003;
    /** Unable to delete post */
    const ERROR_UNABLE_DELETE_POST = 12004;
    /** Unable to update post with other user */
    const ERROR_POST_OWNER_USER = 12005;

    /**
     * Creating a new post
     *
     * @param object $data
     * 
     * @return void
     */
    public function createPost(object $data)
    {
        try {
            $post   = new Post();
            $result = $post->setUserId($data->user_id)
                ->setContent($data->content)
                ->setType($data->type)
                ->create();

            if (!$result) {
                throw new ServiceException('Unable to create post', self::ERROR_UNABLE_CREATE_POST);
            }
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Getting all posts
     * 
     * @return array
     */
    public function getAllPosts()
    {
        $postsList = [];

        try {
            $posts = (new Post())->find();

            foreach ($posts as $post) {
                $postsList[] = [
                    'id' => $post->id,
                    'user' => [
                        'id' => $post->user->id,
                        'username' => $post->user->username,
                        'name' => $post->user->name,
                        'email' => $post->user->email,
                    ],
                    'content' => $post->content,
                    'type' => $post->type
                ];
            }

            return $postsList;
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Getting specific posts
     * 
     * @param int $postId
     * 
     * @return array
     */
    public function getPost($postId)
    {
        try {
            $post = $this->findPost($postId);

            return [
                'id' => $post->id,
                'user' => [
                    'id' => $post->user->id,
                    'username' => $post->user->username,
                    'name' => $post->user->name,
                    'email' => $post->user->email,
                ],
                'content' => $post->content,
                'type' => $post->type
            ];
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Find post with Id
     * 
     * @param int $postId
     * 
     * @return array
     */
    public function findPost(int $postId)
    {
        try {
            $post = (new Post())->findFirstById($postId);

            if (!$post) {
                throw new ServiceException("Post not found with id {$postId}", self::ERROR_POST_NOT_FOUND);
                
            }
            return $post;
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Updating a post
     * 
     * @param int $postId
     * @param object $data
     * 
     * @return array
     */
    public function updatePost(int $postId, object $data)
    {
        try {
            $post = $this->findPost($postId);

            $this->testPostOwner($data->user_id, $post->getUserId());
            
            $updated = $post->setUserId($data->user_id ?: $post->getUserId())
                ->setContent($data->content ?: $post->getContent())
                ->setType($data->type ?: $post->getType())
                ->save();

            if (!$updated) {
                throw new ServiceException("Unable to update post", self::ERROR_UNABLE_UPDATE_USER, $deleted->getMessages());
            }

            return [
                'id' => $post->id,
                'user' => [
                    'id' => $post->user->id,
                    'username' => $post->user->username,
                    'name' => $post->user->name,
                    'email' => $post->user->email,
                ],
                'content' => $post->content,
                'type' => $post->type
            ];
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete a post
     * 
     * @param int $postId
     * 
     * @return void
     */
    public function deletePost(int $postId, int $userId)
    {
        try {
            $post = $this->findPost($postId);
            
            $this->testPostOwner($userId, $post->getUserId());

            $deleted = $post->delete();

            if (!$deleted) {
                throw new ServiceException("Unable to delete post", self::ERROR_UNABLE_DELETE_POST, $deleted->getMessages());
            }
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    private function testPostOwner($userId, $postUserId) {
        if ($userId != $postUserId) {
            throw new ServiceException("Forbidden, post isn't yours", self::ERROR_POST_OWNER_USER);
        }
    }
}