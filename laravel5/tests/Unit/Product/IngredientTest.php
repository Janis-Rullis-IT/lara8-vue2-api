<?php
namespace Tests\Product;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

	class IngredientTest extends TestCase
{
	use DatabaseTransactions;

//	function tearDown(): void
//	{
//		\DB::statement("TRUNCATE`product`");
//		\DB::statement("TRUNCATE`ingredient`");
//	}

	public function testHash()
	{		
		$hash = \App\Models\Product\Ingredient::getHash('abc');
		$this->assertEquals(40, strlen($hash), 'Hash must be exactly 40 chars long.');
		$this->assertTrue(\App\Models\Product\Ingredient::isValidHash($hash), 'This hash should be valid.');
		$this->assertFalse(\App\Models\Product\Ingredient::isValidHash($hash . '-too-long'), 'This hash is too long.');
		$this->assertFalse(\App\Models\Product\Ingredient::isValidHash('too-short'), 'This hashis too short.');
	}

	public function testTitle()
	{
		$this->assertTrue(\App\Models\Product\Ingredient::isValidTitle('Cheese'), 'Title must be valid.');
		$this->assertFalse(\App\Models\Product\Ingredient::isValidTitle(''), 'Empty title is not valid.');
	}

	public function testInsert()
	{		
		$this->assertEquals(0, \App\Models\Product\Ingredient::count(), "Testing tables should be empty after each test.");

		$this->assertEquals('ingredient.product_does_not_exist', \App\Models\Product\Ingredient::insertIfNotExist('Cheese', 7, 10), 'Such product does not exist.');
		
		$product1 = \App\Models\Product::insertIfNotExist('Cheese Pizza');
		$ingredient1 = \App\Models\Product\Ingredient::insertIfNotExist('Cheese', $product1->id, 10);
		$this->assertNotEmpty($ingredient1, 'Ingredient should be inserted.');
		$this->assertEquals(1, \App\Models\Product\Ingredient::count(), "Item should be inserted.");		
		$this->assertEquals($ingredient1->hash, \App\Models\Product\Ingredient::getHash(implode('-', ['Cheese', $product1->id, 10])), 'Inserted hash is invalid.');
		$this->assertEquals($ingredient1->price, 10);
		$this->assertEquals($ingredient1->product_id, $product1->id);
		
		$foundByHash = \App\Models\Product\Ingredient::findByHash($ingredient1->hash);
		$this->assertNotEmpty($foundByHash, 'The item is in the DB. Should be found by the hash.');
		$this->assertEquals($ingredient1->hash, $foundByHash->hash, 'The found item is incorrect.');
		$ingredient2 = \App\Models\Product\Ingredient::insertIfNotExist('Tomato', $product1->id, 200);
		$this->assertEquals($ingredient1->product_id, $ingredient2->product_id);
		$this->assertNotEquals($ingredient1->hash, $ingredient2->hash);
	}

	public function testCollection()
	{
		$this->assertEquals(0, \App\Models\Product\Ingredient::count(), "Testing tables should be empty after each test.");
		$product1 = \App\Models\Product::insertIfNotExist('Cheese Pizza');
		
		$ingredient1 = \App\Models\Product\Ingredient::insertIfNotExist('Cheese', $product1->id, 10);
		$ingredient2 = \App\Models\Product\Ingredient::insertIfNotExist('Tomato', $product1->id, 200);
		
		$this->assertEquals(2, \App\Models\Product\Ingredient::count());
		$this->assertEquals(2, count(\App\Models\Product\Ingredient::getList($product1->id)));
	}
	
	// TODO: Add endpoint tests.
}
