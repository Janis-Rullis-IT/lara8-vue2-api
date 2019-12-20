<?php
/**
 * Operate with ingredients.
 * 
 * For query logging:
 * \DB::enableQueryLog();
 * dd(\DB::getQueryLog());
 */
namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{

	use SoftDeletes;

	protected $table = 'ingredient';

	/**
	 * Get a `where` part for a query builder.
	 * 
	 * @return QueryBuilder
	 */
	public static function getWhere($fields = ['ingredient.id', 'ingredient.title', 'ingredient.hash', 'ingredient.price'])
	{
		return static::select($fields)->orderBy('seq', 'DESC');
	}

	/**
	 * Get a `where` part for a query builder.
	 * 
	 * @return QueryBuilder
	 */
	public static function getWhereHash($hash, $fields = [])
	{
		return static::getWhere($fields)->where('hash', $hash);
	}

	/**
	 * Collect one by $hash.
	 * 
	 * @param string $hash
	 * @return Product|null
	 */
	public static function findByHash($hash, $fields = ['ingredient.product_id', 'ingredient.title', 'ingredient.hash', 'ingredient.price'], $toArray = false)
	{
		$data = static::getWhereHash($hash, $fields)->first();
		return $toArray && !empty($data) ? $data->toArray() : $data;
	}

	/**
	 * #3108 Collect products with a default values set for the most common use.
	 * 
	 * @param type $productId
	 * @param type $fields
	 * @param type $keyBy
	 * @param type $toArray
	 * @param type $limit	 
	 * @return type
	 */
	static public function getList($productId, $fields = ['ingredient.product_id', 'ingredient.title', 'ingredient.hash', 'ingredient.price'], $keyBy = 'hash', $toArray = false, $limit = 0)
	{
		$q = static::getWhere($fields)->where('product_id', $productId);
		$q = $limit ? $q->limit($limit) : $q;
		$data = $q->get();
		$data = $keyBy ? $data->keyBy($keyBy) : $data;
		return $toArray && !empty($data) ? $data->toArray() : $data;
	}

	/**
	 * Check if a record exists.
	 * 
	 * @param string $hash
	 * @return boolean
	 */
	public static function doesExist($hash, $autoFormat = true)
	{
		$hash = $autoFormat ? static::getFormattedStr($hash) : $hash;
		return static::isValidHash($hash) ? static::getWhere()->count() > 0 : false;
	}

	/**
	 * Get a formatted string.
	 * @param string $str
	 * @return string
	 * 
	 * \App\Models\Product\Ingredient::getFormattedStr('<iframe>  Cheese Pizza</iframe>');
	 * > Cheese pizza
	 */
	public static function getFormattedStr($str)
	{
		return trim(filter_var($str, FILTER_SANITIZE_STRING));
	}

	/**
	 * Check if the title meets the requirements.
	 * 
	 * @param string $title
	 * @return boolean
	 * 
	 * \App\Models\Product\Ingredient::isValidTitle('Cheese Pizza');
	 * > true
	 * 
	 * \App\Models\Product\Ingredient::isValidTitle('');
	 * > false
	 */
	public static function isValidTitle($title)
	{
		return static::isValidStr($title);
	}

	/**
	 * Check if the string meets the requirements.
	 * 
	 * @param string $str
	 * @return boolean
	 * 
	 * \App\Models\Product\Ingredient::isValidStr('Cheese Pizza');
	 * > true
	 * 
	 * \App\Models\Product\Ingredient::isValidStr('');
	 * > false
	 */
	public static function isValidStr($str)
	{
		return !empty($str);
	}

	/**
	 * Check if the hash meets the requirements.
	 *
	 * @param string $hash
	 * @return bool
	 * 
	 * \App\Models\Product\Ingredient::isValidHash('a9993e364706816aba3e25717850c26c9cd0d89d');
	 * > true
	 * 
	 * \App\Models\Product\Ingredient::isValidHash('a9993e364706816aba3e25717850c26c9cd0d89d-too-long');
	 * > false
	 * 
	 * \App\Models\Product\Ingredient::isValidHash('too-short');
	 * > false
	 */
	public static function isValidHash(string $hash)
	{
		return strlen($hash) === 40;
	}

	/**
	 * Insert only if it does not exist.
	 * 
	 * @param string $title
	 * @param int $productId
	 * @param int $price
	 * @param bool $validateTitle
	 * @param bool $autoFormat
	 * @return \static|string
	 */
	public static function insertIfNotExist(string $title, int $productId, int $price, bool $validateTitle = true, bool $autoFormat = true)
	{
		$title = $autoFormat ? static::getFormattedStr($title) : $title;

		// Require the field to be in a valid format.
		if ($validateTitle) {
			if (!static::isValidTitle($title)) {
				return 'ingredient.title_invalid';
			}
		}

		if (!static::doesProductExist($productId)) {
			return 'ingredient.product_does_not_exist';
		}

		// TODO: Move array processing to the method itself and pass arrays instead of strings.
		$hash = static::getHash(implode('-', [$title, $productId, $price]));
		$item = static::findByHash($hash);

		if (empty($item->id)) {
			$item = new static;
			return $item->insertOne($title, $productId, $hash, $price);
		}
		return $item;
	}

	/**
	 * @param int $productId
	 * @return boolean
	 */
	public static function doesProductExist(int $productId)
	{
		return \App\Models\Product::where('id', $productId)->count() > 0;
	}

	/**
	 * Generate a unique SHA1 hash (40 chars) from the passed string.   * 
	 * @param string $str
	 * @return string $hash - SHA1 hash (40 chars)
	 * 
	 * \App\Models\Product\Ingredient::getHash('abc');
	 * > a9993e364706816aba3e25717850c26c9cd0d89d
	 */
	public static function getHash(string $str)
	{
		return sha1($str);
	}

	/** 	 
	 * Generate a slug from a string.
	 * @param type $str
	 * @return string
	 * 
	 * \App\Models\Product\Ingredient::getSlug('  Cheese Pizza	');
	 * > 'cheese-pizza'
	 */
	public static function getSlug(string $str)
	{
		return str_slug($str);
	}

	/**
	 * @param string $title
	 * @param int $productId
	 * @param string $hash
	 * @param int $price
	 * @return boolean
	 */
	private function insertOne(string $title, int $productId, string $hash, int $price)
	{
		$this->title = $title;
		$this->product_id = $productId;
		$this->price = $price;
		$this->hash = $hash;
		$this->slug = static::getSlug($title);

		if ($this->save()) {

			// Update product's total price.
			\App\Models\Product::updatePrice($productId);
			
			return static::findByHash($this->hash);
		}
		return false;
	}

	/**
	 * Keep items always ordered correctly. This a cure for parallel requests and deleted items.
	 * @param int $productId
	 * @return boolean
	 */
	public static function updateOrderNumbers(int $productId)
	{
		return \DB::statement("
			UPDATE `ingredient`
			SET `seq` = (@i := @i+1)
			WHERE `product_id` = ?
			AND `deleted_at` IS NULL			
			ORDER BY `seq` ASC
			(SELECT @i := 1)
        ", [$productId]);
	}
}
