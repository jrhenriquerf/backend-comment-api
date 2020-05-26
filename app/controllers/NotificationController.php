<?php

namespace App\Controllers;

use App\Exceptions\ServiceExceptions\ServiceException;
use App\Exceptions\HttpExceptions\Http400Exception;
use App\Exceptions\HttpExceptions\Http403Exception;
use App\Exceptions\HttpExceptions\Http422Exception;
use App\Exceptions\HttpExceptions\Http500Exception;

use App\Services\NotificationService;

use App\Controllers\Helpers\Helper;

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
        $page = $this->request->get('page');
        $limit = $this->request->get('limit');

        try {
            $notificationsList = $this->notificationService->getUserNotifications($userId);

            $paginatedItems = Helper::paginate($notificationsList, $page, $limit);

            $this->notificationService->expireNotifications($paginatedItems["data"], $_ENV['HOURS_EXPIRE_NOTIFICATION']);

            return $paginatedItems;
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), (array) $e);
        }
    }
}