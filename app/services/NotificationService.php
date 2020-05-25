<?php

namespace App\Services;

use App\Models\Notification;
use App\Exceptions\ServiceExceptions\ServiceException;

/**
 * business logic for notifications
 *
 * Class NotificationService
 */
class NotificationService extends AbstractService
{
    /** Unable to create notification */
    const ERROR_UNABLE_CREATE_NOTIFICATION = 14001;
    /** Notification not found */
    const ERROR_NOTIFICATION_NOT_FOUND = 14002;
    /** Unable to update notification */
    const ERROR_UNABLE_UPDATE_NOTIFICATION = 14003;

    /**
     * Creating a new notification
     *
     * @param object $data
     * 
     * @return void
     */
    public function createNotification(object $data)
    {
        try {
            $notification   = new Notification();
            $result = $notification->setCommentId($data->commentId)
                ->setMessage($data->message)
                ->setUserId($data->userId)
                ->create();

            if (!$result) {
                throw new ServiceException('Unable to create notification', self::ERROR_UNABLE_CREATE_NOTIFICATION);
            }
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Creating a new notification
     *
     * @param object $data
     * 
     * @return void
     */
    public function setNotificationExpire(int $notificationId)
    {
        $expireAt = date('Y-m-d H:i:s', strtotime("+1 hour"));

        try {
            $notification   = $this->findNotification($notificationId);
            $result = $notification->setExpireAt($expireAt)->save();

            if (!$result) {
                throw new ServiceException('Unable to update notification', self::ERROR_UNABLE_UPDATE_NOTIFICATION);
            }
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Getting all notifications
     * 
     * @param int $userId
     * 
     * @return array
     */
    public function getUserNotifications($userId)
    {
        $notificationsList = [];

        try {
            $notifications = Notification::query()
                ->where('user_id = :userId:')
                ->andWhere('(expire_at is null or expire_at > :expireAt:)')
                ->bind([ 
                    'userId' => $userId,
                    'expireAt' => date('Y-m-d H:i:s')
                ])
                ->orderBy('datetime desc')
                ->execute();

            foreach ($notifications as $notification) {
                $this->setNotificationExpire($notification->getId());
                
                $notificationsList[] = [
                    'message' => $notification->getMessage(),
                    'comment' => [
                        'comment' => $notification->comment->getComment(),
                        'user' => $notification->comment->user->getName(),
                        'highlight' => $notification->comment->getHighLight(),
                        'price' => $notification->comment->getPrice(),
                        'post' => $notification->comment->getPostId()
                    ],
                    'date' => $notification->getDateTime()
                ];
            }

            return [
                'data' => $notificationsList,
                'total' => count($notificationsList)
            ];
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    /**
     * Find user with Id
     * 
     * @param int $notificationId
     * 
     * @return array
     */
    public function findNotification(int $notificationId)
    {
        try {
            $notification = (new Notification())->findFirstById($notificationId);

            if (!$notification) {
                throw new ServiceException("Notification not found with id {$notificationId}", self::ERROR_NOTIFICATION_NOT_FOUND);
                
            }
            return $notification;
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }
}