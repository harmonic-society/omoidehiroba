# 思い出売場 通販サイト構築手順書
## AWS + Render + GitHub による環境構築ガイド

---

## 目次
1. [プロジェクト概要](#プロジェクト概要)
2. [必要なアカウント](#必要なアカウント)
3. [アーキテクチャ構成](#アーキテクチャ構成)
4. [事前準備](#事前準備)
5. [GitHubリポジトリのセットアップ](#gitHubリポジトリのセットアップ)
6. [AWS環境の構築](#aws環境の構築)
7. [Renderでのデプロイ](#renderでのデプロイ)
8. [ドメイン設定](#ドメイン設定)
9. [セキュリティ設定](#セキュリティ設定)
10. [運用・保守](#運用保守)

---

## プロジェクト概要

**サイト名**: 思い出売場
**目的**: 思い出広場の通販サイト
**技術スタック**: WordPress + WooCommerce
**インフラ構成**:
- フロントエンド・バックエンド: Render
- データベース: AWS RDS (MySQL)
- ストレージ: AWS S3
- CDN: CloudFront (オプション)

---

## 必要なアカウント

以下のアカウントを事前に作成してください:

1. **GitHubアカウント**
   - URL: https://github.com/signup
   - プラン: Free (無料)で開始可能

2. **AWSアカウント**
   - URL: https://aws.amazon.com/jp/
   - 初回12ヶ月無料枠あり
   - クレジットカード必須

3. **Renderアカウント**
   - URL: https://render.com/
   - GitHubアカウントでサインアップ推奨
   - 無料プランあり

---

## アーキテクチャ構成

```
┌─────────────┐
│   ユーザー   │
└──────┬──────┘
       │
       ▼
┌─────────────────────────┐
│  CloudFront (CDN)       │ ← オプション
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│   Render (WordPress)    │
│   - Web Service         │
└───────┬─────────┬───────┘
        │         │
        ▼         ▼
┌───────────┐  ┌──────────────┐
│  AWS RDS  │  │   AWS S3     │
│  (MySQL)  │  │ (メディア保存)│
└───────────┘  └──────────────┘
        │
        ▼
┌─────────────────────────┐
│      GitHub             │
│  (ソースコード管理)      │
└─────────────────────────┘
```

---

## 事前準備

### 1. ローカル環境の確認

```bash
# Gitがインストールされているか確認
git --version

# SSHキーの確認(なければ生成)
ls -la ~/.ssh/id_rsa.pub

# なければ生成
ssh-keygen -t rsa -b 4096 -C "your-email@example.com"
```

### 2. 必要なツールのインストール

```bash
# AWS CLI のインストール (macOS)
brew install awscli

# AWS CLI のバージョン確認
aws --version

# Composerのインストール (WordPress管理用)
brew install composer
```

---

## GitHubリポジトリのセットアップ

### 1. 新規リポジトリの作成

```bash
# 現在のプロジェクトディレクトリで実行
cd /Users/yume/Desktop/omoidehiroba

# すでにGitリポジトリがある場合はスキップ
# git init

# .gitignoreファイルの作成
cat > .gitignore << 'EOF'
# WordPress
wp-config.php
wp-content/uploads/
wp-content/cache/
wp-content/backup-db/

# Environment
.env
.env.local
.env.production

# Dependencies
/vendor/
node_modules/

# OS
.DS_Store
Thumbs.db

# IDE
.vscode/
.idea/

# Logs
*.log
error_log
debug.log
EOF

# コミット
git add .
git commit -m "Initial commit: 思い出売場プロジェクト"
```

### 2. GitHubにプッシュ

1. GitHubで新規リポジトリを作成
   - リポジトリ名: `omoidehiroba-shop`
   - プライベート/パブリック: プライベート推奨
   - READMEやライセンスは追加しない

2. ローカルリポジトリと連携

```bash
# リモートリポジトリを追加
git remote add origin https://github.com/YOUR_USERNAME/omoidehiroba-shop.git

# または既存のoriginがある場合
git remote set-url origin https://github.com/YOUR_USERNAME/omoidehiroba-shop.git

# mainブランチにプッシュ
git push -u origin main
```

### 3. ブランチ戦略の設定

```bash
# 開発用ブランチの作成
git checkout -b develop
git push -u origin develop

# 本番用はmainブランチ
# ステージング用はstagingブランチ(必要に応じて)
git checkout -b staging
git push -u origin staging
```

---

## AWS環境の構築

### 1. AWS CLIの設定

```bash
# AWS CLIの認証情報を設定
aws configure

# 以下を入力
# AWS Access Key ID: (AWSコンソールで発行したアクセスキー)
# AWS Secret Access Key: (シークレットキー)
# Default region name: ap-northeast-1 (東京リージョン)
# Default output format: json
```

### 2. RDS (データベース) の構築

#### 2-1. セキュリティグループの作成

1. AWSマネジメントコンソールにログイン
2. EC2 → セキュリティグループ → セキュリティグループを作成

**設定内容:**
- セキュリティグループ名: `omoidehiroba-db-sg`
- 説明: `RDS MySQL for 思い出売場`
- VPC: デフォルトVPC

**インバウンドルール:**
- タイプ: MySQL/Aurora (3306)
- ソース: Renderのアウトバウンドアドレス
  - 一時的に `0.0.0.0/0` で設定し、後で制限することも可能

#### 2-2. RDSインスタンスの作成

1. RDS → データベース → データベースの作成

**設定内容:**

- **エンジンタイプ**: MySQL
- **エンジンバージョン**: 8.0.x (最新の安定版)
- **テンプレート**: 無料利用枠 (開発・テスト用)
  - 本番環境の場合は「本番稼働用」を選択

- **DB インスタンス識別子**: `omoidehiroba-db`
- **マスターユーザー名**: `admin`
- **マスターパスワード**: 強力なパスワードを設定 (記録しておく)

- **DB インスタンスクラス**: db.t3.micro (無料枠対象)
  - 本番環境: db.t3.small 以上推奨

- **ストレージ**:
  - ストレージタイプ: 汎用SSD (gp3)
  - 割り当てストレージ: 20 GiB
  - ストレージの自動スケーリング: 有効 (最大100GiB)

- **接続**:
  - VPC: デフォルトVPC
  - パブリックアクセス: あり (Renderから接続するため)
  - VPCセキュリティグループ: `omoidehiroba-db-sg`

- **データベース認証**: パスワード認証

- **追加設定**:
  - 最初のデータベース名: `omoidehiroba_shop`
  - バックアップ保持期間: 7日
  - 暗号化: 有効

2. 作成後、エンドポイントをメモ
   - 例: `omoidehiroba-db.xxxxxxxxxxxx.ap-northeast-1.rds.amazonaws.com`

#### 2-3. データベース接続テスト

```bash
# MySQLクライアントでテスト (ローカルにMySQLクライアントがある場合)
mysql -h omoidehiroba-db.xxxxxxxxxxxx.ap-northeast-1.rds.amazonaws.com \
      -P 3306 \
      -u admin \
      -p omoidehiroba_shop
```

### 3. S3 (ストレージ) の構築

#### 3-1. S3バケットの作成

```bash
# バケット名(グローバルで一意である必要があります)
BUCKET_NAME="omoidehiroba-shop-media"

# S3バケットの作成
aws s3 mb s3://${BUCKET_NAME} --region ap-northeast-1
```

または、AWSコンソールから:

1. S3 → バケットを作成

**設定内容:**
- バケット名: `omoidehiroba-shop-media-[ランダム文字列]`
- リージョン: アジアパシフィック (東京) ap-northeast-1
- パブリックアクセスをすべてブロック: オフ
  - 画像を公開する必要があるため

#### 3-2. バケットポリシーの設定

1. 作成したバケット → アクセス許可 → バケットポリシー

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "PublicReadGetObject",
      "Effect": "Allow",
      "Principal": "*",
      "Action": "s3:GetObject",
      "Resource": "arn:aws:s3:::omoidehiroba-shop-media/*"
    }
  ]
}
```

#### 3-3. CORS設定

1. バケット → アクセス許可 → CORS

```json
[
  {
    "AllowedHeaders": ["*"],
    "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
    "AllowedOrigins": ["*"],
    "ExposeHeaders": ["ETag"]
  }
]
```

#### 3-4. IAMユーザーの作成 (S3アクセス用)

1. IAM → ユーザー → ユーザーを追加

**設定内容:**
- ユーザー名: `omoidehiroba-s3-user`
- アクセスキー - プログラムによるアクセス: チェック

2. アクセス許可

ポリシーを直接アタッチ → ポリシーの作成

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "s3:PutObject",
        "s3:GetObject",
        "s3:DeleteObject",
        "s3:ListBucket"
      ],
      "Resource": [
        "arn:aws:s3:::omoidehiroba-shop-media",
        "arn:aws:s3:::omoidehiroba-shop-media/*"
      ]
    }
  ]
}
```

3. アクセスキーとシークレットキーをメモ (後で使用)

### 4. CloudFront (CDN) の設定 (オプション)

高速化とコスト削減のため、CloudFrontの利用を推奨します。

1. CloudFront → ディストリビューションを作成

**設定内容:**
- オリジンドメイン: S3バケットを選択
- オリジンアクセス: パブリック
- ビューワープロトコルポリシー: Redirect HTTP to HTTPS
- 許可されたHTTPメソッド: GET, HEAD, OPTIONS, PUT, POST, PATCH, DELETE
- キャッシュポリシー: CachingOptimized

2. ディストリビューションドメイン名をメモ
   - 例: `d1234567890abc.cloudfront.net`

---

## Renderでのデプロイ

### 1. Renderアカウントのセットアップ

1. https://render.com/ にアクセス
2. "Get Started for Free" をクリック
3. GitHubアカウントで認証
4. GitHubリポジトリへのアクセスを許可

### 2. Web Serviceの作成

#### 2-1. 新規サービスの作成

1. Dashboard → New + → Web Service
2. GitHubリポジトリ `omoidehiroba-shop` を選択
3. Connect

#### 2-2. サービス設定

**基本設定:**
- Name: `omoidehiroba-shop`
- Region: Oregon (US West) または Singapore (最も近いリージョン)
  - 注: 東京リージョンはないため、Singaporeが推奨
- Branch: `main`
- Runtime: Docker または Native Environment

**Native Environmentの場合:**
- Build Command:
  ```bash
  composer install --no-dev --optimize-autoloader
  ```
- Start Command:
  ```bash
  php -S 0.0.0.0:$PORT -t .
  ```

**Dockerの場合 (推奨):**

プロジェクトルートに `Dockerfile` を作成:

```dockerfile
FROM php:8.1-apache

# 必要な拡張機能のインストール
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    mbstring \
    exif \
    pcntl \
    bcmath \
    opcache

# Apacheモジュールの有効化
RUN a2enmod rewrite

# 作業ディレクトリ
WORKDIR /var/www/html

# WordPressのダウンロード
RUN curl -O https://wordpress.org/latest.tar.gz \
    && tar -xzf latest.tar.gz \
    && mv wordpress/* . \
    && rm -rf wordpress latest.tar.gz

# テーマファイルのコピー
COPY . /var/www/html/wp-content/themes/omoidehiroba/

# 権限設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Apache設定
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
```

`apache-config.conf` も作成:

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html

    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

#### 2-3. 環境変数の設定

Environment → Environment Variables

以下の環境変数を追加:

```
# データベース設定
DB_HOST=omoidehiroba-db.xxxxxxxxxxxx.ap-northeast-1.rds.amazonaws.com
DB_NAME=omoidehiroba_shop
DB_USER=admin
DB_PASSWORD=your_rds_password

# WordPress設定
WP_ENV=production
WP_HOME=https://your-site.onrender.com
WP_SITEURL=https://your-site.onrender.com

# WordPress認証キー (https://api.wordpress.org/secret-key/1.1/salt/ で生成)
AUTH_KEY=your_unique_phrase
SECURE_AUTH_KEY=your_unique_phrase
LOGGED_IN_KEY=your_unique_phrase
NONCE_KEY=your_unique_phrase
AUTH_SALT=your_unique_phrase
SECURE_AUTH_SALT=your_unique_phrase
LOGGED_IN_SALT=your_unique_phrase
NONCE_SALT=your_unique_phrase

# AWS S3設定
AWS_ACCESS_KEY_ID=your_s3_access_key
AWS_SECRET_ACCESS_KEY=your_s3_secret_key
AWS_S3_BUCKET=omoidehiroba-shop-media
AWS_S3_REGION=ap-northeast-1
AWS_CLOUDFRONT_URL=https://d1234567890abc.cloudfront.net (オプション)

# その他
WP_DEBUG=false
WP_DEBUG_LOG=false
```

#### 2-4. wp-config.phpの作成

プロジェクトルートに `wp-config.php` を作成:

```php
<?php
/**
 * WordPress設定ファイル
 * 思い出売場 - Render環境用
 */

// データベース設定
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_HOST', getenv('DB_HOST'));
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// 認証用ユニークキー
define('AUTH_KEY',         getenv('AUTH_KEY'));
define('SECURE_AUTH_KEY',  getenv('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY',    getenv('LOGGED_IN_KEY'));
define('NONCE_KEY',        getenv('NONCE_KEY'));
define('AUTH_SALT',        getenv('AUTH_SALT'));
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT',   getenv('LOGGED_IN_SALT'));
define('NONCE_SALT',       getenv('NONCE_SALT'));

// WordPressデータベーステーブルプレフィックス
$table_prefix = 'wp_';

// WordPress URL設定
define('WP_HOME', getenv('WP_HOME'));
define('WP_SITEURL', getenv('WP_SITEURL'));

// SSL設定
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// デバッグモード
define('WP_DEBUG', getenv('WP_DEBUG') === 'true');
define('WP_DEBUG_LOG', getenv('WP_DEBUG_LOG') === 'true');
define('WP_DEBUG_DISPLAY', false);

// ファイルシステム設定
define('FS_METHOD', 'direct');

// S3設定 (WP Offload Mediaプラグイン使用時)
define('AS3CF_SETTINGS', serialize(array(
    'provider' => 'aws',
    'access-key-id' => getenv('AWS_ACCESS_KEY_ID'),
    'secret-access-key' => getenv('AWS_SECRET_ACCESS_KEY'),
)));

// WordPress起動
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
```

**重要**: `wp-config.php` を `.gitignore` に追加してはいけません。環境変数を使用しているため、コミットしても安全です。

### 3. デプロイの実行

1. "Create Web Service" をクリック
2. デプロイが自動的に開始されます
3. ログでデプロイの進行状況を確認

デプロイ完了後、Renderが提供するURLでサイトにアクセス可能:
- 例: `https://omoidehiroba-shop.onrender.com`

---

## ドメイン設定

### 1. カスタムドメインの追加 (Render)

1. Renderダッシュボード → サービス選択
2. Settings → Custom Domains
3. "Add Custom Domain" をクリック
4. ドメイン名を入力 (例: `shop.omoidehiroba.com`)

### 2. DNS設定

ドメインレジストラ (お名前.com、ムームードメインなど) で以下のレコードを追加:

**Aレコード:**
```
Type: A
Name: shop (またはサブドメイン名)
Value: Renderが提供するIPアドレス
TTL: 3600
```

**CNAMEレコード (推奨):**
```
Type: CNAME
Name: shop
Value: omoidehiroba-shop.onrender.com
TTL: 3600
```

### 3. SSL証明書

Renderは自動的にLet's EncryptのSSL証明書を発行・更新します。
設定後、数分でHTTPSアクセスが可能になります。

---

## セキュリティ設定

### 1. RDSセキュリティグループの制限

1. RenderのアウトバウンドIPアドレスを確認
2. RDSセキュリティグループのインバウンドルールを更新
   - `0.0.0.0/0` を削除
   - RenderのIPアドレス範囲のみ許可

### 2. WordPressセキュリティプラグインのインストール

推奨プラグイン:
- **Wordfence Security**: ファイアウォール・マルウェアスキャン
- **iThemes Security**: 総合セキュリティ対策
- **WP Limit Login Attempts**: ログイン試行回数制限

### 3. 定期バックアップの設定

#### RDSスナップショット

1. RDS → データベース → `omoidehiroba-db`
2. アクション → スナップショットの作成
3. 自動スナップショット: 有効 (バックアップ保持期間: 7日)

#### S3バケットのバージョニング

```bash
aws s3api put-bucket-versioning \
  --bucket omoidehiroba-shop-media \
  --versioning-configuration Status=Enabled
```

### 4. WAF設定 (オプション)

CloudFrontを使用している場合、AWS WAFでWebアプリケーションファイアウォールを設定:

1. WAF & Shield → Web ACLs → Create web ACL
2. CloudFrontディストリビューションと関連付け
3. ルールを追加:
   - SQLインジェクション対策
   - XSS対策
   - レート制限

---

## 運用・保守

### 1. モニタリング

#### Renderのモニタリング

- Dashboard → サービス → Metrics
  - CPU使用率
  - メモリ使用率
  - リクエスト数
  - レスポンスタイム

#### CloudWatchアラーム (AWS)

```bash
# RDS CPU使用率アラーム
aws cloudwatch put-metric-alarm \
  --alarm-name omoidehiroba-db-cpu-high \
  --alarm-description "RDS CPU使用率が80%を超えた" \
  --metric-name CPUUtilization \
  --namespace AWS/RDS \
  --statistic Average \
  --period 300 \
  --threshold 80 \
  --comparison-operator GreaterThanThreshold \
  --evaluation-periods 2 \
  --dimensions Name=DBInstanceIdentifier,Value=omoidehiroba-db
```

### 2. ログ管理

#### Renderログ

- Logs → すべてのログを確認可能
- エラーログは自動的に記録

#### RDSログ

1. RDS → データベース → ログとイベント
2. エラーログ、スロークエリログを確認

### 3. スケーリング

#### 垂直スケーリング (スペックアップ)

**Render:**
1. Settings → Instance Type
2. より高性能なプランに変更

**RDS:**
1. RDS → データベース → 変更
2. インスタンスクラスを変更 (例: db.t3.micro → db.t3.small)

#### 水平スケーリング (複数インスタンス)

Renderの有料プランで複数インスタンスを実行可能

### 4. WordPress更新手順

```bash
# 1. ローカルで更新とテスト
cd /Users/yume/Desktop/omoidehiroba

# 2. developブランチで作業
git checkout develop
git pull origin develop

# 3. WordPress/プラグイン/テーマの更新
# (wp-cliを使用するか、管理画面で更新)

# 4. テスト後、コミット
git add .
git commit -m "WordPress更新: バージョン6.x.x"

# 5. mainブランチにマージ
git checkout main
git merge develop
git push origin main

# 6. Renderで自動デプロイが実行される
```

### 5. トラブルシューティング

#### サイトにアクセスできない

```bash
# 1. Renderのログを確認
# Dashboard → Logs

# 2. RDS接続を確認
mysql -h [RDS_ENDPOINT] -u admin -p

# 3. DNS設定を確認
dig shop.omoidehiroba.com

# 4. SSL証明書を確認
openssl s_client -connect shop.omoidehiroba.com:443
```

#### データベースエラー

1. RDSのステータス確認
2. セキュリティグループのインバウンドルール確認
3. wp-config.phpの接続情報確認
4. RDSログでエラー詳細を確認

#### 画像アップロードエラー

1. S3バケットのアクセス許可確認
2. IAMユーザーのポリシー確認
3. WordPressのメディア設定確認
4. WP Offload Mediaプラグインの設定確認

---

## コスト見積もり

### AWS (月額)

- **RDS (db.t3.micro)**: 無料枠内 → $0
  - 無料枠終了後: 約$15-20/月
- **S3 (50GB)**: 約$1-2/月
- **CloudFront (100GB転送)**: 約$8-10/月

**合計**: 無料枠内 $1-2/月、終了後 約$24-32/月

### Render (月額)

- **Free Plan**: $0 (制限あり)
- **Starter Plan**: $7/月
- **Standard Plan**: $25/月 (推奨)

**推奨合計コスト**: 約$26-34/月

---

## チェックリスト

デプロイ前の最終確認:

- [ ] GitHubリポジトリ作成・プッシュ完了
- [ ] AWS RDS作成・接続確認
- [ ] AWS S3バケット作成・ポリシー設定
- [ ] IAMユーザー作成・アクセスキー取得
- [ ] Renderサービス作成
- [ ] 環境変数すべて設定
- [ ] wp-config.php作成
- [ ] Dockerfile/Build設定完了
- [ ] デプロイ成功・サイトアクセス確認
- [ ] WordPress初期設定完了
- [ ] WooCommerceインストール・設定
- [ ] S3連携プラグインインストール
- [ ] セキュリティプラグインインストール
- [ ] バックアップ設定完了
- [ ] ドメイン設定・SSL有効化
- [ ] モニタリング・アラート設定

---

## 参考リンク

- [WordPress公式サイト](https://wordpress.org/)
- [WooCommerce公式サイト](https://woocommerce.com/)
- [Render公式ドキュメント](https://render.com/docs)
- [AWS RDS公式ドキュメント](https://docs.aws.amazon.com/rds/)
- [AWS S3公式ドキュメント](https://docs.aws.amazon.com/s3/)
- [WP Offload Media](https://deliciousbrains.com/wp-offload-media/)

---

## サポート

問題が発生した場合:

1. Renderサポート: https://render.com/support
2. AWSサポート: https://console.aws.amazon.com/support/
3. WordPressフォーラム: https://ja.wordpress.org/support/

---

**作成日**: 2025-11-08
**バージョン**: 1.0
**プロジェクト**: 思い出売場
