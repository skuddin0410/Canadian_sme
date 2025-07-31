<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Setting;

class HomeController extends Controller
{
  public function index() {
    $banners = Banner::with(['photo'])
      // ->whereNotNull('link')
      ->orderBy('order')
      ->orderByDesc('created_at')
      ->get();

    $settings = Setting::query()
      ->whereIn('key', [
        'giveaway_on_top',
        'quiz_on_top',
      ])
      ->get();

    $products = Setting::with(['photo'])
      ->whereIn('key', [
        'home_page_giveaways',
        'home_page_quizzes',
        'home_page_spinners',
      ])
      ->get();

    $favorites = Setting::with(['photo'])
      ->whereIn('key', [
        'home_page_link_1',
        'home_page_link_2',
        'home_page_link_3',
        'home_page_link_4',
      ])
      ->get();

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => compact('banners', 'settings', 'products', 'favorites'),
    ]);
  }
}
