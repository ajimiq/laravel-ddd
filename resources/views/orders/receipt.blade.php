<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>領収書 - {{ $order->order_id }}</title>
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
                        この領収書はテスト用の架空のデータに基づいて作成されています。実際の取引を示すものではありません。
                    </p>
                </div>
            </div>
        </div>
        {{-- 領収書ヘッダー --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold mb-4">領収書</h1>
            <div class="text-right">
                <p class="text-sm">発行日: {{ $receipt['issue_date']->format('Y年m月d日') }}</p>
                <p class="text-sm">領収書番号: {{ $receipt['number'] }}</p>
                <p class="text-sm">注文番号: {{ $order->order_id }}</p>
            </div>
        </div>

        {{-- 宛名と金額 --}}
        <div class="mb-8">
            <p class="text-xl mb-4">{{ $order->customer_name }} 様</p>
            <div class="border-b-2 border-black text-2xl font-bold py-2">
                ￥{{ number_format($order->total_amount_with_tax) }}<span class="text-sm ml-2">（税込）</span>
            </div>
        </div>

        {{-- 明細 --}}
        <div class="mb-8">
            <table class="w-full mb-4">
                <thead>
                    <tr class="border-b">
                        <th class="py-2 text-left">商品名</th>
                        <th class="py-2 text-right">単価</th>
                        <th class="py-2 text-right">数量</th>
                        <th class="py-2 text-right">金額</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                        <tr class="border-b">
                            <td class="py-2">{{ $item->name }}</td>
                            <td class="py-2 text-right">¥{{ number_format($item->price_with_tax) }}</td>
                            <td class="py-2 text-right">{{ $item->quantity }}</td>
                            <td class="py-2 text-right">¥{{ number_format($item->price_with_tax * $item->quantity) }}</td>
                        </tr>
                    @endforeach
                    <tr class="border-b">
                        <td class="py-2">送料</td>
                        <td class="py-2 text-right">-</td>
                        <td class="py-2 text-right">-</td>
                        <td class="py-2 text-right">¥{{ number_format($order->shipping_fee_with_tax) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="py-2"></td>
                        <td class="py-2 text-right font-bold">小計</td>
                        <td class="py-2 text-right">¥{{ number_format($order->total_amount_without_tax) }}</td>
                    </tr>
                    {{-- 税率ごとの内訳を表示 --}}
                    @foreach($taxAmountsByRate  as $taxInfo)
                        <tr>
                            <td colspan="2" class="py-2"></td>
                            <td class="py-2 text-right font-bold">
                                消費税 ({{ number_format($taxInfo['tax_rate'] * 100, 1) }}%)
                            </td>
                            <td class="py-2 text-right">
                                ¥{{ number_format($taxInfo['tax_amount']) }}
                                <span class="text-xs text-gray-500">
                                    (対象: ¥{{ number_format($taxInfo['subtotal_without_tax']) }})
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="border-t-2 border-black">
                        <td colspan="2" class="py-2"></td>
                        <td class="py-2 text-right font-bold">合計</td>
                        <td class="py-2 text-right font-bold">¥{{ number_format($order->total_amount_with_tax) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- 発行元情報 --}}
        <div class="text-center border-t pt-8">
            <h2 class="text-xl font-bold mb-2">{{ $company['name'] }}</h2>
            <p class="text-sm">〒{{ $company['postal_code'] }}</p>
            <p class="text-sm">{{ $company['address'] }}</p>
            <p class="text-sm">TEL: {{ $company['tel'] }}</p>
            <p class="text-sm mt-2">
                適格請求書発行事業者登録番号: {{ $company['registration_number'] }}
            </p>
        </div>

        {{-- 印刷時の注意書きを追加 --}}
        <div class="mt-4 text-center text-xs text-gray-500">
            <p>この領収書は電子的に作成されています。印章や署名がなくても有効です。</p>
            <p>再発行の場合は「再発行」と記載されます。</p>
        </div>

        {{-- 印刷ボタン --}}
        <div class="mt-8 text-center print:hidden">
            <div class="flex justify-center space-x-4">
                <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    印刷する
                </button>
                <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    閉じる
                </button>
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page {
                size: A4;
                margin: 12mm;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</body>
</html> 