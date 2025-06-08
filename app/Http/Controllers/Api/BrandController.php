<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{
    /**
     * Get all brands
     */
    public function index()
    {
        $brands = Brand::all();
        
        $formattedBrands = $brands->map(function($brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug ?? strtolower(str_replace(' ', '-', $brand->name)),
                'description' => $brand->description ?? null
            ];
        })->toArray();
        
        return response()->json([
            'success' => true,
            'data' => $formattedBrands,
            'message' => 'Brands retrieved successfully'
        ]);
    }

    /**
     * Get a specific brand by ID
     */
    public function show($id)
    {
        $brand = Brand::find($id);
        
        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found',
            ], 404);
        }

        $formattedBrand = [
            'id' => $brand->id,
            'name' => $brand->name,
            'slug' => $brand->slug ?? strtolower(str_replace(' ', '-', $brand->name)),
            'description' => $brand->description ?? null
        ];

        return response()->json([
            'success' => true,
            'data' => $formattedBrand,
            'message' => 'Brand retrieved successfully'
        ]);
    }

    /**
     * Get products for a specific brand
     */
    public function products($id, Request $request)
    {
        // Redirect to ProductController's byBrand method
        $productController = new ProductController();
        return $productController->byBrand($id, $request);
    }
    

} 