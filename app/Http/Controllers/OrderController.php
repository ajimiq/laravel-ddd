<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Packages\Order\UseCases\OrderShowUseCase;
use App\Packages\Order\UseCases\OrderShowReceiptUseCase;
use App\Packages\Order\UseCases\OrderCancelUseCase;
use App\Packages\Order\UseCases\OrderIndexUseCase;
use App\Packages\Order\UseCases\Dtos\OrderIndexRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderShowRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderShowReceiptRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderCancelRequestDto;
use Illuminate\Http\JsonResponse;
use App\Traits\LoggableTrait;

class OrderController extends Controller
{
    use LoggableTrait;

    public function __construct(
        private readonly OrderShowUseCase $orderShowUseCase,
        private readonly OrderShowReceiptUseCase $orderShowReceiptUseCase,
        private readonly OrderCancelUseCase $orderCancelUseCase,
        private readonly OrderIndexUseCase $orderIndexUseCase
    ) {
    }

    /**
     * 注文一覧を表示
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->logCurrentMethod('START request: ' . var_export($request->all(), true));
        // リクエストからDTOを作成
        $requestDto = new OrderIndexRequestDto(
            $request->status,
            $request->ordered_from,
            $request->ordered_to
        );

        // UseCaseを実行
        $responseDto = $this->orderIndexUseCase->execute($requestDto);

        // ビューに渡すデータを取得
        return view('orders.index', $responseDto->toArray());
    }

    /**
     * 注文をキャンセル
     * @param Request $request
     * @param string $orderId
     * @return JsonResponse
     */
    public function cancel(Request $request, string $orderId): JsonResponse
    {
        $this->logCurrentMethod('START request: ' . var_export($request->all(), true));

        // リクエストからDTOを作成
        $requestDto = new OrderCancelRequestDto(
            $orderId,
            $request->input('cancel_reason'),
            now()->format('Y-m-d H:i:s')
        );

        // UseCaseを実行してレスポンスDTOを取得
        $responseDto = $this->orderCancelUseCase->execute($requestDto);

        $this->logCurrentMethod('response: ' . var_export($responseDto->toArray(), true));

        // レスポンスDTOの内容に基づいてJSONレスポンスを返す
        if ($responseDto->isSuccess()) {
            return response()->json([
                'success' => true,
                'message' => $responseDto->getMessage(),
                'order_id' => $responseDto->getOrderId()
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $responseDto->getMessage(),
                'order_id' => $responseDto->getOrderId()
            ], 400);
        }
    }

    /**
     * 領収書を表示
     * @param string $orderId
     * @return View
     */
    public function showReceipt(string $orderId): View
    {
        $this->logCurrentMethod('START request: ' . $orderId);

        // リクエストからDTOを作成
        $requestDto = new OrderShowReceiptRequestDto($orderId);

        // UseCaseを実行
        $responseDto = $this->orderShowReceiptUseCase->execute($requestDto);

        // ビューに渡すデータを取得
        return view('orders.receipt', $responseDto->toArray());
    }

    /**
     * 注文詳細を表示
     * @param string $orderId
     * @return View
     */
    public function showDetail(string $orderId): View
    {
        $this->logCurrentMethod('START request: ' . $orderId);

        // リクエストからDTOを作成
        $requestDto = new OrderShowRequestDto($orderId);

        // UseCaseを実行
        $responseDto = $this->orderShowUseCase->execute($requestDto);

        // ビューに渡すデータを取得
        return view('orders.detail', $responseDto->toArray());
    }
}
