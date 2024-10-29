<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandEditResource;
use App\Http\Resources\BrandListResource;
use App\Manager\ImageManager;
use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $categories = (new Brand())->getAllBrands($request->all());
        return BrandListResource::collection($categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param StoreBrandRequest $request
     * @return JsonResponse
     */
    final public function store(StoreBrandRequest $request): JsonResponse
    {
        $brand = $request->except('logo');
        $brand['slug'] = Str::slug($request->input('slug'));
        $brand['user_id'] = auth()->id();
        if($request->has('logo')){
            $brand['logo'] = $this->processImageUpload($request->input('logo'),$brand['slug']);
        }
        (new Brand())->storeBrand($brand);
        return response()->json(['msg'=>'Brand created successfully', 'cls' => 'success']);
    }

    /**
     * @param Brand $brand
     * @return BrandEditResource
     */
    final public function show(Brand $brand): BrandEditResource
    {
        return new BrandEditResource($brand);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        //
    }

    /**
     * @param UpdateBrandRequest $request
     * @param Brand $brand
     * @return JsonResponse
     */
    final public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        $brand_data = $request->except('logo');
        $brand_data['slug'] = Str::slug($request->input('slug'));
        if($request->has('logo')){
            $brand_data['logo'] = $this->processImageUpload($request->input('logo'), $brand_data['slug'], $brand->logo );
        }
        $brand->update($brand_data);
        return response()->json(['msg'=>'Brand updated successfully', 'cls' => 'success']);
    }

    /**
     * @param Brand $brand
     * @return JsonResponse
     */
    public function destroy(Brand $brand): JsonResponse
    {
        if (!empty($brand->logo)){
            ImageManager::deletePhoto(Brand::BRAND_IMAGE_PATH, $brand->logo);
            ImageManager::deletePhoto(Brand::BRAND_THUMB_IMAGE_PATH, $brand->logo);
        }
        $brand->delete();
        return response()->json(['msg'=>'Brand successfully deleted!', 'cls' => 'warning']);
    }

    /**
     * @param string $file
     * @param string $name
     * @param string|null $existing_photo
     * @return string
     */
    private function processImageUpload(string $file, string $name, string|null $existing_photo = null): string
    {

        $width = 800;
        $height = 800;
        $width_thumb = 150;
        $height_thumb = 150;
        $path = Brand::BRAND_IMAGE_PATH;
        $path_thumb = Brand::BRAND_THUMB_IMAGE_PATH;

        if (!empty($existing_photo)){
            ImageManager::deletePhoto(Brand::BRAND_IMAGE_PATH, $existing_photo);
            ImageManager::deletePhoto(Brand::BRAND_THUMB_IMAGE_PATH, $existing_photo);
        }
        $photo_name = ImageManager::uploadImage($name, $width, $height, $path, $file);
        ImageManager::uploadImage($name, $width_thumb, $height_thumb, $path_thumb, $file);
        return $photo_name;
    }

    /**
     * @return JsonResponse
     */
    final public function getBrandsList(): JsonResponse
    {
        $brands = (new Brand())->getBrandNameAndId();
        return response()->json($brands);
    }

}
