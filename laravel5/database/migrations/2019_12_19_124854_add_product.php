<?php

/**
 * Add the `product` table. Will contain products like 'Cheese Pica' for 4.44. `ingredient` will point to it.
 * 
 * https://github.com/janis-rullis/sql/blob/master/mysql/Sample-table-with-common-fields.sql
 * https://github.com/janis-rullis/sql/blob/master/mysql/String/Unique-texts.md
 */
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProduct extends Migration
{
    
    public function up()
    {
       // Always use raw SQL because code's logic may change. In refactoring this would still work.
        \DB::statement("
            CREATE TABLE IF NOT EXISTS `product` (
              `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,              
              `title` VARCHAR(250) NOT NULL,
              `slug` VARCHAR(250) NOT NULL COMMENT 'Used in the frontend links. Ex., /products/pizza-1 ',
              `hash` CHAR(40) NOT NULL COMMENT 'SHA1(title) works as an ID for API calls.',
              `type` ENUM('other', 'pizza', 'soup', 'salad') NOT NULL DEFAULT 'other' COMMENT 'This allows to avoid being stuck with a one products scope and re-work-hell when a new product-type appears.',
              `price` INT(10) UNSIGNED DEFAULT NULL COMMENT \"Updated when an ingredient has been changed. A fixed field avoids on-the-fly calculations. Base price should always be a long INT field, so any time could add a higher precision than 2 digits, easily make conversions and calc.\",              
          
              `created_at` timestamp NULL DEFAULT NULL,
              `updated_at` timestamp NULL DEFAULT NULL,
              `deleted_at` timestamp NULL DEFAULT NULL,
              
              `sys_info` varchar(20) DEFAULT NULL COMMENT 'In case if You need to mark it or add some flag. For inner use. For example, edited 3 JSONs by hand and mark \"#1234 upd\", so they could be identified later.',
              
              PRIMARY KEY(`id`),
              UNIQUE INDEX `hash`(`hash`, `type`),
              INDEX `type_del`(`type`, `deleted_at`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains products like \'Cheese Pizza\' for 4.12 Eur. `ingredient` tab points to it';
        ");
    }

    public function down()
    {
      \DB::statement("DROP TABLE `product`");
    }
}
