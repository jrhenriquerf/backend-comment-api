<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class CommentMigration_1590626517227727
 */
class CommentMigration_1590626517227727 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('Comment', [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'size' => 1,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'user_id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'post_id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'user_id'
                        ]
                    ),
                    new Column(
                        'highlight',
                        [
                            'type' => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'post_id'
                        ]
                    ),
                    new Column(
                        'price',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => false,
                            'size' => 1,
                            'after' => 'highlight'
                        ]
                    ),
                    new Column(
                        'comment',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 1024,
                            'after' => 'price'
                        ]
                    ),
                    new Column(
                        'datetime',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'default' => "CURRENT_TIMESTAMP",
                            'notNull' => true,
                            'after' => 'comment'
                        ]
                    ),
                    new Column(
                        'datetime_highlight',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'notNull' => false,
                            'after' => 'datetime'
                        ]
                    ),
                    new Column(
                        'deleted_at',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'notNull' => false,
                            'after' => 'datetime_highlight'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('Comment_post_FK', ['post_id'], ''),
                    new Index('Comment_user_FK', ['user_id'], '')
                ],
                'references' => [
                    new Reference(
                        'Comment_post_FK',
                        [
                            'referencedSchema' => 'social_media',
                            'referencedTable' => 'Post',
                            'columns' => ['post_id'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'NO ACTION',
                            'onDelete' => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'Comment_user_FK',
                        [
                            'referencedSchema' => 'social_media',
                            'referencedTable' => 'User',
                            'columns' => ['user_id'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'NO ACTION',
                            'onDelete' => 'NO ACTION'
                        ]
                    )
                ],
                'options' => [
                    'table_type' => 'BASE TABLE',
                    'auto_increment' => '89',
                    'engine' => 'InnoDB',
                    'table_collation' => 'utf8mb4_0900_ai_ci'
                ],
            ]
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        $this->batchInsert('Comment', [
                'id',
                'user_id',
                'post_id',
                'highlight',
                'price',
                'comment',
                'datetime',
                'datetime_highlight',
                'deleted_at'
            ]
        );
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        $this->batchDelete('Comment');
    }

}
