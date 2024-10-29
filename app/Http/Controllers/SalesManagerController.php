<?php

namespace App\Http\Controllers;

use App\Http\Resources\SalesManagerListResource;
use App\Manager\ImageManager;
use App\Models\Address;
use App\Models\SalesManager;
use App\Http\Requests\StoreSalesManagerRequest;
use App\Http\Requests\UpdateSalesManagerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesManagerController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $sales_manager = (new SalesManager())->getSalesManagerList($request->all());
        return SalesManagerListResource::collection($sales_manager);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param StoreSalesManagerRequest $request
     * @return JsonResponse
     */
    public function store(StoreSalesManagerRequest $request): JsonResponse
    {
        $sales_manager = (new SalesManager())->prepareData($request->all(), auth());
        $address = (new Address())->prepareData($request->all());
        if ($request->has('photo')){
            $name = Str::slug($sales_manager['name'].'-'.now().'-photo');
            $sales_manager['photo'] = ImageManager::processImageUpload(
                $request->input('photo'),
                $name,
                path: SalesManager::PHOTO_UPLOAD_PATH,
                width: SalesManager::PHOTO_WIDTH,
                height: SalesManager::PHOTO_HEIGHT,
                path_thumb: SalesManager::THUMB_PHOTO_UPLOAD_PATH,
                width_thumb:SalesManager::PHOTO_THUMB_WIDTH,
                height_thumb: SalesManager::PHOTO_THUMB_HEIGHT,
            );
        }
        if ($request->has('nid_photo')){
            $name = Str::slug($sales_manager['name'].'-'.now().'-nid-photo');
            $sales_manager['nid_photo'] = ImageManager::processImageUpload(
                $request->input('nid_photo'),
                $name,
                path: SalesManager::PHOTO_UPLOAD_PATH,
                width: SalesManager::PHOTO_WIDTH,
                height: SalesManager::PHOTO_HEIGHT,
                path_thumb: SalesManager::THUMB_PHOTO_UPLOAD_PATH,
                width_thumb:SalesManager::PHOTO_THUMB_WIDTH,
                height_thumb: SalesManager::PHOTO_THUMB_HEIGHT,
            );
        }
        try {
            DB::beginTransaction();
            $sales_manager = SalesManager::create($sales_manager);
            $sales_manager->address()->create($address);
            DB::commit();
            return response()->json(['msg' => 'Supplier created successfully','cls' => 'success']);
        }catch (\Throwable $e){
            if (isset($sales_manager['photo'])){
                ImageManager::deletePhoto(SalesManager::PHOTO_UPLOAD_PATH, $sales_manager['photo']);
                ImageManager::deletePhoto(SalesManager::THUMB_PHOTO_UPLOAD_PATH, $sales_manager['photo']);
            }
            if (isset($sales_manager['nid_photo'])){
                ImageManager::deletePhoto(SalesManager::PHOTO_UPLOAD_PATH, $sales_manager['nid_photo']);
                ImageManager::deletePhoto(SalesManager::THUMB_PHOTO_UPLOAD_PATH, $sales_manager['nid_photo']);
            }
            info('SALES_MANAGER_STORE_FAILED', ['sales_manager'=>$sales_manager, 'address'=>$address, $e]);
            DB::rollBack();
            return response()->json(['msg' => 'Something is going wrong','cls' => 'warning', 'flag'=>'true']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesManager $salesManager)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesManager $salesManager)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalesManagerRequest $request, SalesManager $salesManager)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesManager $salesManager)
    {
        //
    }
}
