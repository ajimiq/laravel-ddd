<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Packages\Order\UseCases\OrderShowUseCase;
use App\Packages\Order\UseCases\OrderShowReceiptUseCase;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderShowUseCase $orderShowUseCase,
        private readonly OrderShowReceiptUseCase $orderShowReceiptUseCase
    ) {
    }

    /**
     * 注文一覧を表示
     */
    public function index(Request $request): View
    {
        Log::channel('online')->info(var_export($request->all(), true));

        $query = Order::query()->with('orderItems');
        // ステータスで絞り込み
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 注文日で絞り込み
        if ($request->filled('ordered_from')) {
            $query->where('ordered_at', '>=', $request->ordered_from . ' 00:00:00');
        }
        if ($request->filled('ordered_to')) {
            $query->where('ordered_at', '<=', $request->ordered_to . ' 23:59:59');
        }

        // 注文日の降順でソート
        $query->orderBy('ordered_at', 'desc');

        // ページネーション（1ページ10件）
        $orders = $query->paginate(10)->withQueryString();

        return view('orders.index', [
            'orders' => $orders,
            'statuses' => [
                'pending' => '決済待ち',
                // 'failed' => '失敗',
                'unshipped' => '未発送',
                // 'shipped' => '発送済み',
            ],
            'search' => [
                'status' => $request->status,
                'ordered_from' => $request->ordered_from,
                'ordered_to' => $request->ordered_to,
            ],
        ]);
    }

    public function downloadReceipt(string $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // 領収書のPDF生成処理（実装は省略）
        // return response()->download($pdfPath, "receipt_{$orderId}.pdf");
        
        return back()->with('error', '領収書の出力は準備中です。');
    }

    public function cancel(string $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        if ($order->status !== 'unshipped') {
            return back()->with('error', 'この注文はキャンセルできません。');
        }
        
        // キャンセル処理
        $order->update(['status' => 'canceled']);
        
        return back()->with('success', '注文をキャンセルしました。');
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