# 架空のECサイト受注管理システム

## 概要

- 架空のECサイトから受注データを取得して、管理するシステムです。
- DDD（オーニオンアーキテクチャ）をベースにLaravelでの開発の練習用に作成しています。
- GitHub Copitot,Cursor(主にCursor)を活用して、開発しています。

## 開発環境

- PHP 8.4
- Laravel 12
- MySQL 8.0
- Composer

## 機能概要

- 架空のECサイトから受注データを取得するバッチ
    - フィットネス用品を扱った架空のECサイトでランダムでそれっぽいデータを作成する。
- 受注データを一覧表示機能
- 受注データの詳細表示機能
- 受注データの領収書表示機能
    - インボイス対応

## ディレクトリ構成

```
.
├── app/
│   ├── Console/
│   │   └── Commands/ -> バッチ
│   ├── Http/
│   │   └── Controllers/ -> コントローラ
│   ├── Models/ -> モデル
│   └── Packages/ -> オニオンアーキテクチャベースのモジュールをこの配下で管理
│       ├── Orders/ -> 受注コンテキスト
│       │   ├── Domains/ -> ドメイン層
│       │   │   ├── Services/ -> ドメインサービス
│       │   │   ├── ValueObjects/ -> 値オブジェクト
│       │   ├── Infrastructures/ -> インフラ層（DB読み書き、外部連携）
│       │   │   ├── OrderRepository.php
│       │   │   └── TestMallOrderGetter.php
│       │   └── UseCases/ -> ユースケース層
│       │       ├── OrderShowUseCase.php
│       │       ├── OrderShowReceiptUseCase.php
│       │       └── OrderReceiveUseCase.php
│       └── Shared/ -> コンテキストで共有するドメインをこの配下で管理
│           └── Domains/
│               └── ValueObjects/│
├── database/
│   ├── migrations/ -> マイグレーション
│   └── seeders/ -> シーダー
├── resources/
│   └── views/ -> 画面表示
│       ├── index.blade.php -> トップ画面
│       └── orders/ -> 受注画面
└── routes/
    └── web.php -> ルーター
```

## 初期設定

- MySQLをインストール

- .envのDBにMySQLの設定情報を記述

### マイグレーションとシーダーを実行

```bash
php artisan migrate:fresh --seed
```

### テストモールから注文を取得する

```bash
php artisan command:receiveTestMallOrderBatch
```

※ランダムで10件ほど取得できる。追加取得した場合は再度実行する。

### laravelを起動する

```bash
php artisan serve
```

### 画面で受注データを確認できる

http://127.0.0.1:8000/
