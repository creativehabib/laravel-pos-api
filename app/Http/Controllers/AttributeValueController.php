<?php

namespace App\Http\Controllers;

use App\Models\AttributeValue;
use App\Http\Requests\StoreAttributeValueRequest;
use App\Http\Requests\UpdateAttributeValueRequest;
use Illuminate\Http\JsonResponse;

class AttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param StoreAttributeValueRequest $request
     * @return JsonResponse
     */
    final public function store(StoreAttributeValueRequest $request): JsonResponse
    {
        $value_data = $request->all();
        $value_data['user_id'] = auth()->id();
        AttributeValue::create($value_data);
        return response()->json(['msg'=>'Value Created successfully!', 'cls' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(AttributeValue $attributeValue)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttributeValue $attributeValue)
    {
        //
    }

    /**
     * @param UpdateAttributeValueRequest $request
     * @param AttributeValue $value
     * @return JsonResponse
     */
    public function update(UpdateAttributeValueRequest $request, AttributeValue $value): JsonResponse
    {
        $value_data = $request->all();
        $value->update($value_data);
        return response()->json(['msg'=>'Value update successfully!', 'cls' => 'success']);
    }

    /**
     * @param AttributeValue $value
     * @return JsonResponse
     */
    final public function destroy(AttributeValue $value): JsonResponse
    {
        $value->delete();
        return response()->json(['msg'=>'Value successfully deleted!', 'cls' => 'warning']);
    }
}
