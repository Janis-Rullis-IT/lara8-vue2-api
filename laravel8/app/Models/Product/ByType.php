<?php
/**
 * Operate only with products that have the specific type.
 */
namespace App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ByType extends \App\Models\Product
{

	static $typerField = 'type';
	static $typeerValue = 'pizza';

	/**
	 * Forces to only manipulate with products that have the specific type.
	 */
	protected static function boot()
	{
		parent::boot();

		static::addGlobalScope(function (Builder $builder) {
			$builder->where(static::$typerField, static::$typeValue);
		});
	}

	/**
	 * Product.save() but also sets `self::typerField` = `self::$typeValue`.
	 * @param array $options
	 * @return boolean
	 */
	public function save(array $options = [])
	{
		$this->{static::$typeField} = static::$typeValue;
		return parent::save();
	}
}
