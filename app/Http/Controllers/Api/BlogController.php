<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Blog;

class BlogController extends Controller
{
    public function index(Request $request) {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $offset = ($page - 1) * $limit;
        $order = $request->query('order', 'desc');

        $blogs = Blog::with(['photo', 'category', 'creator'])
            ->orderBy('created_at', $order)
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => [
                'blogs' => $blogs,
                'request' => $request->all(),
            ],
        ]);
    }

    public function categories(Request $request) {
        $categories = Category::with(['blogs', 'blogs.photo', 'blogs.category', 'blogs.creator'])
            ->whereHas('blogs')
            ->where('type', 'blogs')
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => $categories,
        ]);
    }

    public function show(Request $request, $slug) {
        $blog = Blog::with(['photo', 'category', 'creator'])
            ->firstWhere('slug', $slug);
        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'not found',
                'data' => collect(),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => $blog,
        ]);
    }
}
