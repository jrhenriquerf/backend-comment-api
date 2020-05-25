<?php

namespace App\Controllers;

use App\Exceptions\ServiceExceptions\ServiceException;
use App\Exceptions\HttpExceptions\Http400Exception;
use App\Exceptions\HttpExceptions\Http403Exception;
use App\Exceptions\HttpExceptions\Http422Exception;
use App\Exceptions\HttpExceptions\Http500Exception;

use App\Services\NotificationService;

/**
 * Operations with Notifications
 */
class NotificationController extends AbstractController
{
    /**
     * Returns notification list
     *
     * @param int $userId
     * 
     * @return array
     */
    public function getUserNotificationsAction($userId)
    {
        try {
            return $this->notificationService->getUserNotifications($userId);
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), (array) $e);
        }
    }
}