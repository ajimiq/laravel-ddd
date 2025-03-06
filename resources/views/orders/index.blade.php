<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>注文一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- トーストメッセージ -->
    <div id="toast" class="fixed top-4 right-4 z-50 transform transition-transform duration-300 translate-x-full">
        <div class="bg-white rounded-lg shadow-lg p-4 max-w-sm">
            <div class="flex items-center">
                <!-- 成功アイコン -->
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <!-- メッセージ -->
                <div class="ml-3">
                    <p id="toastMessage" class="text-sm font-medium text-gray-900"></p>
                </div>
                <!-- 閉じるボタン -->
                <div class="ml-4 flex-shrink-0 flex">
                    <button type="button" class="inline-flex text-gray-400 hover:text-gray-500" onclick="hideToast()">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

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
                                       ($order->status === 'cancelled' ? 'bg-red-100 text-gray-800' : 'bg-blue-100 text-blue-800'))) }}">
                                    {{ $statuses[$order->status] ?? $order->status }}
                                </span>
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
                                <span class="text-gray-400 cursor-not-allowed" title="未発送の注文の領収書は表示できません">
                                    領収書
                                </span>
                                @endif

                                {{-- キャンセルボタン（未発送の場合のみ表示） --}}
                                @if ($order->status === 'unshipped')
                                    <button type="button" class="text-red-600 hover:text-red-900 cancel-order" data-order-id="{{ $order->order_id }}">
                                        キャンセル
                                    </button>
                                @endif
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

    <!-- キャンセルモーダル -->
    <div id="cancelModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity hidden">
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">注文キャンセル</h3>
                                <div class="mt-2">
                                    <form id="cancelForm">
                                        <input type="hidden" id="cancelOrderId" name="order_id">
                                        <div class="mt-2">
                                            <label for="cancelReason" class="block text-sm font-medium text-gray-700">キャンセル理由</label>
                                            <textarea id="cancelReason" name="cancel_reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" id="confirmCancel" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">キャンセルする</button>
                        <button type="button" id="closeModal" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">閉じる</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('cancelModal');
        const closeModalButton = document.getElementById('closeModal');
        const toast = document.getElementById('toast');

        function showToast(message) {
            const toastMessage = document.getElementById('toastMessage');
            toastMessage.textContent = message;
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
            
            // 3秒後に自動で非表示
            setTimeout(hideToast, 3000);
        }

        function hideToast() {
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-full');
        }

        // キャンセルボタンのクリックイベント
        document.querySelectorAll('.cancel-order').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                document.getElementById('cancelOrderId').value = orderId;
                modal.classList.remove('hidden');
            });
        });

        // モーダルを閉じる
        closeModalButton.addEventListener('click', function() {
            modal.classList.add('hidden');
        });

        // モーダルの外側をクリックして閉じる
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });

        // キャンセル確認ボタンのクリックイベント
        document.getElementById('confirmCancel').addEventListener('click', async function() {
            const orderId = document.getElementById('cancelOrderId').value;
            const cancelReason = document.getElementById('cancelReason').value;

            if (!cancelReason.trim()) {
                alert('キャンセル理由を入力してください。');
                return;
            }

            try {
                const response = await fetch(`/orders/${orderId}/cancel`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ cancel_reason: cancelReason })
                });

                const data = await response.json();

                if (response.ok) {
                    // モーダルを閉じる
                    modal.classList.add('hidden');
                    // メッセージを表示
                    showToast(data.message);
                    // 1秒後にページをリロード
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('エラーが発生しました。');
            }
        });
    });
    </script>
</body>
</html> 