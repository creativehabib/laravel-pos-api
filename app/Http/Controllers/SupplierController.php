<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupplierEditResource;
use App\Http\Resources\SupplierListResource;
use App\Manager\ImageManager;
use App\Models\Address;
use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    final public function index(Request $request): AnonymousResourceCollection
    {
        $suppliers = (new Supplier())->getSupplierList($request->all());
        return SupplierListResource::collection($suppliers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param StoreSupplierRequest $request
     * @return JsonResponse
     */
    final public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = (new Supplier())->prepareData($request->all(), auth());
        $address = (new Address())->prepareData($request->all());
        if ($request->has('logo')){
            $name = Str::slug($supplier['name'].'-'.now());
          $supplier['logo'] = ImageManager::processImageUpload(
                $request->input('logo'),
                $name,
                path: Supplier::IMAGE_UPLOAD_PATH,
                width: Supplier::LOGO_WIDTH,
                height: Supplier::LOGO_HEIGHT,
                path_thumb: Supplier::THUMB_IMAGE_UPLOAD_PATH,
                width_thumb:Supplier::LOGO_THUMB_WIDTH,
                height_thumb: Supplier::LOGO_THUMB_HEIGHT,
            );
        }
        try {
            DB::beginTransaction();
            $supplier = Supplier::create($supplier);
            $supplier->address()->create($address);
            DB::commit();
            return response()->json(['msg' => 'Supplier created successfully','cls' => 'success']);
        }catch (\Throwable $e){
            if (isset($supplier['logo'])){
                ImageManager::deletePhoto(Supplier::IMAGE_UPLOAD_PATH, $supplier['logo']);
                ImageManager::deletePhoto(Supplier::THUMB_IMAGE_UPLOAD_PATH, $supplier['logo']);
            }
            info('SUPPLIER_STORE_FAILED', ['supplier'=>$supplier, 'address'=>$address, $e]);
            DB::rollBack();
            return response()->json(['msg' => 'Something is going wrong','cls' => 'warning', 'flag'=>'true']);
        }



    }

    /**
     * @param Supplier $supplier
     * @return SupplierEditResource
     */
    final public function show(Supplier $supplier): SupplierEditResource
    {
        $supplier->load('address');
        return new SupplierEditResource($supplier);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * @param UpdateSupplierRequest $request
     * @param Supplier $supplier
     * @return JsonResponse
     */
    final public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier_data = (new Supplier())->prepareData($request->all(), auth());
        $address_data = (new Address())->prepareData($request->all());
        if ($request->has('logo')){
            $name = Str::slug($supplier_data['name'].'-'.now());
            $supplier_data['logo'] = ImageManager::processImageUpload(
                $request->input('logo'),
                $name,
                path: Supplier::IMAGE_UPLOAD_PATH,
                width: Supplier::LOGO_WIDTH,
                height: Supplier::LOGO_HEIGHT,
                path_thumb: Supplier::THUMB_IMAGE_UPLOAD_PATH,
                width_thumb:Supplier::LOGO_THUMB_WIDTH,
                height_thumb: Supplier::LOGO_THUMB_HEIGHT,
                existing_photo: $supplier->logo
            );
        }
        try {
            DB::beginTransaction();
            $supplier_data = $supplier->update($supplier_data);
            $supplier->address()->update($address_data);
            DB::commit();
            return response()->json(['msg' => 'Supplier updated successfully','cls' => 'success']);
        }catch (\Throwable $e){
            info('SUPPLIER_STORE_FAILED', ['supplier'=>$supplier_data, 'address'=>$address_data, $e]);
            DB::rollBack();
            return response()->json(['msg' => 'Something is going wrong','cls' => 'warning', 'flag'=>'true']);
        }
    }

    /**
     * @param Supplier $supplier
     * @return JsonResponse
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        if (!empty($supplier->logo)){
            ImageManager::deletePhoto(Supplier::IMAGE_UPLOAD_PATH, $supplier['logo']);
            ImageManager::deletePhoto(Supplier::THUMB_IMAGE_UPLOAD_PATH, $supplier['logo']);
        }
        (new Address())->deleteAddressBySupplierId($supplier);
        $supplier->delete();
        return response()->json(['msg' => 'Supplier deleted successfully', 'cls'=>'warning']);
    }

    /**
     * @return JsonResponse
     */

    public function getSupplierList(): JsonResponse
    {
        $suppliers = (new Supplier())->getSupplierSelectList();
        return response()->json($suppliers);
    }

}
