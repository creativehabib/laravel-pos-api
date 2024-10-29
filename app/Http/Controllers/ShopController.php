<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateShopRequest;
use App\Http\Resources\ShopEditResource;
use App\Http\Resources\ShopListResource;
use App\Manager\ImageManager;
use App\Models\Address;
use App\Models\Shop;
use App\Http\Requests\StoreShopRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;;

class ShopController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    final public function index(Request $request): AnonymousResourceCollection
    {
        $shops = (new Shop())->getShopList($request->all());
        return ShopListResource::collection($shops);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param StoreShopRequest $request
     * @return JsonResponse
     */
    final public function store(StoreShopRequest $request): JsonResponse
    {
        $shop = (new Shop())->prepareData($request->all(), auth());
        $address = (new Address())->prepareData($request->all());
        if ($request->has('logo')){
            $name = Str::slug($shop['name'].'-'.now());
            $shop['logo'] = ImageManager::processImageUpload(
                $request->input('logo'),
                $name,
                path: Shop::IMAGE_UPLOAD_PATH,
                width: Shop::LOGO_WIDTH,
                height: Shop::LOGO_HEIGHT,
                path_thumb: Shop::THUMB_IMAGE_UPLOAD_PATH,
                width_thumb: Shop::LOGO_THUMB_WIDTH,
                height_thumb: Shop::LOGO_THUMB_HEIGHT,
            );
        }
        try {
            DB::beginTransaction();
            $shop = Shop::create($shop);
            $shop->address()->create($address);
            DB::commit();
            return response()->json(['msg' => 'Shop created successfully','cls' => 'success']);
        }catch (\Throwable $e){
            if (isset($shop['logo'])){
                ImageManager::deletePhoto(Shop::IMAGE_UPLOAD_PATH, $shop['logo']);
                ImageManager::deletePhoto(Shop::THUMB_IMAGE_UPLOAD_PATH, $shop['logo']);
            }
            info('SUPPLIER_STORE_FAILED', ['shop'=>$shop, 'address'=>$address, $e]);
            DB::rollBack();
            return response()->json(['msg' => 'Something is going wrong','cls' => 'warning', 'flag'=>'true']);
        }
    }

    /**
     * @param Shop $shop
     * @return ShopEditResource
     */
    public function show(Shop $shop): ShopEditResource
    {
        $shop->load('address');
        return new ShopEditResource($shop);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop)
    {
        //
    }

    /**
     * @param UpdateShopRequest $request
     * @param Shop $shop
     * @return JsonResponse
     */
    final public function update(UpdateShopRequest $request, Shop $shop): JsonResponse
    {
        $shop_data = (new Shop())->prepareData($request->all(), auth());
        $address_data = (new Address())->prepareData($request->all());
        if ($request->has('logo')){
            $name = Str::slug($shop_data['name'].'-'.now());
            $shop_data['logo'] = ImageManager::processImageUpload(
                $request->input('logo'),
                $name,
                path: Shop::IMAGE_UPLOAD_PATH,
                width: Shop::LOGO_WIDTH,
                height: Shop::LOGO_HEIGHT,
                path_thumb: Shop::THUMB_IMAGE_UPLOAD_PATH,
                width_thumb:Shop::LOGO_THUMB_WIDTH,
                height_thumb: Shop::LOGO_THUMB_HEIGHT,
                existing_photo: $shop->logo
            );
        }
        try {
            DB::beginTransaction();
            $shop_data = $shop->update($shop_data);
            $shop->address()->update($address_data);
            DB::commit();
            return response()->json(['msg' => 'Shop updated successfully','cls' => 'success']);
        }catch (\Throwable $e){
            info('SUPPLIER_STORE_FAILED', ['shop'=>$shop_data, 'address'=>$address_data, $e]);
            DB::rollBack();
            return response()->json(['msg' => 'Something is going wrong','cls' => 'warning', 'flag'=>'true']);
        }
    }

    /**
     * @param Shop $shop
     * @return JsonResponse
     */
    final public function destroy(Shop $shop): JsonResponse
    {
        if (!empty($shop->logo)){
            ImageManager::deletePhoto(Shop::IMAGE_UPLOAD_PATH, $shop['logo']);
            ImageManager::deletePhoto(Shop::THUMB_IMAGE_UPLOAD_PATH, $shop['logo']);
        }
        (new Address())->deleteAddressBySupplierId($shop);
        $shop->delete();
        return response()->json(['msg' => 'Shop deleted successfully', 'cls'=>'warning']);
    }


    public function getShopList(): JsonResponse
    {
        $shops = (new Shop())->getShopListIdName();
        return response()->json($shops);
    }
}
