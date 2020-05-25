<?php

namespace App\Controllers;

use App\Exceptions\ServiceExceptions\ServiceException;
use App\Exceptions\HttpExceptions\Http400Exception;
use App\Exceptions\HttpExceptions\Http422Exception;
use App\Exceptions\HttpExceptions\Http500Exception;

use App\Services\AbstractService;
use App\Services\UserService;

/**
 * Operations with Users: CRUD
 */
class UserController extends AbstractController
{
    /**
     * Adding user
     */
    public function addAction()
    {
        $data = $this->request->getJsonRawBody();
        $invalidErrors = $this->validate($data);
        $requiredData = $this->requiredValidate($data);
        
        $errors = array_merge($requiredData, $invalidErrors);

        $data->money = $data->money ?: 0;

        $data->subscriber = $data->subscriber ? 1 : 0;

        if ($errors) {
            $exception = new Http400Exception(_('Input parameters validation error'));
            throw $exception->addErrorDetails($errors);
        }

        try {
            $this->userService->createUser($data);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case AbstractService::ERROR_ALREADY_EXISTS:
                case UserService::ERROR_UNABLE_CREATE_USER:
                    throw new Http422Exception($e->getMessage(), (array) $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), (array) $e);
            }
        }
    }

    /**
     * Returns user list
     *
     * @return array
     */
    public function getAllAction()
    {
        try {
            return $this->userService->getAllUsers();
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), (array) $e);
        }
    }

     /**
     * Updating existing user
     *
     * @param int $userId
     * 
     * @return array
     */
    public function updateAction($userId)
    {
        $data = $this->request->getJsonRawBody();
        $errors = $this->validate($data);

        $data->money = isset($data->money) ? $data->money : null;

        $data->subscriber = isset($data->subscriber) ? $data->subscriber : null;

        if ($errors) {
            $exception = new Http400Exception(_('Input parameters validation error'));
            throw $exception->addErrorDetails($errors);
        }

        try {
            return $this->userService->updateUser($userId, $data);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case UserService::ERROR_USER_NOT_FOUND:
                case UserService::ERROR_UNABLE_UPDATE_USER:
                    throw new Http422Exception($e->getMessage(), (array) $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), (array) $e);
            }
        }
    }

    /**
     * Delete an existing user
     *
     * @param int $userId
     * 
     * @return void
     */
    public function deleteAction($userId)
    {
        try {
            $this->userService->deleteUser($userId);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case UserService::ERROR_USER_NOT_FOUND:
                case UserService::ERROR_UNABLE_DELETE_USER:
                    throw new Http422Exception($e->getMessage(), (array) $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), (array) $e);
            }
        }
    }

    /**
     * Validate the user data values
     * 
     * @param object $data
     * 
     * @return array
     */
    private function validate(object $data) {
        $errors = [];

        if (!empty($data->username) 
            && (!is_string($data->username) 
            || !preg_match('/^[A-z0-9_-]{3,16}$/', $data->username))) {
            $errors['username'] = 'Username must consist of 3-16 letters, numbers or \'-\' and \'_\' symbols';
        }

        if (!empty($data->password) 
            && (!is_string($data->password) 
            || !preg_match('/^[A-z0-9_-]{6,18}$/', $data->password))) {
            $errors['password'] = 'Password must consist of 6-18 letters, numbers or \'-\' and \'_\' symbols';
        }

        if (!empty($data->email) 
            && !is_string($data->email)) {
            $errors['email'] = 'String expected';
        }

        if (!empty($data->name) 
            && !is_string($data->name)) {
            $errors['name'] = 'String expected';
        }

        return $errors;
    }

    /**
     * Validate required user data
     * 
     * @param object $data
     * 
     * @return array
     */
    private function requiredValidate(object $data) {
        $errors = [];

        if (empty($data->username)) {
            $errors['username'] = 'Username required';
        }

        if (empty($data->password)) {
            $errors['password'] = 'Password required';
        }

        if (empty($data->email)) {
            $errors['email'] = 'Email required';
        }

        if (empty($data->name)) {
            $errors['name'] = 'Name required';
        }

        return $errors;
    }
}