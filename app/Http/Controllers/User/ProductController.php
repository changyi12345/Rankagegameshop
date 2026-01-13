<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\G2BulkService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $g2bulkService;

    public function __construct()
    {
        $this->g2bulkService = new G2BulkService();
    }

    /**
     * Display products page
     */
    public function index(Request $request)
    {
        $categoryId = $request->get('category_id');
        $gameFilter = $request->get('game'); // 'mlbb' or 'pubg'
        
        $categoriesResult = $this->g2bulkService->getCategories();
        $categories = $categoriesResult['categories'] ?? [];
        
        $productsResult = $this->g2bulkService->getProducts($categoryId);
        $products = $productsResult['products'] ?? [];

        // Filter products by game if specified
        if ($gameFilter) {
            $products = array_filter($products, function($product) use ($gameFilter) {
                $title = strtolower($product['title'] ?? '');
                $category = strtolower($product['category_title'] ?? '');
                
                if ($gameFilter === 'mlbb') {
                    return stripos($title, 'diamond') !== false 
                        || stripos($category, 'mobile legends') !== false
                        || stripos($category, 'mlbb') !== false;
                } elseif ($gameFilter === 'pubg') {
                    return stripos($title, 'uc') !== false 
                        || stripos($category, 'pubg') !== false;
                }
                return true;
            });
            $products = array_values($products); // Re-index array
        }

        // Get exchange rate (default: 1 USD = 2100 Ks)
        $exchangeRate = \App\Models\Setting::get('usd_to_kyat_rate', 2100);

        return view('user.products.index', compact('categories', 'products', 'categoryId', 'gameFilter', 'exchangeRate'));
    }

    /**
     * Show product details
     */
    public function show($id)
    {
        $result = $this->g2bulkService->getProduct($id);
        
        if (!$result['success']) {
            return redirect()->route('products.index')
                ->with('error', $result['message'] ?? 'Product not found');
        }

        $product = $result['product'];
        
        // Detect game type from product
        $title = strtolower($product['title'] ?? '');
        $category = strtolower($product['category_title'] ?? '');
        
        $isMLBB = stripos($title, 'diamond') !== false 
                || stripos($category, 'mobile legends') !== false
                || stripos($category, 'mlbb') !== false;
        
        $isPUBG = stripos($title, 'uc') !== false 
                || stripos($category, 'pubg') !== false;
        
        // Get exchange rate (default: 1 USD = 2100 Ks)
        $exchangeRate = \App\Models\Setting::get('usd_to_kyat_rate', 2100);

        return view('user.products.show', compact('product', 'isMLBB', 'isPUBG', 'exchangeRate'));
    }

    /**
     * Purchase a product
     */
    public function purchase(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $result = $this->g2bulkService->purchaseProduct($id, $request->quantity);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Product purchased successfully',
                'order_id' => $result['order_id'],
                'delivery_items' => $result['delivery_items'] ?? [],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Purchase failed',
        ], 400);
    }
}
