<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryEditResource;
use App\Http\Resources\CategoryResource;
use App\Manager\ImageManager;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $categories = (new Category())->getAllCategories($request->all());
        return CategoryResource::collection($categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $request->except('photo');
        $category['slug'] = Str::slug($request->input('slug'));
        $category['user_id'] = auth()->id();
        if($request->has('photo')){
            $category['photo'] = $this->processImageUpload($request->input('photo'),$category['slug']);
        }
        (new Category())->storeCategory($category);
        return response()->json(['msg'=>'Category Created Successfully', 'cls' => 'success']);
    }

    /**
     * @param Category $category
     * @return CategoryEditResource
     */
    final public function show(Category $category): CategoryEditResource
    {
        return new CategoryEditResource($category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * @param UpdateCategoryRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    final public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category_data = $request->except('photo');
        $category_data['slug'] = Str::slug($request->input('slug'));
        if($request->has('photo')){
            $category_data['photo'] = $this->processImageUpload($request->input('photo'), $category_data['slug'], $category->photo );
        }
        $category->update($category_data);
        return response()->json(['msg'=>'Category Updated Successfully', 'cls' => 'success']);
    }

    /**
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        if (!empty($category->photo)){
            ImageManager::deletePhoto(Category::CATEGORY_IMAGE_PATH, $category->photo);
            ImageManager::deletePhoto(Category::CATEGORY_THUMB_IMAGE_PATH, $category->photo);
        }
        $category->delete();
        return response()->json(['msg'=>'Category Successfully Deleted!', 'cls' => 'warning']);
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
        $path = Category::CATEGORY_IMAGE_PATH;
        $path_thumb = Category::CATEGORY_THUMB_IMAGE_PATH;

        if (!empty($existing_photo)){
            ImageManager::deletePhoto(Category::CATEGORY_IMAGE_PATH, $existing_photo);
            ImageManager::deletePhoto(Category::CATEGORY_THUMB_IMAGE_PATH, $existing_photo);
        }
        $photo_name = ImageManager::uploadImage($name, $width, $height, $path, $file);
        ImageManager::uploadImage($name, $width_thumb, $height_thumb, $path_thumb, $file);
        return $photo_name;
    }

    /**
     * @return JsonResponse
     */
    final public function getCategoryList(): JsonResponse
    {
        $categories = (new Category())->getCategoryNameAndId();
        return response()->json($categories);
    }
}
