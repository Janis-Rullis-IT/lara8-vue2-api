<?php
/**
 * Operate only with products that have the specific type.
 */
namespace App\Models\Accounts\Product\ByType;

class Pizza extends \App\Models\Product\ByType
{

	static $typerField = 'type';
	static $typeerValue = 'pizza';

}
