<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubCategoryEditResource;
use App\Http\Resources\SubCategoryListResource;
use App\Manager\ImageManager;
use App\Models\SubCategory;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    final public function index(Request $request): AnonymousResourceCollection
    {
        $categories = (new SubCategory())->getAllSubCategories($request->all());
        return SubCategoryListResource::collection($categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param StoreSubCategoryRequest $request
     * @return JsonResponse
     */
    final public function store(StoreSubCategoryRequest $request):JsonResponse
    {
        $sub_category = $request->except('photo');
        $sub_category['slug'] = Str::slug($request->input('slug'));
        $sub_category['user_id'] = auth()->id();
        if($request->has('photo')){
            $sub_category['photo'] = $this->processImageUpload($request->input('photo'),$sub_category['slug']);
        }
        (new SubCategory())->storeSubCategory($sub_category);
        return response()->json(['msg'=>'Sub Category Created Successfully', 'cls' => 'success']);
    }

    /**
     * @param SubCategory $subCategory
     * @return SubCategoryEditResource
     */
    public function show(SubCategory $subCategory): SubCategoryEditResource
    {
        return new SubCategoryEditResource($subCategory);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubCategory $subCategory)
    {
        //
    }

    /**
     * @param UpdateSubCategoryRequest $request
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    final public function update(UpdateSubCategoryRequest $request, SubCategory $subCategory): JsonResponse
    {
        $category_data = $request->except('photo');
        $category_data['slug'] = Str::slug($request->input('slug'));
        if($request->has('photo')){
            $category_data['photo'] = $this->processImageUpload($request->input('photo'), $category_data['slug'], $subCategory->photo );
        }
        $subCategory->update($category_data);
        return response()->json(['msg'=>'Sub category Updated Successfully', 'cls' => 'success']);
    }

    /**
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    final public function destroy(SubCategory $subCategory): JsonResponse
    {
        if (!empty($subCategory->photo)){
            ImageManager::deletePhoto(SubCategory::CATEGORY_IMAGE_PATH, $subCategory->photo);
            ImageManager::deletePhoto(SubCategory::CATEGORY_THUMB_IMAGE_PATH, $subCategory->photo);
        }
        $subCategory->delete();
        return response()->json(['msg'=>'Sub Category Successfully Deleted!', 'cls' => 'warning']);
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
        $path = SubCategory::CATEGORY_IMAGE_PATH;
        $path_thumb = SubCategory::CATEGORY_THUMB_IMAGE_PATH;

        if (!empty($existing_photo)){
            ImageManager::deletePhoto(SubCategory::CATEGORY_IMAGE_PATH, $existing_photo);
            ImageManager::deletePhoto(SubCategory::CATEGORY_THUMB_IMAGE_PATH, $existing_photo);
        }
        $photo_name = ImageManager::uploadImage($name, $width, $height, $path, $file);
        ImageManager::uploadImage($name, $width_thumb, $height_thumb, $path_thumb, $file);
        return $photo_name;
    }

    /**
     * @return JsonResponse
     */
    final public function getSubCategoryList($category_id): JsonResponse
    {
        $categories = (new SubCategory())->getCategoryNameAndId($category_id);
        return response()->json($categories);
    }
}
