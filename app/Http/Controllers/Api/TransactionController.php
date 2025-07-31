<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Coupon;
use App\Models\Wallet;
use App\Models\Payment;
use App\Models\Withdrawal;
use App\Models\Transaction;

class TransactionController extends Controller
{
  public function index() {
    $summary = Transaction::summary()
      ->firstWhere('user_id', auth()->guard('api')->id());

    $transanctions = Transaction::query()
      ->when(Transaction::where('table_type', 'wallets')->exists(), function ($query) {
        $query->with(['wallet']);
      })
      ->when(Transaction::where('table_type', 'payments')->exists(), function ($query) {
        $query->with(['payment']);
      })
      ->when(Transaction::where('table_type', 'withdrawals')->exists(), function ($query) {
        $query->with(['withdrawal']);
      })
      ->where('user_id', auth()->guard('api')->id())
      ->latest()
      ->get();

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => compact('summary', 'transanctions'),
    ]);
  }

  public function store(Request $request) {
    $validator = Validator::make($request->all(), [
      'purpose' => 'required|string|in:redeem,deposit',
      'coupon' => 'required_if:purpose,redeem|nullable|string|max:255',
      'amount' => 'required_unless:purpose,redeem|nullable|numeric|decimal:2|max:99999',
      'reference' => 'required_if:purpose,deposit|nullable|string|max:255',
      'response' => 'required_if:purpose,deposit|nullable',
    ]);
    $validator->after(function ($validator) use ($request) {
      $totalAmountToday = Transaction::where('user_id', auth()->guard('api')->id())
        ->where('table_type', 'payments')
        ->where('purpose', 'deposit')
        ->where('journal_type', 'credit')
        ->whereDate('created_at', today())
        ->sum('amount');
      if ($request->purpose == 'deposit' && ($totalAmountToday + $request->amount) > 99999) {
        $validator->errors()->add('amount', 'The total amount for the day exceeds 99999.');
      }
    });
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->errors(),
        'data' => $request->all(),
      ], 422);
    }

    if ($request->purpose == 'redeem') {
      $coupon = Coupon::where('name', $request->coupon)
        ->whereDate('expires_at', '>=', now())
        ->first();
      if (!$coupon) {
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

      $wallet = Wallet::where('user_id', auth()->guard('api')->id())
        ->where('table_id', $coupon->id)
        ->where('table_type', 'coupons')
        ->first();
      if ($wallet) {
        return response()->json([
          'success' => false,
          'message' => [
            'coupon' => [
              'The coupon has already been redeemed.'
            ],
          ],
          'data' => $request->all(),
        ], 422);
      }

      $wallet = new Wallet();
      $wallet->user_id = auth()->guard('api')->id();
      $wallet->table_id = $coupon->id;
      $wallet->table_type = 'coupons';
      $wallet->amount = $coupon->price;
      $wallet->status = 'success';
      $wallet->save();

      $request->merge(['table_id' => $wallet->id]);
      $request->merge(['table_type' => 'wallets']);
      $request->merge(['amount' => $wallet->amount]);
    } else if ($request->purpose == 'deposit') {
      $payment = new Payment();
      $payment->user_id = auth()->guard('api')->id();
      $payment->reference = $request->reference;
      $payment->response = $request->response;
      $payment->amount = $request->amount;
      $payment->status = 'success';
      $payment->save();

      $request->merge(['table_id' => $payment->id]);
      $request->merge(['table_type' => 'payments']);

      // Referral
      $wallet = Wallet::where('user_id', auth()->guard('api')->id())
        ->where('table_type', 'users')
        ->where('status', 'init')
        ->first();
      if ($wallet) {
        // Referee User
        $wallet->amount = round($payment->amount * ($wallet->amount / 100), 2);
        $wallet->status = 'success';
        $wallet->save();

        $transanction = new Transaction();
        $transanction->user_id = auth()->guard('api')->id();
        $transanction->table_id = $wallet->id;
        $transanction->table_type = 'wallets';
        $transanction->purpose = 'referral';
        $transanction->journal_type = 'credit';
        $transanction->amount = $wallet->amount;
        $transanction->save();

        // Referrer User
        $referrerWallet = new Wallet();
        $referrerWallet->user_id = $wallet->table_id;
        $referrerWallet->table_id = auth()->guard('api')->id();
        $referrerWallet->table_type = 'users';
        $referrerWallet->amount = $wallet->amount;
        $referrerWallet->status = 'success';
        $referrerWallet->save();

        $transanction = new Transaction();
        $transanction->user_id = $wallet->table_id;
        $transanction->table_id = $referrerWallet->id;
        $transanction->table_type = 'wallets';
        $transanction->purpose = 'referral';
        $transanction->journal_type = 'credit';
        $transanction->amount = $wallet->amount;
        $transanction->save();
      }
    }

    $transanction = new Transaction();
    $transanction->user_id = auth()->guard('api')->id();
    $transanction->table_id = $request->table_id;
    $transanction->table_type = $request->table_type;
    $transanction->purpose = $request->purpose;
    $transanction->journal_type = 'credit';
    $transanction->amount = $request->amount;
    $transanction->save();

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => $transanction,
    ]);
  }

  public function show(Request $request) {
    $validator = Validator::make($request->all(), [
      'coupon' => 'required|string|max:255',
    ]);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->errors(),
        'data' => $request->all(),
      ], 422);
    }

    $coupon = Coupon::where('name', $request->coupon)
      ->whereDate('expires_at', '>=', now())
      ->first();
    if (!$coupon) {
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

    $wallet = Wallet::where('user_id', auth()->guard('api')->id())
      ->where('table_id', $coupon->id)
      ->where('table_type', 'coupons')
      ->where('status', 'success')
      ->first();
    if ($wallet) {
      return response()->json([
        'success' => false,
        'message' => [
          'coupon' => [
            'The coupon has already been redeemed.'
          ],
        ],
        'data' => $request->all(),
      ], 422);
    }

    $wallet = Wallet::firstOrNew([
      'user_id' => auth()->guard('api')->id(),
      'table_id' => $coupon->id,
      'table_type' => 'coupons',
    ]);
    $wallet->amount = $coupon->type == 'percent' ? $coupon->price * $coupon->percent / 100 : $coupon->price;
    $wallet->status = 'init';
    $wallet->save();

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => $wallet,
    ]);
  }

  public function update(Request $request) {
    $validator = Validator::make($request->all(), [
      'amount' => 'required|numeric|decimal:2',
    ]);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->errors(),
        'data' => $request->all(),
      ], 422);
    }

    $transanction = new Withdrawal();
    $transanction->user_id = auth()->guard('api')->id();
    $transanction->amount = $request->amount;
    $transanction->status = 'init';
    $transanction->save();

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => $transanction,
    ]);
  }
}
