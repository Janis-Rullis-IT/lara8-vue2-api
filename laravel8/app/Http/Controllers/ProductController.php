<?php
/**
 * Products.
 * 
 * ## Guides for the future-self to use the best code structure.
 * 
 * https://github.com/janis-rullis/dev/blob/master/Code-structures/user.vue
 * https://github.com/janis-rullis/dev/blob/master/Code-structures/Controllers/Accounts/UserController.php
 * https://github.com/janis-rullis/dev/blob/master/Code-structures/Models/Accounts/User.php
 * https://github.com/janis-rullis/dev/blob/master/Code-structures/tests/Accounts/GuestTest.php
 * https://github.com/janis-rullis/dev/blob/master/HTTP-statuses.md
 * 
 * Response always should have 2 fields - data and success (boolean) or errors (array).
 */
namespace App\Http\Controllers;

use \App\Models\Product;

class ProductController
{

	/**
	 * @param string $hash
	 * @return type
	 */
	public function show(string $hash)
	{
		$errors = [];
		$status = Product::isValidHash(Product::getFormattedStr($slug));
		if ($status !== true) {
			$errors[] = $status;
		}
		$status = Product::isValidType($type);
		if ($status !== true) {
			$errors[] = $status;
		}

		if (empty($errors)) {
			return response()->json(['data' => \App\Models\Product::findByHash($slug), 'success' => true], 200);
		} else {
			return response()->json(['errors' => $errors], 400);
		}
	}

	/**
	 * @param string $type
	 * @param string $slug
	 * @return type
	 */
	public function showBySlug(string $type, string $slug)
	{
		$errors = [];
		$status = Product::isValidStr(Product::getFormattedStr($slug));
		if ($status !== true) {
			$errors[] = $status;
		}
		$status = Product::isValidType($type);
		if ($status !== true) {
			$errors[] = $status;
		}

		if (empty($errors)) {
			return response()->json(['data' => \App\Models\Product::findBySlug($slug, $type), 'success' => true], 200);
		} else {
			return response()->json(['errors' => $errors], 400);
		}
	}

	/**
	 * Return a list of products.
	 */
	public function index()
	{
		return response()->json(['data' => \App\Models\Product::getList(), 'success' => true], 200);
	}
}
