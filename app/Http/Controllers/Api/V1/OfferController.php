<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\FlashDeal;
use App\Model\FlashDealProduct;
use App\Model\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function __construct(
        private FlashDeal $flash_deal,
        private FlashDealProduct $flash_deal_product,
        private Product $product
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_flash_deal(Request $request): JsonResponse
    {
        try {
            $flash_deal = $this->flash_deal->active()->where('deal_type','flash_deal')->first();

            if (!isset($flash_deal)){
                $products = [
                    'total_size' => null,
                    'limit' => $request['limit'],
                    'offset' => $request['offset'],
                    'flash_deal' => $flash_deal,
                    'products' => []
                ];
                return response()->json($products, 200);
            }

            $p_ids = $this->flash_deal_product->with(['product'])
                ->whereHas('product',function($q){
                    $q->active();
                })
                ->where(['flash_deal_id' => $flash_deal->id])
                ->pluck('product_id')
                ->toArray();

            $paginator = $this->product->with(['rating'])
                ->whereIn('id', $p_ids)
                ->paginate($request['limit'], ['*'], 'page', $request['offset']);

            $products = [
                'total_size' => $paginator->total(),
                'limit' => $request['limit'],
                'offset' => $request['offset'],
                'flash_deal' => $flash_deal,
                'products' => $paginator->items()
            ];

            $products['products'] = Helpers::product_data_formatting($products['products'], true);
            return response()->json($products, 200);

        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    /**
     * @param Request $request
     * @param $flash_deal_id
     * @return JsonResponse
     */
    public function get_flash_deal_products(Request $request, $flash_deal_id): JsonResponse
    {
        $p_ids = $this->flash_deal_product->with(['product'])
            ->whereHas('product',function($q){
                $q->active();
            })
            ->where(['flash_deal_id' => $flash_deal_id])
            ->pluck('product_id')
            ->toArray();


        if (count($p_ids) > 0) {
            $paginator = $this->product->with(['rating'])
               ->whereIn('id', $p_ids)
               ->paginate($request['limit'], ['*'], 'page', $request['offset']);

            $products = [
                'total_size' => $paginator->total(),
                'limit' => $request['limit'],
                'offset' => $request['offset'],
                'products' => $paginator->items()
            ];

            $products['products'] = Helpers::product_data_formatting($products['products'], true);
            return response()->json($products, 200);
        }

        return response()->json([], 200);
    }
}
