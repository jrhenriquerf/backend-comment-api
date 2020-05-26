<?php

namespace App\Controllers\Helpers;

use Phalcon\Paginator\Adapter\NativeArray;

/**
 * Helper class
 */
class Helper
{
    /**
     * Return array data parameter paginated to api response
     *
     * @param array $data
     * @param int $page
     * @param int $limit
     * 
     * @return array
     */
    public static function paginate(array $data, int $page, int $limit)
    {
        $paginator = new NativeArray(
            [
                "data"  => $data,
                "limit" => $limit,
                "page"  => $page,
            ]
        );

        $paginate = $paginator->paginate();

        return [
            'data' => $paginate->items,
            'page' => $paginate->current,
            'total' => $paginate->totalItems,
            'lastPage' => $paginate->last,
            'limit' => $paginate->limit
        ];
    }
}