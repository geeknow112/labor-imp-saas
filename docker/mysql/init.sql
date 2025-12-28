-- 中央管理用データベースの初期化
CREATE DATABASE IF NOT EXISTS labor_imp_saas_central;

-- テナント用データベース作成権限を持つユーザーを作成
CREATE USER IF NOT EXISTS 'labor_user'@'%' IDENTIFIED BY 'labor_password';
GRANT ALL PRIVILEGES ON labor_imp_saas_central.* TO 'labor_user'@'%';
GRANT CREATE, DROP, ALTER, SELECT, INSERT, UPDATE, DELETE ON *.* TO 'labor_user'@'%';

-- 権限を反映
FLUSH PRIVILEGES;