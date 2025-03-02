<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>受注管理システム（テスト）</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <div class="min-h-screen flex flex-col">
            {{-- ヘッダー --}}
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold mb-6">受注管理システム（テスト）</h1>
                </div>
            </header>

            {{-- メインコンテンツ --}}
            <main class="flex-grow">
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    {{-- メニューカード --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {{-- 注文一覧カード --}}
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg transition-all hover:shadow-md">
                            <a href="{{ route('orders.index') }}" class="block p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                                            注文一覧
                                        </h2>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            注文の一覧を表示し、詳細確認や状態管理を行います
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </main>

            {{-- フッター --}}
            <footer class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                        &copy; {{ date('Y') }} 受注管理システム All rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    </body>
</html>
