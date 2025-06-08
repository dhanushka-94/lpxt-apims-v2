<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    /**
     * Get all categories
     */
    public function index()
    {
        $categories = $this->getCategories();
        
        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ]);
    }

    /**
     * Get a specific category by ID
     */
    public function show($id)
    {
        $category = Category::with('subcategories')->find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        // Format the response to match the expected structure
        $formattedCategory = [
            'id' => $category->id,
            'code' => $category->code,
            'name' => $category->name,
            'parent_id' => $category->parent_id,
            'slug' => $category->slug,
            'image' => $category->image,
            'description' => $category->description,
            'subcategories' => $category->subcategories->map(function($subcategory) {
                return [
                    'id' => $subcategory->id,
                    'code' => $subcategory->code,
                    'name' => $subcategory->name,
                    'parent_id' => $subcategory->parent_id,
                    'slug' => $subcategory->slug,
                    'image' => $subcategory->image,
                    'description' => $subcategory->description,
                ];
            })->toArray()
        ];

        return response()->json([
            'success' => true,
            'data' => $formattedCategory,
            'message' => 'Category retrieved successfully'
        ]);
    }

    /**
     * Get products in a specific category
     */
    public function products($id, Request $request)
    {
        $perPage = $request->input('per_page', 15);
        
        $products = Product::with(['category', 'subcategory', 'brand', 'photos', 'status', 'attributes.attribute'])
            ->where('category_id', $id)
            ->paginate($perPage);
        
        // Use the ProductController to format the products
        $productController = new ProductController();
        $formattedProducts = $productController->formatProducts($products->items());
        
        return response()->json([
            'success' => true,
            'data' => [
                'current_page' => $products->currentPage(),
                'data' => $formattedProducts,
                'from' => $products->firstItem(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'to' => $products->lastItem(),
                'total' => $products->total()
            ],
            'message' => 'Products in category retrieved successfully'
        ]);
    }
    
    /**
     * Get all categories with their subcategories
     */
    private function getCategories()
    {
        // Get main categories (parent_id is null)
        $mainCategories = Category::whereNull('parent_id')->get();
        
        $formattedCategories = $mainCategories->map(function($category) {
            // Get subcategories for this main category
            $subcategories = Category::where('parent_id', $category->id)->get();
            
            return [
                'id' => $category->id,
                'code' => $category->code,
                'name' => $category->name,
                'parent_id' => $category->parent_id,
                'slug' => $category->slug,
                'image' => $category->image,
                'description' => $category->description,
                'subcategories' => $subcategories->map(function($subcategory) {
                    return [
                        'id' => $subcategory->id,
                        'code' => $subcategory->code,
                        'name' => $subcategory->name,
                        'parent_id' => $subcategory->parent_id,
                        'slug' => $subcategory->slug,
                        'image' => $subcategory->image,
                        'description' => $subcategory->description,
                    ];
                })->toArray()
            ];
        })->toArray();
        
        return $formattedCategories;
    }
} 