-- Actualizacion segura de base de datos para desplegar sin ejecutar php artisan migrate.
-- Usar en phpMyAdmin/importador sobre la base actual, despues de hacer respaldo.
-- No borra datos. Solo agrega columnas faltantes y registra migraciones nuevas.

SET @current_database = DATABASE();

-- provider_business_lines
CREATE TABLE IF NOT EXISTS `provider_business_lines` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `provider_business_lines_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `provider_business_lines` (`name`, `active`, `created_at`, `updated_at`) VALUES
('Medicamentos', 1, NOW(), NOW()),
('Servicios farmaceuticos', 1, NOW(), NOW()),
('Materiales de obra', 1, NOW(), NOW()),
('Otros', 1, NOW(), NOW());

INSERT IGNORE INTO `provider_business_lines` (`name`, `active`, `created_at`, `updated_at`)
SELECT DISTINCT TRIM(`business_line`), 1, NOW(), NOW()
FROM `providers`
WHERE `business_line` IS NOT NULL
  AND TRIM(`business_line`) <> '';

-- providers.provider_business_line_id
SET @sql = IF(
    (
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @current_database
          AND TABLE_NAME = 'providers'
          AND COLUMN_NAME = 'provider_business_line_id'
    ) = 0,
    'ALTER TABLE `providers` ADD COLUMN `provider_business_line_id` BIGINT UNSIGNED NULL AFTER `business_line`',
    'SELECT ''providers.provider_business_line_id ya existe'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `providers` p
INNER JOIN `provider_business_lines` l ON l.`name` = p.`business_line`
SET p.`provider_business_line_id` = l.`id`
WHERE p.`provider_business_line_id` IS NULL;

-- recurring_service_receipts.amount
SET @sql = IF(
    (
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @current_database
          AND TABLE_NAME = 'recurring_service_receipts'
          AND COLUMN_NAME = 'amount'
    ) = 0,
    'ALTER TABLE `recurring_service_receipts` ADD COLUMN `amount` DECIMAL(14,2) NULL AFTER `period_start`',
    'SELECT ''recurring_service_receipts.amount ya existe'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- recurring_services.is_domiciled
SET @sql = IF(
    (
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @current_database
          AND TABLE_NAME = 'recurring_services'
          AND COLUMN_NAME = 'is_domiciled'
    ) = 0,
    'ALTER TABLE `recurring_services` ADD COLUMN `is_domiciled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `payment_interval_days`',
    'SELECT ''recurring_services.is_domiciled ya existe'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- purchase_orders.is_credit
