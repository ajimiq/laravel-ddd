<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">注文一覧</h1>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        表示されている注文データはすべてテスト用の架空のデータです。
                    </p>
                </div>
            </div>
        </div>

        {{-- 検索フォーム --}}
        <form method="GET" action="{{ route('orders.index') }}" class="bg-white p-6 rounded-lg shadow-md mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">ステータス</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">すべて</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ $search['status'] === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="ordered_from" class="block text-sm font-medium text-gray-700">注文日（開始）</label>
                    <input type="date" name="ordered_from" id="ordered_from" 
                        value="{{ $search['ordered_from'] }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="ordered_to" class="block text-sm font-medium text-gray-700">注文日（終了）</label>
                    <input type="date" name="ordered_to" id="ordered_to" 
                        value="{{ $search['ordered_to'] }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        検索
                    </button>
                </div>
            </div>
        </form>

        {{-- 注文一覧テーブル --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">注文ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ステータス</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">顧客名</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">注文金額</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">注文日時</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->order_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $order->status === 'shipped' ? 'bg-green-100 text-green-800' : 
                                       ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($order->status === 'failed' ? 'bg-red-100 text-red-800' : 
                                       ($order->status === 'canceled' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800'))) }}">
                                    {{ $statuses[$order->status] ?? $order->status }}
                                </span>
                                {{-- @if($order->canceled_at)
                                    <span class="block text-xs text-gray-500 mt-1">
                                        キャンセル: {{ $order->canceled_at->format('Y-m-d H:i') }}
                                    </span>
                                @endif --}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->customer_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ¥{{ number_format($order->total_amount_with_tax) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->ordered_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 space-x-2">
                                {{-- 詳細ボタン --}}
                                <a href="{{ route('orders.show', $order->order_id) }}" 
                                    class="text-blue-600 hover:text-blue-900"
                                    target="_blank">
                                     詳細
                                </a>

                                {{-- 領収書リンク --}}
                                @if($order->status !== 'pending')
                                <a href="{{ route('orders.receipt', $order->order_id) }}" 
                                   class="text-blue-600 hover:text-blue-900"
                                   target="_blank">
                                    領収書
                                </a>
                                @else
                                <span class="text-gray-400 cursor-not-allowed" title="決済待ちの注文の領収書は表示できません">
                                    領収書
                                </span>
                                @endif

                                {{-- キャンセルボタン（未発送の場合のみ表示） --}}
                                {{-- @if($order->status === 'unshipped')
                                    <form action="{{ route('orders.cancel', $order->order_id) }}" 
                                          method="POST" 
                                          class="inline-block"
                                          onsubmit="return confirm('注文をキャンセルしてもよろしいですか？');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900 ml-2">
                                            キャンセル
                                        </button>
                                    </form>
                                @endif --}}
                            </td>
                        </tr>
                        {{-- 詳細表示用の行 --}}
                        <tr class="hidden" id="detail-{{ $order->order_id }}">
                            <td colspan="6" class="px-6 py-4 bg-gray-50">
                                <div class="text-sm">
                                    <h4 class="font-bold mb-2">注文商品</h4>
                                    <table class="w-full mb-4">
                                        <thead>
                                            <tr class="text-xs text-gray-500">
                                                <th class="text-left py-2">商品名</th>
                                                <th class="text-right py-2">単価</th>
                                                <th class="text-right py-2">数量</th>
                                                <th class="text-right py-2">小計</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->orderItems as $item)
                                                <tr>
                                                    <td class="py-1">{{ $item->name }}</td>
                                                    <td class="text-right">¥{{ number_format($item->price_with_tax) }}</td>
                                                    <td class="text-right">{{ $item->quantity }}</td>
                                                    <td class="text-right">¥{{ number_format($item->price_with_tax * $item->quantity) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="border-t">
                                                <td colspan="3" class="py-1 text-right">送料:</td>
                                                <td class="text-right">¥{{ number_format($order->shipping_fee_with_tax) }}</td>
                                            </tr>
                                            <tr class="font-bold">
                                                <td colspan="3" class="py-1 text-right">合計:</td>
                                                <td class="text-right">¥{{ number_format($order->total_amount_with_tax) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <h4 class="font-bold mb-2">配送先</h4>
                                    <p class="whitespace-pre-line">{{ $order->customer_address }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ページネーション --}}
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</body>
</html> 