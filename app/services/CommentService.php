<?php

namespace App\Services;

use App\Models\Comment;

use App\Exceptions\ServiceExceptions\ServiceException;

/**
 * business logic for comments
 *
 * Class CommentService
 */
class CommentService extends AbstractService
{
    /** Unable to create comment */
    const ERROR_UNABLE_CREATE_COMMENT = 13001;
    /** Comment not found */
    const ERROR_COMMENT_NOT_FOUND = 13003;
    /** Unable to delete comment */
    const ERROR_UNABLE_DELETE_COMMENT = 13004;
    /** Unable to update comment with other user */
    const ERROR_COMMENT_OWNER_USER = 13005;

    /**
     * Creating a new comment
     *
     * @param object $data
     * 
     * @return int
     */
    public function createComment(object $data)
    {
        $highligthDatetime = date(
            'Y-m-d H:i:s', 
            strtotime("+{$data->price} minutes")
        );

        try {
            $comment   = new Comment();
            $result = $comment->setUserId($data->user_id)
                ->setPostId($data->post_id)
                ->setHighlight($data->highlight)
                ->setPrice($data->price)
                ->setComment($data->comment)
                ->setDateTimeHighlight($highligthDatetime)
                ->create();

            if (!$result) {
                throw new ServiceException('Unable to create comment', self::ERROR_UNABLE_CREATE_COMMENT);
            }

            return [
                'id' => $comment->getId(),
                'postId' => $comment->getPostId()
            ];
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Getting all comments
     * 
     * @return array
     */
    public function getAllComments()
    {
        $commentsList = [];

        try {
            $comments = Comment::query()
                ->where('deleted_at is null')
                ->execute();

            foreach ($comments as $comment) {
                $commentsList[] = [
                    'id' => $comment->getId(),
                    'user' => [
                        'id' => $comment->user->getId(),
                        'username' => $comment->user->getUsername(),
                        'name' => $comment->user->getName(),
                        'email' => $comment->user->getEmail(),
                        'subscriber' => $comment->user->getSubscriber()
                    ],
                    'post' => $comment->getPostId(),
                    'comment' => $comment->getComment(),
                    'highlight' => $comment->getHighlight(),
                    'price' => $comment->getPrice(),
                    'date' => $comment->getDateTime(),
                    'highlightDateFinish' => $comment->getDatetimeHighlight()
                ];
            }

            usort($commentsList, [ $this, 'orderComments' ]);

            return $commentsList;
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    private function checkHighlight(string $highligthDatetime) {
        if (strtotime($highligthDatetime) >= strtotime("now")) {
            return true;
        }

        return false;
    }

    /**
     * Order comments
     */
    private function orderComments($a, $b) {
        if ($this->checkHighlight($a["highlightDateFinish"])) {
            return $a["price"] > $b["price"] ? -1 : 1;
        }

        if (strtotime($a['highlightDateFinish']) == strtotime($b['highlightDateFinish'])) {
            return 0;
        }
        
        return strtotime($a['highlightDateFinish']) > strtotime($b['highlightDateFinish']) ? -1 : 1;
    }

    /**
     * Getting specific comments
     * 
     * @param int $commentId
     * 
     * @return array
     */
    public function getComment($commentId)
    {
        try {
            $comment = $this->findComment($commentId);

            return [
                'id' => $comment->getId(),
                'post_id' => $comment->getPostId(),
                'user' => [
                    'id' => $comment->user->getId(),
                    'username' => $comment->user->getUsername(),
                    'name' => $comment->user->getName(),
                    'email' => $comment->user->getEmail(),
                    'subscriber' => $comment->user->getSubscriber()
                ],
                'comment' => $comment->getComment(),
                'highlight' => $comment->getHighlight(),
                'price' => $comment->getPrice(),
                'deleted' => $comment->getDeletedAt(),
                'date' => $comment->getDateTime()
            ];
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Find comment with Id
     * 
     * @param int $commentId
     * 
     * @return array
     */
    public function findComment(int $commentId)
    {
        try {
            $comment = (new Comment())->findFirstById($commentId);

            if (!$comment) {
                throw new ServiceException("Comment not found with id {$commentId}", self::ERROR_COMMENT_NOT_FOUND);
                
            }
            return $comment;
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete a comment
     * 
     * @param int $commentId
     * 
     * @return void
     */
    public function deleteComment(int $commentId, int $userId)
    {
        try {
            $comment = $this->findComment($commentId);
            $post = $this->postService->findPost($comment->getPostId());

            if (!$this->testOwner($userId, $comment->getUserId()) && !$this->testOwner($userId, $post->getUserId())){
                throw new ServiceException(
                    "Forbidden, comment isn't yours", 
                    self::ERROR_COMMENT_OWNER_USER, 
                    [
                        'message' => "You can't delete this comment because your aren't the owner of the post or the comment"
                    ]
                );
            };

            $deleted = $comment->setDeletedAt(date('Y-m-d H:i:s'))->save();

            if (!$deleted) {
                throw new ServiceException("Unable to delete comment", self::ERROR_UNABLE_DELETE_COMMENT, $deleted->getMessages());
            }
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete a comment
     * 
     * @param int $postId
     * @param int $userId
     * 
     * @return void
     */
    public function deleteAllComments(int $postId, int $userId)
    {
        try {
            $post = $this->postService->findPost($postId);

            if (!$this->testOwner($userId, $post->getUserId())){
                throw new ServiceException(
                    "Forbidden, comment isn't yours", 
                    self::ERROR_COMMENT_OWNER_USER, 
                    [
                        'message' => "You can't delete these comments because your aren't the owner of the post"
                    ]
                );
            };

            $deleted = [];
            foreach ($post->getComment() as $comment) {
                if (!$comment->getDeletedAt()) {
                    array_push($deleted, $comment->getId());

                    $result = $comment->setDeletedAt(date('Y-m-d H:i:s'))->save();

                    if ($result) {
                        array_pop($deleted);
                    }
                }
            }

            if (count($deleted) > 0) {
                throw new ServiceException(
                    "Unable to delete some comments", 
                    self::ERROR_UNABLE_DELETE_COMMENT,
                    $deleted
                );
            }
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    private function testOwner($userId, $commentUserId) {
        return $userId == $commentUserId;
    }
}