<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Cart;

class CartController extends Controller
{
  public function index(Request $request) {
    $carts = Cart::query()
      ->when(Cart::where('table_type', 'giveaways')->exists(), function ($query) {
        $query->with(['giveaway', 'giveaway.photo']);
      })
      ->when(Cart::where('table_type', 'quizzes')->exists(), function ($query) {
        $query->with(['quiz', 'quiz.photo']);
      })
      ->when(Cart::where('table_type', 'spinners')->exists(), function ($query) {
        $query->with(['spinner']);
      })
      ->where('user_id', auth()->guard('api')->id())
      ->get();

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => $carts,
    ]);
  }

  public function store(Request $request) {
    $validator = Validator::make($request->all(), [
      'table_id' => 'required|integer|exists:' . $request->table_type . ',id',
      'table_type' => 'required|string|in:giveaways,quizzes,spinners',
      'quantity' => 'required|integer|min:1',
      'amount' => 'required|numeric|decimal:2',
    ]);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->errors(),
        'data' => $request->all(),
      ], 422);
    }

    $cart = Cart::firstOrNew([
      'user_id' => auth()->guard('api')->id(),
    ]);
    $cart->table_id = $request->table_id;
    $cart->table_type = $request->table_type;
    $cart->quantity = $request->quantity;
    $cart->amount = $request->amount;
    $cart->save();

    $carts = Cart::query()
      ->when(Cart::where('table_type', 'giveaways')->exists(), function ($query) {
        $query->with(['giveaway', 'giveaway.photo']);
      })
      ->when(Cart::where('table_type', 'quizzes')->exists(), function ($query) {
        $query->with(['quiz', 'quiz.photo']);
      })
      ->when(Cart::where('table_type', 'spinners')->exists(), function ($query) {
        $query->with(['spinner']);
      })
      ->where('user_id', auth()->guard('api')->id())
      ->get();

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => $carts,
    ]);
  }
}
