# Regression Tests

BlogPost機能のリグレッションテストをGolangで実装しています。

## テスト内容

### BlogPost CRUD テスト
- ✅ 新規作成（DB + ファイル作成）
- ✅ 読み取り
- ✅ 更新（DB + ファイル更新）
- ✅ 削除（DB + ファイル削除）
- ✅ 一覧取得

### ファイル同期テスト
- ✅ DB保存時のファイル作成確認
- ✅ YAML front matter形式確認
- ✅ ファイル内容の整合性確認

### Filament管理画面テスト
- ✅ 管理画面アクセス確認

## ローカル実行

```bash
# Laravel サーバー起動
php artisan serve

# 別ターミナルでテスト実行
cd tests/regression
go mod tidy
go test -v ./...
```

## GitHub Actions

プッシュ時に自動実行されます：
1. Laravel環境セットアップ
2. データベースマイグレーション
3. Laravel開発サーバー起動
4. Goリグレッションテスト実行

## 環境変数

- `TEST_BASE_URL`: テスト対象のベースURL（デフォルト: http://localhost:8000）

## 必要な前提条件

- BlogPost API エンドポイントが実装されていること
- ファイルストレージが設定されていること
- データベースが正常に動作していること