SET @sql = IF(
    (
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @current_database
          AND TABLE_NAME = 'purchase_orders'
          AND COLUMN_NAME = 'is_credit'
    ) = 0,
    'ALTER TABLE `purchase_orders` ADD COLUMN `is_credit` TINYINT(1) NOT NULL DEFAULT 0 AFTER `due_date`',
    'SELECT ''purchase_orders.is_credit ya existe'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- purchase_orders.credit_days
SET @sql = IF(
    (
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @current_database
          AND TABLE_NAME = 'purchase_orders'
          AND COLUMN_NAME = 'credit_days'
    ) = 0,
    'ALTER TABLE `purchase_orders` ADD COLUMN `credit_days` SMALLINT UNSIGNED NULL AFTER `is_credit`',
    'SELECT ''purchase_orders.credit_days ya existe'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- companies.purchase_order_notes
SET @sql = IF(
    (
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @current_database
          AND TABLE_NAME = 'companies'
          AND COLUMN_NAME = 'purchase_order_notes'
    ) = 0,
    'ALTER TABLE `companies` ADD COLUMN `purchase_order_notes` TEXT NULL AFTER `address`',
    'SELECT ''companies.purchase_order_notes ya existe'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- providers.reference
SET @sql = IF(
    (
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @current_database
          AND TABLE_NAME = 'providers'
          AND COLUMN_NAME = 'reference'
    ) = 0,
    'ALTER TABLE `providers` ADD COLUMN `reference` VARCHAR(255) NULL AFTER `clabe`',
    'SELECT ''providers.reference ya existe'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- purchase_orders.reference
SET @sql = IF(
    (
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @current_database
          AND TABLE_NAME = 'purchase_orders'
          AND COLUMN_NAME = 'reference'
    ) = 0,
    'ALTER TABLE `purchase_orders` ADD COLUMN `reference` VARCHAR(255) NULL AFTER `credit_days`',
    'SELECT ''purchase_orders.reference ya existe'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- purchase_orders.payment_concept
SET @sql = IF(
    (
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @current_database
          AND TABLE_NAME = 'purchase_orders'
          AND COLUMN_NAME = 'payment_concept'
    ) = 0,
    'ALTER TABLE `purchase_orders` ADD COLUMN `payment_concept` VARCHAR(255) NULL AFTER `reference`',
    'SELECT ''purchase_orders.payment_concept ya existe'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- users.plain_password
SET @sql = IF(
    (
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @current_database
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME = 'plain_password'
    ) = 0,
    'ALTER TABLE `users` ADD COLUMN `plain_password` VARCHAR(255) NULL AFTER `password`',
    'SELECT ''users.plain_password ya existe'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Registrar migraciones como aplicadas para que Laravel no intente repetirlas despues.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_01_000001_add_amount_to_recurring_service_receipts_table', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
    SELECT 1
    FROM (SELECT `migration` FROM `migrations`) AS existing_migrations
    WHERE existing_migrations.`migration` = '2026_07_01_000001_add_amount_to_recurring_service_receipts_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_02_000001_add_is_domiciled_to_recurring_services_table', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
    SELECT 1
    FROM (SELECT `migration` FROM `migrations`) AS existing_migrations
    WHERE existing_migrations.`migration` = '2026_07_02_000001_add_is_domiciled_to_recurring_services_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_02_000002_add_credit_fields_to_purchase_orders_table', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
    SELECT 1
    FROM (SELECT `migration` FROM `migrations`) AS existing_migrations
    WHERE existing_migrations.`migration` = '2026_07_02_000002_add_credit_fields_to_purchase_orders_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_02_000003_add_purchase_order_notes_to_companies_table', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
    SELECT 1
    FROM (SELECT `migration` FROM `migrations`) AS existing_migrations
    WHERE existing_migrations.`migration` = '2026_07_02_000003_add_purchase_order_notes_to_companies_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_07_000001_add_reference_to_providers_and_payment_fields_to_purchase_orders', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
    SELECT 1
    FROM (SELECT `migration` FROM `migrations`) AS existing_migrations
    WHERE existing_migrations.`migration` = '2026_07_07_000001_add_reference_to_providers_and_payment_fields_to_purchase_orders'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_07_000002_add_plain_password_to_users_table', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
    SELECT 1
    FROM (SELECT `migration` FROM `migrations`) AS existing_migrations
    WHERE existing_migrations.`migration` = '2026_07_07_000002_add_plain_password_to_users_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_08_000001_create_provider_business_lines_table', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
    SELECT 1
    FROM (SELECT `migration` FROM `migrations`) AS existing_migrations
    WHERE existing_migrations.`migration` = '2026_07_08_000001_create_provider_business_lines_table'
);

-- Valores seguros para datos existentes.
UPDATE `purchase_orders`
SET `is_credit` = 0
WHERE `is_credit` IS NULL;

UPDATE `recurring_services`
SET `is_domiciled` = 0
WHERE `is_domiciled` IS NULL;

UPDATE `users`
SET `plain_password` = 'ghfarma2026'
WHERE `email` = 'gherzig@sasordenesdecompra.com'
  AND `plain_password` IS NULL;

UPDATE `users`
SET `password` = '$2y$10$tCxEiTqoSdAdwDC.FFeWIu3HC2OzADGP3PYPMeN2wABOgYCEE5MFm',
    `plain_password` = 'ghfarma2026'
WHERE `plain_password` IS NULL;
