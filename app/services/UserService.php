<?php

namespace App\Services;

use App\Models\User;
use App\Exceptions\ServiceExceptions\ServiceException;

/**
 * business logic for users
 *
 * Class UserService
 */
class UserService extends AbstractService
{
    /** Unable to create user */
    const ERROR_UNABLE_CREATE_USER = 11001;
    /** Unable to find users */
    const ERROR_UNABLE_UPDATE_USER = 11002;
    /** User not found */
    const ERROR_USER_NOT_FOUND = 11003;
    /** Unable to delete user */
    const ERROR_UNABLE_DELETE_USER = 11004;

    /**
     * Creating a new user
     *
     * @param object $userData
     * 
     * @return void
     */
    public function createUser(object $userData)
    {
        try {
            $user   = new User();
            $result = $user->setUsername($userData->username)
                ->setPassword(password_hash($userData->password, PASSWORD_DEFAULT))
                ->setEmail($userData->email)
                ->setName($userData->name)
                ->setMoney($userData->money)
                ->setSubscriber($userData->subscriber)
                ->create();

            if (!$result) {
                throw new ServiceException('Unable to create user', self::ERROR_UNABLE_CREATE_USER);
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new ServiceException('User already exists', self::ERROR_ALREADY_EXISTS);
            } else {
                throw new ServiceException($e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     * Getting all users
     * 
     * @return array
     */
    public function getAllUsers()
    {
        try {
            $users = (new User())->find();

            foreach ($users as $user) {
                $usersList[] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name,
                    'email' => $user->email,
                    'money' => $user->money,
                    'subscriber' => $user->subscriber,
                ];
            }

            return $usersList;
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Find user with Id
     * 
     * @param int $userId
     * 
     * @return array
     */
    public function findUser(int $userId)
    {
        try {
            $user = (new User())->findFirstById($userId);

            if (!$user) {
                throw new ServiceException("User not found with id {$userId}", self::ERROR_USER_NOT_FOUND);
                
            }
            return $user;
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Updating an users
     * 
     * @param int $userId
     * @param object $data
     * 
     * @return array
     */
    public function updateUser(int $userId, object $data)
    {
        try {
            $user = $this->findUser($userId);

            $password = $user->getPassword();
            if ($data->password) {
                $password = password_hash($userData->password, PASSWORD_DEFAULT);
            }
            
            $user->setUsername($data->username ?: $user->getUsername())
                 ->setPassword($password)
                 ->setEmail($data->email ?: $user->getEmail())
                 ->setName($data->name ?: $user->getName())
                 ->setMoney($data->money === null ? $user->getMoney() : $data->money)
                 ->setSubscriber($data->subscriber === null ? $user->getSubscriber() : (int) $data->subscriber)
                 ->save();

            return [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'money' => $user->getMoney(),
                'subscriber' => $user->getSubscriber(),
            ];
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete an user
     * 
     * @param int $userId
     * 
     * @return void
     */
    public function deleteUser(int $userId)
    {
        try {
            $user = $this->findUser($userId);
            
            $deleted = $user->delete();

            if (!$deleted) {
                throw new ServiceException("Unable to delete user", self::ERROR_UNABLE_DELETE_USER, $deleted->getMessages());
            }
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }
}