<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PublicProductServiceController extends Controller
{
    
     public function products(Request $request)
    {
        $query = Product::active()->with(['category', 'pricingTiers' => function($q) {
            $q->active()->orderBy('sort_order');
        }]);

        if ($request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $products = $query->orderBy('sort_order')->orderBy('name')->get();
        $categories = ProductCategory::active()->parents()->orderBy('name')->get();

        return view('company.public.products.index', compact('products', 'categories'));
    }

    public function productDetail($slug)
    {
        $product = Product::active()
            ->with(['category', 'technicalSpecs', 'pricingTiers' => function($q) {
                $q->active()->orderBy('sort_order');
            }])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('company.public.products.show', compact('product', 'relatedProducts'));
    }

    public function services(Request $request)
    {
        $query = Service::active()->with(['category', 'pricingTiers' => function($q) {
            $q->active()->orderBy('sort_order');
        }]);

        if ($request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $services = $query->orderBy('sort_order')->orderBy('name')->get();
        $categories = ServiceCategory::active()->parents()->orderBy('name')->get();

        return view('company.public.services.index', compact('services', 'categories'));
    }

    public function serviceDetail($slug)
    {
        $service = Service::active()
            ->with(['category', 'pricingTiers' => function($q) {
                $q->active()->orderBy('sort_order');
            }])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedServices = Service::active()
            ->where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->limit(4)
            ->get();

        return view('company.public.services.show', compact('service', 'relatedServices'));
    }
}
