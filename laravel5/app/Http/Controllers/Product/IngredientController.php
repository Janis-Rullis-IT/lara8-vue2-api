<?php
/**
 * Ingredients used in products like pizza.
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use \App\Models\Product;
use \App\Models\Product\Ingredient;

class IngredientController extends Controller
{

	/**
	 * Return a list of ingredients
	 * 
	 * GET /products/{productHash}/ingredients
	 * @param string $productHash
	 * @return type
	 */
	public function index(string $productHash)
	{
		$status = Product::isValidHash(Product::getFormattedStr($productHash));
		if ($status !== true) {
			return response()->json(['errors' => [$status]], 400);
		}
		$foundByHash = Product::findByHash($productHash);
		if (empty($foundByHash)) {
			return response()->json(['errors' => ['ingredient.product_does_not_exist']], 400);
		}

		return response()->json(['data' => Ingredient::getList($foundByHash->id, ['ingredient.title', 'ingredient.hash', 'ingredient.price'], false, true), 'success' => true], 200);
	}

	/** 	 
	 * Add a new ingredient to the specified product.
	 * 
	 * POST /products/{producthash}/ingredients
	 * 
	 * @param string $producthash
	 * @return JSON
	 */
	public function store(string $productHash)
	{
		$status = Product::isValidHash(Product::getFormattedStr($productHash));
		if ($status !== true) {
			return response()->json(['errors' => [$status]], 400);
		}
		$post = request()->only('title', 'price');
		if (!isset($post['title'], $post['price'])) {
			return response()->json(['errors' => ['ingredient.invalid_request.']], 400);
		}
		$foundByHash = Product::findByHash($productHash);
		if (empty($foundByHash)) {
			return response()->json(['errors' => ['ingredient.product_does_not_exist']], 400);
		}
		$data = \App\Models\Product\Ingredient::insertIfNotExist($post['title'], $foundByHash->id, $post['price']);
		if (empty($data['hash'])) {
			return response()->json(['errors' => $data], 400);
		} else {
			$data['product_price'] = \App\Models\Product::getPrice($foundByHash->id)['price'];
			return response()->json(['data' => $data, 'success' => true], 200);
		}
	}

	/** 	 
	 * Remove an ingredient.
	 * 
	 * DELETE /products/{$productHash}/ingredients/{$ingredientHash}
	 * 
	 * @param string $productHash
	 * @param string $ingredientHash
	 * @return JSON
	 */
	// TODO: Should use a single hash?
	public function remove(string $productHash, string $ingredientHash)
	{
		// TODO: Currently $productHash is not used but could be used to remove 
		// the item from the specific product but keep for selection for others.
		$status = Ingredient::isValidHash(Product::getFormattedStr($ingredientHash));
		if ($status !== true) {
			return response()->json(['errors' => [$status]], 400);
		}
		$foundByHash = Ingredient::findByHash($ingredientHash, ['ingredient.id', 'ingredient.product_id']);
		if (empty($foundByHash)) {
			return response()->json(['errors' => ['ingredient.does_not_exist']], 400);
		} else {
			$productId = $foundByHash->product_id;
			$foundByHash->delete();
			
			// Update product's total price.
			\App\Models\Product::updatePrice($productId);

			// Return the changed total price.
			return response()->json(['data' => ["product_price" => \App\Models\Product::getPrice($productId)['price']], 'success' => true], 200);
		}
	}

	public function sequence(string $productHash)
	{
		dd(request()->all(), $producthash);
	}
}
