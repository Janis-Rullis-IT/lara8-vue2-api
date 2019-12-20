<?php
/**
 * Operate with products.
 * 
 * For query logging:
 * \DB::enableQueryLog();
 * dd(\DB::getQueryLog());
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

	use SoftDeletes;

	protected $table = 'product';
	static $types = ['other' => 1, 'pizza' => 1, 'soup' => 1, 'salad' => 1];

	/**
	 * Get a `where` part for a query builder.
	 * 
	 * @param string $type = 'other' // 'other', 'pizza', 'soup', 'salad'.
	 * @return QueryBuilder
	 */
	public static function getWhere($type = 'other', $fields = ['product.id', 'product.title', 'product.slug', 'product.hash', 'product.price', 'product.type'])
	{
		return static::select($fields)->where('type', $type);
	}

	/**
	 * Get a `where` part for a query builder.
	 * 
	 * @param string $type = 'other' // 'other', 'pizza', 'soup', 'salad'.
	 * @return QueryBuilder
	 */
	public static function getWhereHash($hash, $type = 'other', $fields = [])
	{
		return static::getWhere($type, $fields)->where('hash', $hash);
	}

	/**
	 * Collect one by $hash.
	 * 
	 * @param string $hash
	 * @param string $type = 'other' // 'other', 'pizza', 'soup', 'salad'.
	 * @return Product|null
	 */
	public static function findByHash($hash, $type = 'other', $fields = ['product.id', 'product.title', 'product.slug', 'product.hash', 'product.price', 'product.type'], $toArray = false)
	{
		$data = static::getWhereHash($hash, $type, $fields)->first();
		return $toArray && !empty($data) ? $data->toArray() : $data;	
	}

	/**
	 * Get a `where` part for a query builder.
	 * 
	 * @param string $type = 'other' // 'other', 'pizza', 'soup', 'salad'.
	 * @return QueryBuilder
	 */
	public static function getWhereSlug($slug, $type = 'other', $fields = [])
	{
		return static::getWhere($type, $fields)->where('slug', $slug);
	}

	/**
	 * Collect one by slug.
	 * 
	 * @param string $slug
	 * @param string $type = 'other' // 'other', 'pizza', 'soup', 'salad'.
	 * @return Product|null
	 */
	public static function findBySlug($slug, $type = 'other', $fields = ['product.id', 'product.title', 'product.slug', 'product.hash', 'product.price', 'product.type'], $toArray = false)
	{
		$data = static::getWhereSlug($slug, $type, $fields)->first();
		return $toArray && !empty($data) ? $data->toArray() : $data;		
	}

	/**
	 * #3108 Collect products with a default values set for the most common use.
	 * 
	 * @param type $fields
	 * @param type $keyBy
	 * @param type $toArray
	 * @param type $limit
	 * @param type $type
	 * @return type
	 */
	static public function getList($fields = ['product.title', 'product.slug', 'product.hash', 'product.price', 'product.type'], $keyBy = 'hash', $toArray = false, $limit = 0, $type = 'other')
	{
		$q = static::getWhere($type, $fields);
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
	public static function doesExist($hash, $type = 'other', $autoFormat = true)
	{
		$hash = $autoFormat ? static::getFormattedStr($hash) : $hash;
		return static::isValidHash($hash) ? static::getWhere($type)->count() > 0 : false;
	}

	/**
	 * Get a formatted string.
	 * @param string $str
	 * @return string
	 * 
	 * \App\Models\Product::getFormattedStr('<iframe>  Cheese Pizza</iframe>');
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
	 * \App\Models\Product::isValidTitle('Cheese Pizza');
	 * > true
	 * 
	 * \App\Models\Product::isValidTitle('');
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
	 * \App\Models\Product::isValidStr('Cheese Pizza');
	 * > true
	 * 
	 * \App\Models\Product::isValidStr('');
	 * > false
	 */
	public static function isValidStr($str)
	{
		return !empty($str);
	}

	/**
	 * Check if the hash meets the requirements.
	 *
	 * @param type $hash
	 * @return type
	 * 
	 * \App\Models\Product::isValidHash('a9993e364706816aba3e25717850c26c9cd0d89d');
	 * > true
	 * 
	 * \App\Models\Product::isValidHash('a9993e364706816aba3e25717850c26c9cd0d89d-too-long');
	 * > false
	 * 
	 * \App\Models\Product::isValidHash('too-short');
	 * > false

	 */
	public static function isValidHash($hash)
	{
		return strlen($hash) === 40;
	}

	/**
	 * Check if the passed type is allowed.
	 * 
	 * @param string $type
	 * @return boolean
	 * 
	 * \App\Models\Product::isValidType('pizza');
	 * > true
	 * 
	 * \App\Models\Product::isValidType('invalid-type');
	 * > false
	 */
	public static function isValidType($type)
	{
		return isset(static::$types[$type]);
	}

	/**
	 * Insert only if it does not exist.
	 * 
	 * @param string $title
	 * @param string $type = 'other' // 'other', 'pizza', 'soup', 'salad'.
	 * @param boolean $validateTitle = true
	 * @param boolean $autoFormat = true
	 * @return Product|boolean
	 * 
	 * \App\Models\Product::insertIfNotExist('Cheese Pizza')
	 * >  [
	  "id" => 1
	  "title" => "Cheese Pizza"
	  "slug" => "cheese-pizza"
	  "hash" => "03bebe9c4abcb31d924e7c79af5ffd721c8b6877"
	  "type" => "other"
	  "price" => null
	  "created_at" => "2019-12-20 07:43:21"
	  "updated_at" => "2019-12-20 07:43:21"
	  "deleted_at" => null
	  "sys_info" => null
	  ]
	 * 
	 * \App\Models\Product::insertIfNotExist('Cheese Pizza', 'soup')v
	 * >  [
	  "id" => 2
	  "title" => "Cheese Pizza"
	  "slug" => "cheese-pizza"
	  "hash" => "1a227f66fcfd8682e26723441a9b4a3b61399dce"
	  "type" => "soup"
	  "price" => null
	  "created_at" => "2019-12-20 07:44:41"
	  "updated_at" => "2019-12-20 07:44:41"
	  "deleted_at" => null
	  "sys_info" => nu
	 */
	public static function insertIfNotExist($title, $type = 'other', $validateTitle = true, $autoFormat = true)
	{
		$title = $autoFormat ? static::getFormattedStr($title) : $title;

		// Require the field to be in a valid format.
		if ($validateTitle) {
			if (!static::isValidTitle($title)) {
				return 'product.title_invalid';
			}
		}

		if (!static::isValidType($type)) {
			return 'product.type_invalid';
		}

		$hash = static::getHash($title . '-' . $type);
		$item = static::findByHash($hash);

		if (empty($item)) {
			$item = new static;
			return $item->insertOne($title, $type, $hash);
		}
		return $item;
	}

	/**
	 * Generate a unique SHA1 hash (40 chars) from the passed string.   * 
	 * @param string $str
	 * @return string $hash - SHA1 hash (40 chars)
	 * 
	 * \App\Models\Product::getHash('abc');
	 * > a9993e364706816aba3e25717850c26c9cd0d89d
	 */
	public static function getHash($str)
	{
		return sha1($str);
	}

	/** 	 
	 * Generate a slug from a string.
	 * @param type $str
	 * @return string
	 * 
	 * \App\Models\Product::getSlug('  Cheese Pizza	');
	 * > 'cheese-pizza'
	 */
	public static function getSlug($str)
	{
		return str_slug($str);
	}

	/**
	 * @param type $title
	 * @param type $type
	 * @param type $hash
	 * @return boolean
	 */
	private function insertOne($title, $type, $hash)
	{
		$this->title = $title;
		$this->hash = $hash;
		$this->type = $type;
		$this->slug = static::getSlug($title);

		if ($this->save()) {
			return static::findByHash($this->hash, $this->type);
		}
		return false;
	}
}
