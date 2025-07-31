<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Giveaway;
use App\Models\Order;
use App\Models\Cart;

class OrderController extends Controller
{
  public function index() {
    $orders = Order::query()
      ->when(Order::where('table_type', 'giveaways')->exists(), function ($query) {
        $query->with(['giveaway', 'giveaway.photo']);
      })
      ->when(Order::where('table_type', 'quizzes')->exists(), function ($query) {
        $query->with(['quiz', 'quiz.photo']);
      })
      ->when(Order::where('table_type', 'spinners')->exists(), function ($query) {
        $query->with(['spinner']);
      })
      ->where('user_id', auth()->guard('api')->id())
      ->latest()
      ->get();

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => $orders,
    ]);
  }

  public function store(Request $request) {
    $validator = Validator::make($request->all(), [
      'table_id' => 'required_unless:table_type,spinners|nullable|integer|exists:' . $request->table_type . ',id',
      'table_type' => 'required|string|in:giveaways,quizzes,spinners',
      'amount' => 'required|numeric|decimal:2',
      'quantity' => 'required|numeric|min:1',
      'wallet_id' => 'sometimes|nullable|string|max:255',
    ]);
    $validator->after(function ($validator) use ($request) {
      $summary = Transaction::summary()
        ->firstWhere('user_id', auth()->guard('api')->id());
      if ($request->amount > 0.00 && ($summary->balance ?? 0.00) < $request->amount) {
        $validator->errors()->add('amount', 'Insufficient wallet balance.');
      }

      if ($request->table_type == 'giveaways') {
        if ($request->amount == 0.00 && $request->quantity > 1) {
          $validator->errors()->add('quantity', 'For free giveaways, the quantity must be 1.');
        }

        $giveaway = Giveaway::find($request->table_id);
        if ($giveaway) {
          $orders = Order::where('user_id', auth()->id())
            ->where('table_id', $request->table_id)
            ->where('table_type', 'giveaways')
            ->count();
          if ($orders + $request->quantity > $giveaway->tickets) {
            $validator->errors()->add('quantity', 'The quantity exceeds the maximum limit.');
          }
        } else {
          $validator->errors()->add('table_id', 'The specified giveaway does not exist.');
        }
      } elseif (in_array($request->table_type, ['quizzes', 'spinners'])) {
        if ($request->quantity > 1) {
          $validator->errors()->add('quantity', 'For quizzes and spinners, the quantity must be 1.');
        }
      }
    });
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->errors(),
        'data' => $request->all(),
      ], 422);
    }

    if ($request->wallet_id) {
      $wallet = Wallet::find($request->wallet_id);
      if (!$wallet) {
        return response()->json([
          'success' => false,
          'message' => [
            'coupon' => [
              'The coupon is invalid or expired.'
            ],
          ],
          'data' => $request->all(),
        ], 422);
      }
      $wallet->status = 'success';
      $wallet->save();

      $transanction = new Transaction();
      $transanction->user_id = auth()->guard('api')->id();
      $transanction->table_id = $wallet->id;
      $transanction->table_type = 'wallets';
      $transanction->purpose = 'redeem';
      $transanction->journal_type = 'credit';
      $transanction->amount = $wallet->amount;
      $transanction->save();

      $transanction = new Transaction();
      $transanction->user_id = auth()->guard('api')->id();
      $transanction->table_id = $wallet->id;
      $transanction->table_type = 'wallets';
      $transanction->purpose = 'pay';
      $transanction->journal_type = 'debit';
      $transanction->amount = $wallet->amount;
      $transanction->save();
    }

    $wallet = new Wallet();
    $wallet->user_id = auth()->guard('api')->id();
    $wallet->table_id = $request->table_id;
    $wallet->table_type = $request->table_type;
    $wallet->amount = $request->table_type == 'spinners' ? 0.00 : $request->amount;
    $wallet->status = 'success';
    $wallet->save();

    $transanction = new Transaction();
    $transanction->user_id = auth()->guard('api')->id();
    $transanction->table_id = $wallet->id;
    $transanction->table_type = 'wallets';
    $transanction->purpose = 'pay';
    $transanction->journal_type = 'debit';
    $transanction->amount = $request->table_type == 'spinners' ? 0.00 : $request->amount;
    $transanction->save();

    if ($request->quantity > 0) {
      for ($i = 0; $i < $request->quantity; $i++) {
        $order = new Order();
        $order->user_id = auth()->guard('api')->id();
        $order->table_id = $request->table_id;
        $order->table_type = $request->table_type;
        $order->amount = round($request->amount / $request->quantity, 2);
        $order->save();
      }
    }

    Cart::where('user_id', auth()->guard('api')->id())
      ->delete();

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => $order,
    ]);
  }

  public function show(Request $request) {
    $validator = Validator::make($request->all(), [
      'table_id' => 'required_unless:table_type,spinners|nullable|integer|exists:' . $request->table_type . ',id',
      'table_type' => 'required|string|in:giveaways,quizzes,spinners',
    ]);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->errors(),
        'data' => $request->all(),
      ], 422);
    }

    $order = Order::query()
      ->when($request->table_type == 'giveaways', function ($query) {
        $query->with(['giveaway', 'giveaway.photo']);
      })
      ->when($request->table_type == 'quizzes', function ($query) {
        $query->with(['quiz', 'quiz.photo']);
      })
      ->when($request->table_type == 'spinners', function ($query) {
        $query->with(['spinner'])
          ->whereDate('created_at', today());
      }, function ($query) use ($request) {
        $query->where('table_id', $request->table_id);
      })
      ->where('table_type', $request->table_type)
      ->where('user_id', auth()->guard('api')->id())
      ->first();
    if (!$order) {
      return response()->json([
        'success' => false,
        'message' => 'not found',
        'data' => $order,
      ], 404);
    }

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => $order,
    ]);
  }

  public function update(Request $request) {
    $validator = Validator::make($request->all(), [
      'table_id' => 'required|integer|exists:' . $request->table_type . ',id',
      'table_type' => 'required|string|in:giveaways,quizzes,spinners',
      'winning_type' => 'required|string|in:none,amount,coupon,prize,link',
      'winning' => 'required|string|max:255',
    ]);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->errors(),
        'data' => $request->all(),
      ], 422);
    }

    if ($request->table_type === 'spinners') {
      $order = new Order();
      $order->user_id = auth()->guard('api')->id();
      $order->table_id = $request->table_id;
      $order->table_type = $request->table_type;
      $order->amount = 0.00;
    } else {
      $order = Order::where('user_id', auth()->guard('api')->id())
        ->where('table_id', $request->table_id)
        ->where('table_type', $request->table_type)
        ->first();
      if (!$order) {
        return response()->json([
          'success' => false,
          'message' => 'not found',
          'data' => $order,
        ], 404);
      }
    }

    if ($request->winning_type == 'amount') {
      $wallet = new Wallet();
      $wallet->user_id = auth()->guard('api')->id();
      $wallet->table_id = $request->table_id ?? $order->table_id;
      $wallet->table_type = $request->table_type ?? $order->table_type;
      $wallet->amount = $request->winning;
      $wallet->status = 'success';
      $wallet->save();

      $transanction = new Transaction();
      $transanction->user_id = auth()->guard('api')->id();
      $transanction->table_id = $wallet->id;
      $transanction->table_type = 'wallets';
      $transanction->purpose = $request->purpose;
      $transanction->journal_type = 'credit';
      $transanction->amount = $request->amount;
      $transanction->save();
    }

    $order->winning_type = $request->winning_type;
    $order->winning = $request->winning;
    $order->save();

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => $order,
    ]);
  }
}
