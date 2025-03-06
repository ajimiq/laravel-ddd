<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文詳細 - {{ $order->order_id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white">
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        表示されているデータはテスト用の架空のデータです。実際の取引を示すものではありません。
                    </p>
                </div>
            </div>
        </div>
        {{-- ヘッダー --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">注文詳細</h1>
            <a href="javascript:window.close()" class="text-blue-600 hover:text-blue-900">閉じる</a>
        </div>

        {{-- 注文基本情報 --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-bold mb-4">注文情報</h2>
                    <dl class="grid grid-cols-3 gap-2 text-sm">
                        <dt class="text-gray-600">注文番号:</dt>
                        <dd class="col-span-2">{{ $order->order_id }}</dd>
                        
                        <dt class="text-gray-600">注文日時:</dt>
                        <dd class="col-span-2">{{ $order->ordered_at->format('Y年m月d日 H:i:s') }}</dd>
                        
                        <dt class="text-gray-600">ステータス:</dt>
                        <dd class="col-span-2">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $order->status === 'shipped' ? 'bg-green-100 text-green-800' : 
                                   ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($order->status === 'failed' ? 'bg-red-100 text-red-800' : 
                                   ($order->status === 'cancelled' ? 'bg-red-100 text-gray-800' : 'bg-blue-100 text-blue-800'))) }}">
                                {{ $statuses[$order->status] ?? $order->status }}
                            </span>
                        </dd>
                    </dl>
                </div>

                <div>
                    <h2 class="text-lg font-bold mb-4">お客様情報</h2>
                    <dl class="grid grid-cols-3 gap-2 text-sm">
                        <dt class="text-gray-600">お名前:</dt>
                        <dd class="col-span-2">{{ $order->customer_name }}</dd>
                        
                        <dt class="text-gray-600">メール:</dt>
                        <dd class="col-span-2">{{ $order->customer_email }}</dd>
                        
                        <dt class="text-gray-600">電話番号:</dt>
                        <dd class="col-span-2">{{ $order->customer_phone }}</dd>
                        
                        <dt class="text-gray-600">配送先:</dt>
                        <dd class="col-span-2 whitespace-pre-line">{{ $order->customer_address }}</dd>
                    </dl>
                </div>
            </div>
            <!-- キャンセル情報 -->
            @if($order->status === 'cancelled')
            <div class="mt-4">
                <h4 class="font-bold mb-2">キャンセル情報</h4>
                <div class="bg-gray-50 p-4 rounded">
                    <div class="mb-2">
                        <span class="text-gray-600">キャンセル日時:</span>
                        <span class="ml-2">{{ $order->canceled_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">キャンセル理由:</span>
                        <span class="ml-2">{{ $order->cancel_reason }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>


        {{-- 注文商品 --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-bold mb-4">注文商品</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2 text-left">商品名</th>
                            <th class="py-2 text-right">単価（税込）</th>
                            <th class="py-2 text-right">単価（税抜）</th>
                            <th class="py-2 text-right">税率</th>
                            <th class="py-2 text-right">数量</th>
                            <th class="py-2 text-right">小計（税込）</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr class="border-b">
                                <td class="py-2">{{ $item->name }}</td>
                                <td class="py-2 text-right">¥{{ number_format($item->price_with_tax) }}</td>
                                <td class="py-2 text-right">¥{{ number_format($item->price_without_tax) }}</td>
                                <td class="py-2 text-right">{{ number_format($item->price_tax_rate * 100, 1) }}%</td>
                                <td class="py-2 text-right">{{ $item->quantity }}</td>
                                <td class="py-2 text-right">¥{{ number_format($item->price_with_tax * $item->quantity) }}</td>
                            </tr>
                        @endforeach
                        <tr class="border-b">
                            <td class="py-2">送料</td>
                            <td class="py-2 text-right">¥{{ number_format($order->shipping_fee_with_tax) }}</td>
                            <td class="py-2 text-right">¥{{ number_format($order->shipping_fee_without_tax) }}</td>
                            <td class="py-2 text-right">{{ number_format($order->shipping_fee_tax_rate * 100, 1) }}%</td>
                            <td class="py-2 text-right">-</td>
                            <td class="py-2 text-right">¥{{ number_format($order->shipping_fee_with_tax) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="py-2"></td>
                            <td class="py-2 text-right font-bold">小計（税抜）:</td>
                            <td class="py-2 text-right">¥{{ number_format($order->total_amount_without_tax) }}</td>
                        </tr>
                        @foreach($taxAmountsByRate as $taxInfo)
                            <tr>
                                <td colspan="3" class="py-2"></td>
                                <td class="py-2 text-right font-bold">
                                    消費税 ({{ number_format($taxInfo['tax_rate'] * 100, 1) }}%):
                                </td>
                                <td class="py-2 text-right">
                                    ¥{{ number_format($taxInfo['tax_amount']) }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="border-t-2 border-black">
                            <td colspan="3" class="py-2"></td>
                            <td class="py-2 text-right font-bold">合計（税込）:</td>
                            <td class="py-2 text-right font-bold">¥{{ number_format($order->total_amount_with_tax) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- 操作ボタン --}}
        <div class="flex justify-end space-x-4">
            <a href="{{ route('orders.receipt', $order->order_id) }}" 
               target="_blank"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">領収書を表示</a>
        </div>
    </div>
</body>
</html> 