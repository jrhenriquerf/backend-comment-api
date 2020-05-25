<?php

namespace App\Services;

use App\Models\Transaction;
use App\Exceptions\ServiceExceptions\ServiceException;

/**
 * business logic for transactions
 *
 * Class TransactionService
 */
class TransactionService extends AbstractService
{
    private $tax = 0.1;
    private $taxValue;

    /** Unable to create transaction */
    const ERROR_UNABLE_CREATE_TRANSACTION = 15001;

    /**
     * Creating a new transaction
     *
     * @param int $userId
     * @param int $commentId
     * 
     * @return void
     */
    public function buyHighlight(int $userId, int $commentId)
    {
        try {
            $comment = $this->commentService->findComment($commentId);
            $this->price = $comment->getPrice();
            $this->applyTax();

            $expense = new Transaction();

            $result = $expense->setUserId($userId)
                ->setCommentId($commentId)
                ->setPrice($this->price)
                ->setType('expense')
                ->create();

            if (!$result) {
                throw new ServiceException('Unable to create transaction', self::ERROR_UNABLE_CREATE_TRANSACTION);
            }

            $this->userService->pay($userId, $comment->getPrice());

            $invoice = new Transaction();

            $result = $invoice->setUserId($userId)
                ->setCommentId($commentId)
                ->setPrice($this->taxValue)
                ->setType('invoice')
                ->create();

            if (!$result) {
                throw new ServiceException('Unable to create transaction', self::ERROR_UNABLE_CREATE_TRANSACTION);
            }
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode());
        }
    }

    private function applyTax() {
        $this->taxValue = $this->price * $this->tax;
        $this->price -= $this->taxValue;
    }
}