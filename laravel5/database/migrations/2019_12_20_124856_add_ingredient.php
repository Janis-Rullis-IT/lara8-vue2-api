<?php

/**
 * Add the `ingredient` table. Will contain items like Cheese 1 euro, Tomato 0.5 euro. Attached to products.
 * 
 * https://github.com/janis-rullis/sql/blob/master/mysql/Sample-table-with-common-fields.sql
 */
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIngredient extends Migration
{
    
    public function up()
    {
       // Always use raw SQL because code's logic may change. In refactoring this would still work.
        \DB::statement("
            CREATE TABLE IF NOT EXISTS `ingredient` (
              `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,              
              `title` VARCHAR(250) NOT NULL,
              `slug` VARCHAR(250) NOT NULL COMMENT 'May be useful if needed in the frontend links. Ex., /ingredients/pizza-1/cheese ',
              `hash` CHAR(40) NOT NULL COMMENT 'SHA1(title, product_id, price) works as an ID for API calls.',              
              `price` INT(10) UNSIGNED DEFAULT NULL COMMENT \"Updated when an ingredient has been changed. A fixed field avoids on-the-fly calculations. Base price should always be a long INT field, so any time could add a higher precision than 2 digits, easily make conversions and calc.\",              
			  `product_id` INT(10) UNSIGNED NOT NULL COMMENT 'Points to the product table.',
			  `seq` TINYINT(2) UNSIGNED NOT NULL DEFAULT '1',
          
              `created_at` timestamp NULL DEFAULT NULL,
              `updated_at` timestamp NULL DEFAULT NULL,
              `deleted_at` timestamp NULL DEFAULT NULL,
              
              `sys_info` varchar(20) DEFAULT NULL COMMENT 'In case if You need to mark it or add some flag. For inner use. For example, edited 3 JSONs by hand and mark \"#1234 upd\", so they could be identified later.',
              
              PRIMARY KEY(`id`),
              UNIQUE INDEX `hash`(`title`, `product_id`, `price`),
              INDEX `product_del`(`product_id`, `deleted_at`, `seq`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ontain items like Cheese 1 euro, Tomato 0.5 euro. Attached to products';
        ");
    }

    public function down()
    {
      \DB::statement("DROP TABLE `ingredient`");
    }
}
