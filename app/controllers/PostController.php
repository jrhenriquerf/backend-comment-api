<?php

namespace App\Controllers;

use App\Exceptions\ServiceExceptions\ServiceException;
use App\Exceptions\HttpExceptions\Http400Exception;
use App\Exceptions\HttpExceptions\Http403Exception;
use App\Exceptions\HttpExceptions\Http422Exception;
use App\Exceptions\HttpExceptions\Http500Exception;

use App\Services\AbstractService;
use App\Services\PostService;

/**
 * Operations with Posts
 */
class PostController extends AbstractController
{
    /**
     * Adding new post
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

        try {
            $this->postService->createPost($data);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case PostService::ERROR_UNABLE_CREATE_POST:
                    throw new Http422Exception($e->getMessage(), (array) $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), (array) $e);
            }
        }
    }

    /**
     * Returns posts list
     *
     * @return array
     */
    public function getAllAction()
    {
        try {
            return $this->postService->getAllPosts();
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), (array) $e);
        }
    }

    /**
     * Return specific post
     *
     * @param int $postId
     * 
     * @return array
     */
    public function getAction($postId)
    {
        try {
            return $this->postService->getPost($postId);
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), (array) $e);
        }
    }

     /**
     * Updating existing post
     *
     * @param int $postId
     * 
     * @return array
     */
    public function updateAction($postId)
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
            return $this->postService->updatePost($postId, $data);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case PostService::ERROR_POST_NOT_FOUND:
                case PostService::ERROR_UNABLE_UPDATE_POST:
                    throw new Http422Exception($e->getMessage(), (array) $e);
                case PostService::ERROR_POST_OWNER_USER:
                    throw new Http403Exception($e->getMessage(), (array) $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), (array) $e);
            }
        }
    }

    /**
     * Delete an existing post
     *
     * @param int $postId
     * 
     * @return void
     */
    public function deleteAction($postId)
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
            $this->postService->deletePost($postId, $data->user_id);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case PostService::ERROR_POST_NOT_FOUND:
                case PostService::ERROR_UNABLE_DELETE_POST:
                    throw new Http422Exception($e->getMessage(), (array) $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), (array) $e);
            }
        }
    }

    /**
     * Validate the post data values
     * 
     * @param object $data
     * 
     * @return array
     */
    private function validate(object $data) {
        $errors = [];

        if (!empty($data->user_id) && !preg_match('/^[0-9]+$/', $data->user_id)) {
            $errors['user_id'] = 'User_id must consist of a number';
        }

        if (!empty($data->content) && !is_string($data->content)) {
            $errors['content'] = 'Content must consist of a text';
        }

        if (!empty($data->attachment)
            && (!is_string($data->attachment) 
            || !preg_match('/^(http|https|ftp):\/\//', $data->attachment))) {
            $errors['attachment'] = 'attachment must consist of a link with protocol';
        }

        if (!empty($data->attachmentType)
            && (!is_string($data->attachmentType)
            || !preg_match('/^(video|image)$/', $data->attachmentType))) {
            $errors['attachmentType'] = 'attachment must consist in "video" or "image"';
        }

        return $errors;
    }

    /**
     * Validate required post data
     * 
     * @param object $data
     * 
     * @return array
     */
    private function requiredValidate(object $data) {
        $errors = [];

        if (empty($data->user_id)) {
            $errors['user_id'] = 'User is required';
        }

        if (empty($data->content)) {
            $errors['content'] = 'Content is required';
        }

        return $errors;
    }
}