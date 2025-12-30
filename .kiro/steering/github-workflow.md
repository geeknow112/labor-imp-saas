---
inclusion: always
---

# GitHub ワークフロー

## 基本的な流れ

### 1. 変更をコミット&プッシュ
```bash
wsl git add .
wsl git commit -m "コミットメッセージ"
wsl git push --set-upstream origin ブランチ名
```

### 2. PR作成（PowerShellで実行）
```bash
gh pr create --title "PRタイトル" --body "PR説明"
```

## 初回設定（必要に応じて）

### Git設定
```bash
wsl git config --global user.email "geeknow112@gmail.com"
wsl git config --global user.name "geeknow112"
```

### WSLリポジトリの安全ディレクトリ設定
```bash
git config --global --add safe.directory '//wsl$/Ubuntu-22.04/home/a/projects/labor-imp-saas'
```

## 注意点
- WSL内でのgitコマンドは `wsl` プレフィックスを付ける
- PR作成はPowerShellで実行（GitHub CLI使用）
- ブランチ名は機能に応じて適切に命名する（例：feature/blog-management）