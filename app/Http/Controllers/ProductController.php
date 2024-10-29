<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductListForBarCodeResource;
use App\Http\Resources\ProductListResource;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\ProductAttribute;
use App\Models\ProductSpecification;
use App\Models\Shop;
use App\Models\SubCategory;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    final public function index(Request $request): AnonymousResourceCollection
    {
        $products = (new Product())->getProductList($request->all());
        return ProductListResource::collection($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param StoreProductRequest $request
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $product = (new Product())->storeProduct($request->all(), auth()->id());

            if ($request->has('attributes')){
                (new ProductAttribute())->storeAttributeData($request->input('attributes'), $product);
            }
            if ($request->has('specifications')){
                (new ProductSpecification())->storeProductSpecification($request->input('specifications'),$product);
            }

            DB::commit();
            return response()->json(['msg' => 'Product Saved Successfully','cls' => 'success','product_id'=>$product->id]);

        }catch (\Throwable $e){
            info('PRODUCT_SAVE_FAILED', ['data' => $request->all(), 'error' => $e->getMessage()]);
            DB::rollBack();
            return response()->json(['msg' => $e->getMessage(), 'cls' => 'warning']);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load([
            'category:id,name',
            'sub_category:id,name',
            'photos:id,photo,product_id',
            'brand:id,name',
            'country:id,name',
            'supplier:id,name,phone',
            'created_by:id,name',
            'updated_by:id,name',
            'primary_photo',
            'product_attributes',
            'product_attributes.attributes',
            'product_attributes.attribute_value']);
        return new ProductDetailResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }

    public function getProductListForBarCode(Request $request)
    {
        $products = (new Product())->getProductForBarCode($request->all());
        return ProductListForBarCodeResource::collection($products);
    }

    /**
     * @return JsonResponse
     */
    public function get_product_columns(): JsonResponse
    {
        $columns = Schema::getColumnListing('products');
        $formatted_column = [];
        foreach ($columns as $column) {
            $formatted_column[] = ['id' => $column, 'name' => ucfirst(str_replace('_',' ', $column))];
        }
        return response()->json($formatted_column);
    }

    /**
     * @return JsonResponse
     */
    final public function get_add_product_data(): JsonResponse
    {
        return response()->json([
            'categories' => (new Category())->getCategoryNameAndId(),
            'brands' => (new Brand())->getBrandNameAndId(),
            'countries' => (new Country())->getCountryNameAndId(),
            'suppliers' => (new Supplier())->getSupplierSelectList(),
            'attributes' => (new Attribute())->getAttributeListWithValue(),
            'sub_categories' => (new SubCategory())->getSubCategoryIdAndName(),
            'shops' => (new Shop())->getShopIdAndName(),
        ]);
    }
}
