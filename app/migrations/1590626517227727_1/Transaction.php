<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class TransactionMigration_1590626517227727
 */
class TransactionMigration_1590626517227727 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('Transaction', [
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
                        'comment_id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'user_id'
                        ]
                    ),
                    new Column(
                        'price',
                        [
                            'type' => Column::TYPE_DECIMAL,
                            'notNull' => true,
                            'size' => 10,
                            'scale' => 2,
                            'after' => 'comment_id'
                        ]
                    ),
                    new Column(
                        'type',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 100,
                            'after' => 'price'
                        ]
                    ),
                    new Column(
                        'datetime',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'default' => "CURRENT_TIMESTAMP",
                            'notNull' => true,
                            'after' => 'type'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('Transaction_user_FK', ['user_id'], ''),
                    new Index('Transaction_comment_FK', ['comment_id'], '')
                ],
                'references' => [
                    new Reference(
                        'Transaction_comment_FK',
                        [
                            'referencedSchema' => 'social_media',
                            'referencedTable' => 'Comment',
                            'columns' => ['comment_id'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'NO ACTION',
                            'onDelete' => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'Transaction_user_FK',
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
                    'auto_increment' => '138',
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
        $this->batchInsert('Transaction', [
                'id',
                'user_id',
                'comment_id',
                'price',
                'type',
                'datetime'
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
        $this->batchDelete('Transaction');
    }

}
