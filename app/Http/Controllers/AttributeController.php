<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttributeListResource;
use App\Models\Attribute;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttributeController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    final public function index(): AnonymousResourceCollection
    {
        $attributes = (new Attribute())->getAttributeList();
        return AttributeListResource::collection($attributes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param StoreAttributeRequest $request
     * @return JsonResponse
     */
    final public function store(StoreAttributeRequest $request): JsonResponse
    {
        $attribute_data = $request->all();
        $attribute_data['user_id'] = auth()->id();
        Attribute::create($attribute_data);
        return response()->json(['msg'=>'Attribute Created successfully!', 'cls' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        //
    }

    /**
     * @param UpdateAttributeRequest $request
     * @param Attribute $attribute
     * @return JsonResponse
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute): JsonResponse
    {
        $attribute_data = $request->all();
        $attribute->update($attribute_data);
        return response()->json(['msg'=>'Attribute update successfully!', 'cls' => 'success']);
    }

    /**
     * @param Attribute $attribute
     * @return JsonResponse
     */
    public function destroy(Attribute $attribute): JsonResponse
    {
        $attribute->delete();
        return response()->json(['msg'=>'Attribute successfully deleted!', 'cls' => 'warning']);
    }

    /**
     * @return JsonResponse
     */
    final public function getAttributeList(): JsonResponse
    {
        $attributes = (new Attribute())->getAttributeListWithValue();
        return response()->json($attributes);
    }
}
