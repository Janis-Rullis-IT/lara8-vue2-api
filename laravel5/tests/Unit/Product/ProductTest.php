<?php
namespace Tests\Product;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductTest extends TestCase
{
	use DatabaseTransactions;
//
//	function tearDown(): void
//	{
//		\DB::statement("TRUNCATE`product`");
//	}

	public function testtHash()
	{
		$hash = \App\Models\Product::getHash('abc');
		$this->assertEquals(40, strlen($hash), 'Hash must be exactly 40 chars long.');
		$this->assertTrue(\App\Models\Product::isValidHash($hash), 'This hash should be valid.');
		$this->assertFalse(\App\Models\Product::isValidHash($hash . '-too-long'), 'This hash is too long.');
		$this->assertFalse(\App\Models\Product::isValidHash('too-short'), 'This hashis too short.');
	}

	public function testtSlug()
	{
		$this->assertEquals('cheese-pizza', \App\Models\Product::getSlug('  Cheese Pizza	'), 'Incorrect slug.');
		$this->assertEquals('Cheese Pizza', \App\Models\Product::getFormattedStr('<iframe>  Cheese Pizza</iframe>'), 'getFormattedStr() must remove tags.');
	}

	public function testTitle()
	{
		$this->assertTrue(\App\Models\Product::isValidTitle('Cheese Pizza'), 'Title must be valid.');
		$this->assertFalse(\App\Models\Product::isValidTitle(''), 'Empty title is not valid.');
	}

	public function testInsert()
	{
		$this->assertEquals(0, \App\Models\Product::count(), "Testing tables should be empty after each test.");

		$this->assertEquals('product.type_invalid', \App\Models\Product::insertIfNotExist('Cheese Pizza', 'invalid-type'));

		$product = \App\Models\Product::insertIfNotExist('Cheese Pizza');
		$this->assertNotEmpty($product, 'The product is in the DB. Should be found by the hash.');
		$this->assertEquals(1, \App\Models\Product::count(), "Item should be inserted.");
		$this->assertEquals($product->type, 'other', "Default product type should be 'other'.");
		$this->assertEquals($product->hash, \App\Models\Product::getHash('Cheese Pizza-other'), 'Inserted hash is invalid.');
		$this->assertEquals($product->slug, \App\Models\Product::getSlug('Cheese Pizza'), 'Inserted slug is invalid.');

		$foundByHash = \App\Models\Product::findByHash($product->hash);
		
		$this->assertNotEmpty($foundByHash, 'The product is in the DB. Should be found by the hash.');		
		$this->assertEquals($product->hash, $foundByHash['hash'], 'The found item is incorrect.');

		$foundBySlug = \App\Models\Product::findBySlug($product->slug);
		$this->assertNotEmpty($foundByHash, 'The product is in the DB. Should be found by the slug.');
		$this->assertEquals($product->hash, $foundBySlug['hash'], 'The found item is incorrect.');

		$product2 = \App\Models\Product::insertIfNotExist('Cheese Pizza');
		$this->assertNotEmpty($product2, 'Should return the first product because the type and title is the same.');
		$this->assertEquals($product2->hash, $product->hash, 'The 2nd product should be the first product because the type and title is the same.');

		$soup = \App\Models\Product::insertIfNotExist('Cheese Pizza', 'soup');
		$this->assertNotEmpty($product, 'The product is in the DB. Should be found by the hash.');
		$this->assertNotEquals($soup->id, $product->hash, 'The 3nd product should be different than the first product because the title is the same but the type is different.');

		$this->assertEquals(2, \App\Models\Product::count());
	}

	public function testCollection()
	{
		$this->assertEquals(0, \App\Models\Product::count(), "Testing tables should be empty after each test.");
		$product1 = \App\Models\Product::insertIfNotExist('Cheese Pizza');
		$product2 = \App\Models\Product::insertIfNotExist('Apple Pizza');
		$product3 = \App\Models\Product::insertIfNotExist('Salami Pizza');
		$this->assertEquals(3, \App\Models\Product::count());
		$this->assertEquals(3, count(\App\Models\Product::getList()));
	}
	
	// TODO: Add endpoint tests.
}
