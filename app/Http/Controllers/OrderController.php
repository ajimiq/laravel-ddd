<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Packages\Order\UseCases\OrderShowUseCase;
use App\Packages\Order\UseCases\OrderShowReceiptUseCase;
use App\Packages\Order\UseCases\OrderCancelUseCase;
use App\Packages\Order\UseCases\OrderIndexUseCase;
use App\Packages\Order\UseCases\Dtos\OrderIndexRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderShowRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderShowReceiptRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderCancelRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderCancelResponseDto;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderShowUseCase $orderShowUseCase,
        private readonly OrderShowReceiptUseCase $orderShowReceiptUseCase,
        private readonly OrderCancelUseCase $orderCancelUseCase,
        private readonly OrderIndexUseCase $orderIndexUseCase
    ) {
    }

    /**
     * 注文一覧を表示
     */
    public function index(Request $request): View
    {
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

    public function downloadReceipt(string $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // 領収書のPDF生成処理（実装は省略）
        // return response()->download($pdfPath, "receipt_{$orderId}.pdf");
        
        return back()->with('error', '領収書の出力は準備中です。');
    }

    /**
     * 注文をキャンセル
     */
    public function cancel(Request $request, string $orderId): JsonResponse
    {
        // リクエストからDTOを作成
        $requestDto = new OrderCancelRequestDto(
            $orderId,
            $request->input('cancel_reason'),
            now()->format('Y-m-d H:i:s')
        );

        // UseCaseを実行してレスポンスDTOを取得
        $responseDto = $this->orderCancelUseCase->execute($requestDto);

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
     */
    public function showReceipt(string $orderId): View
    {
        // 注文を取得
        $order = Order::findOrFail($orderId);

        // // 決済待ちの場合は領収書を表示しない
        // if ($order->status === 'pending') {
        //     return back()->with('error', '決済待ちの注文の領収書は表示できません。');
        // }

        // リクエストからDTOを作成
        $requestDto = new OrderShowReceiptRequestDto($orderId);

        // UseCaseを実行
        $responseDto = $this->orderShowReceiptUseCase->execute($requestDto);
        
        // ビューに渡すデータを取得
        return view('orders.receipt', $responseDto->toArray());
    }

    /**
     * 注文詳細を表示
     */
    public function showDetail(string $orderId): View
    {
        // リクエストからDTOを作成
        $requestDto = new OrderShowRequestDto($orderId);

        // UseCaseを実行
        $responseDto = $this->orderShowUseCase->execute($requestDto);

        // ビューに渡すデータを取得
        return view('orders.show', $responseDto->toArray());
    }
} 