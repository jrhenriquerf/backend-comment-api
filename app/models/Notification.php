<?php

namespace App\Models;

class Notification extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id;

    /**
     *
     * @var integer
     */
    protected $comment_id;

    /**
     *
     * @var string
     */
    protected $expire_at;

    /**
     *
     * @var string
     */
    protected $datetime;

    /**
     *
     * @var string
     */
    protected $message;

    /**
     *
     * @var integer
     */
    protected $user_id;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field comment_id
     *
     * @param integer $comment_id
     * @return $this
     */
    public function setCommentId($comment_id)
    {
        $this->comment_id = $comment_id;

        return $this;
    }

    /**
     * Method to set the value of field expire_at
     *
     * @param string $expire_at
     * @return $this
     */
    public function setExpireAt($expire_at)
    {
        $this->expire_at = $expire_at;

        return $this;
    }

    /**
     * Method to set the value of field datetime
     *
     * @param string $datetime
     * @return $this
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Method to set the value of field message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Method to set the value of field user_id
     *
     * @param integer $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field comment_id
     *
     * @return integer
     */
    public function getCommentId()
    {
        return $this->comment_id;
    }

    /**
     * Returns the value of field expire_at
     *
     * @return string
     */
    public function getExpireAt()
    {
        return $this->expire_at;
    }

    /**
     * Returns the value of field datetime
     *
     * @return string
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Returns the value of field message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns the value of field user_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("social_media");
        $this->setSource("Notification");
        $this->belongsTo('user_id', 'App\Models\User', 'id', ['alias' => 'User']);
        $this->belongsTo('comment_id', 'App\Models\Comment', 'id', ['alias' => 'Comment']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Notification[]|Notification|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Notification|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
