<?php

namespace App\Controllers;

use App\Exceptions\ServiceExceptions\ServiceException;
use App\Exceptions\HttpExceptions\Http400Exception;
use App\Exceptions\HttpExceptions\Http403Exception;
use App\Exceptions\HttpExceptions\Http422Exception;
use App\Exceptions\HttpExceptions\Http500Exception;

use App\Services\CommentService;
use App\Services\PostService;

use App\Controllers\Helpers\Helper;

/**
 * Operations with Comments
 */
class CommentController extends AbstractController
{
    /**
     * Adding new comment
     */
    public function addAction()
    {
        $data = $this->request->getJsonRawBody();
        $invalidErrors = $this->validate($data);
        $requiredData = $this->requiredValidate($data);
        
        $errors = array_merge($requiredData, $invalidErrors);

        if ($errors) {
            $exception = new Http400Exception(_('Input parameters validation error'));
            throw $exception->addErrorDetails($errors);
        }

        $data->highlight = $data->price > 0 ? 1 : 0;

        try {
            $userComments = $this->commentService->countCommentsWithSecondsRole($data->user_id);
            
            if ($userComments >= $_ENV['COMMENTS_PER_MINUTE']) {
                throw new Http422Exception(
                    "Sorry, something went wrong",
                    [
                        'message' => "You can't make more than 5 comments per minute"
                    ]
                );
            }

            $user = $this->userService->findUser($data->user_id);

            if ($data->highlight && !$this->userService->canBuyHighlight($data->user_id, $data->price)) {
                throw new Http422Exception(
                    "Sorry, something went wrong",
                    [
                        'message' => "You don't have money enough"
                    ]
                );
            }

            $post = $this->postService->findPost($data->post_id);

            if (!$data->highlight && !$user->getSubscriber() && !$post->user->getSubscriber()) {
                throw new Http422Exception(
                    "Sorry, something went wrong",
                    [
                        'message' => "You can't comment on this post, please pay highlight or subscribe to proceed"
                    ]
                );
            }

            // Start a transaction
            $this->db->begin();

            $comment = $this->commentService->createComment($data);

            if ($data->highlight) {
                $this->transactionService->buyHighlight($data->user_id, $comment["id"]);
            }

            $this->notify(
                $comment["id"], 
                "{$user->getName()} have commented on your post", 
                $post->getUserId()
            );

            $this->db->commit();
        } catch (ServiceException $e) {
            $this->db->rollback();
            
            switch ($e->getCode()) {
                case CommentService::ERROR_UNABLE_CREATE_COMMENT:
                case NotificationService::ERROR_UNABLE_CREATE_NOTIFICATION:
                case NotificationService::ERROR_UNABLE_UPDATE_NOTIFICATION:
                case NotificationService::ERROR_NOTIFICATION_NOT_FOUND:
                    throw new Http422Exception($e->getMessage(), (array) $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), (array) $e);
            }
        }
    }

    /**
     * Notify post owner
     * 
     * @param $comment
     * @param $message
     * @param $userId
     * 
     * @return void
     */
    private function notify($commentId, $message, $userId) {
        $this->notificationService->createNotification((object) [
            'commentId' => $commentId,
            'message' => $message,
            'userId' => $userId
        ]);
    }

    /**
     * Returns comment list
     *
     * @return array
     */
    public function getAllAction()
    {
        $page = $this->request->get('page');
        $limit = $this->request->get('limit');

        try {
            $commentsList = $this->commentService->getAllComments();

            return Helper::paginate($commentsList, $page, $limit);
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), (array) $e);
        }
    }

    /**
     * Return specific comment
     *
     * @param int $commentId
     * 
     * @return array
     */
    public function getAction($commentId)
    {
        try {
            return $this->commentService->getComment($commentId);
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), (array) $e);
        }
    }

    /**
     * Delete an existing comment
     *
     * @param int $commentId
     * 
     * @return void
     */
    public function deleteAction($commentId)
    {
        $data = $this->request->getJsonRawBody();
        $errors = $this->validate($data);

        if (empty($data->user_id)) {
            $errors['user_id'] = 'User is required';
        }

        if ($errors) {
            $exception = new Http400Exception(_('Input parameters validation error'));
            throw $exception->addErrorDetails($errors);
        }

        try {
            $this->commentService->deleteComment($commentId, $data->user_id);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case CommentService::ERROR_COMMENT_NOT_FOUND:
                    throw new Http404Exception($e->getMessage(), (array) $e);
                case CommentService::ERROR_UNABLE_DELETE_COMMENT:
                    throw new Http422Exception($e->getMessage(), (array) $e);
                case CommentService::ERROR_COMMENT_OWNER_USER:
                    throw new Http403Exception($e->getMessage(), (array) $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), (array) $e);
            }
        }
    }

    /**
     * Delete all comments of a post
     *
     * @param int $postId
     * 
     * @return void
     */
    public function deleteAllAction($postId)
    {
        $data = $this->request->getJsonRawBody();
        $errors = $this->validate($data);

        if (empty($data->user_id)) {
            $errors['user_id'] = 'User is required';
        }

        if ($errors) {
            $exception = new Http400Exception(_('Input parameters validation error'));
            throw $exception->addErrorDetails($errors);
        }

        try {
            $this->commentService->deleteAllComments($postId, $data->user_id);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case PostService::ERROR_POST_NOT_FOUND:
                case CommentService::ERROR_UNABLE_DELETE_COMMENT:
                    throw new Http422Exception($e->getMessage(), (array) $e);
                case CommentService::ERROR_COMMENT_OWNER_USER:
                    throw new Http403Exception($e->getMessage(), (array) $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), (array) $e);
            }
        }
    }

    /**
     * Validate the comment data values
     * 
     * @param object $data
     * 
     * @return array
     */
    private function validate(object $data) {
        $errors = [];

        if (!empty($data->user_id) && !preg_match('/^[0-9]+$/', $data->user_id)) {
            $errors['user_id'] = 'User id must consist of a number';
        }

        if (!empty($data->post_id) && !preg_match('/^[0-9]+$/', $data->post_id)) {
            $errors['post_id'] = 'Post id must consist of a number';
        }

        if (!empty($data->comment) && !is_string($data->comment)) {
            $errors['comment'] = 'Content must consist of a text';
        }

        if (!empty($data->price) && !preg_match('/^[0-9]+$/', $data->price)) {
            $errors['price'] = 'price must consist of a number';
        }

        return $errors;
    }

    /**
     * Validate required comment data
     * 
     * @param object $data
     * 
     * @return array
     */
    private function requiredValidate(object $data) {
        $errors = [];

        if (empty($data->user_id)) {
            $errors['user_id'] = 'User id is required';
        }

        if (empty($data->post_id)) {
            $errors['post_id'] = 'Post id is required';
        }

        if (empty($data->comment)) {
            $errors['comment'] = 'Comment is required';
        }

        return $errors;
    }
}