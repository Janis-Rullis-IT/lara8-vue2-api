<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertProducts extends Migration
{

	public function up()
	{
		// Always use raw SQL because code's logic may change. In refactoring this would still work.
		\DB::statement("
          INSERT INTO `product` (`id`, `title`, `slug`, `hash`, `type`, `price`, `created_at`, `updated_at`, `deleted_at`, `sys_info`) VALUES
			(1, 'Cheese Pizza', 'cheese-pizza', '03bebe9c4abcb31d924e7c79af5ffd721c8b6877', 'other', NULL, '2019-12-20 06:22:54', '2019-12-20 06:22:54', NULL, NULL),
			(2, 'Apple Pizza', 'apple-pizza', '28da577a98ddfdf7c34a674e021bc9f6211ceb90', 'other', NULL, '2019-12-20 06:22:54', '2019-12-20 06:22:54', NULL, NULL),
			(3, 'Salami Pizza', 'salami-pizza', 'bebda2e6a982a25a851f64997470c02cf02ff6dd', 'other', NULL, '2019-12-20 06:22:54', '2019-12-20 06:22:54', NULL, NULL);
        ");
	}

	public function down()
	{
		\DB::statement("TRUNCATE `product`");
	}
}
