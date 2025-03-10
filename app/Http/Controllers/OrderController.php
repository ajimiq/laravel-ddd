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
        $result = $this->orderIndexUseCase->execute([
            'status' => $request->status,
            'ordered_from' => $request->ordered_from,
            'ordered_to' => $request->ordered_to,
        ]);

        return view('orders.index', $result);
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
        try {
            $this->orderCancelUseCase->execute(
                $orderId,
                $request->input('cancel_reason'),
                now()->format('Y-m-d H:i:s')
            );

            return response()->json([
                'message' => '注文をキャンセルしました。',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
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

        $result = $this->orderShowReceiptUseCase->execute($orderId);
        
        return view('orders.receipt', [
            'order' => $result['order'],
            'company' => $result['company'],
            'receipt' => $result['receipt'],
            'taxAmountsByRate' => $result['tax_amounts_by_rate'],
        ]);
    }

    /**
     * 注文詳細を表示
     */
    public function showDetail(string $orderId): View
    {
        $result = $this->orderShowUseCase->execute($orderId);

        return view('orders.show', [
            'order' => $result['order'],
            'taxAmountsByRate' => $result['tax_amounts_by_rate'],
            'statuses' => $result['statuses'],
        ]);
    }
} 