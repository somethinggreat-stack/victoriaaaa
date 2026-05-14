-- ============================================================================
-- VictoriaLove — Live Database Setup
-- ============================================================================
-- Target DB : victoria
-- DB user   : victoriauser
-- Generated from the 14 Laravel migrations under database/migrations/
--
-- How to run on the live server (one of):
--   mysql -u victoriauser -p victoria < setup.sql
--   mysql -u root     -p victoria < setup.sql
--
-- Idempotent: uses CREATE TABLE IF NOT EXISTS and INSERT IGNORE so it is
-- safe to re-run. Marks every migration as already executed in the
-- `migrations` table so future `php artisan migrate` is a no-op.
-- ============================================================================

USE `victoria`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- ────────────────────────────────────────────────────────────────────────────
-- Laravel framework tables
-- ────────────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL PRIMARY KEY,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(255) NOT NULL PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache` (
  `key` VARCHAR(255) NOT NULL PRIMARY KEY,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` VARCHAR(255) NOT NULL PRIMARY KEY,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL,
  `reserved_at` INT UNSIGNED NULL DEFAULT NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` VARCHAR(255) NOT NULL PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options` MEDIUMTEXT NULL,
  `cancelled_at` INT NULL DEFAULT NULL,
  `created_at` INT NOT NULL,
  `finished_at` INT NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────────────
-- Public-form lead capture tables
-- ────────────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(255) NULL DEFAULT NULL,
  `topic` VARCHAR(255) NULL DEFAULT NULL,
  `score` VARCHAR(255) NULL DEFAULT NULL,
  `timeline` VARCHAR(255) NULL DEFAULT NULL,
  `source` VARCHAR(255) NULL DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'new',
  `ip` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` VARCHAR(512) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  KEY `contacts_email_index` (`email`),
  KEY `contacts_status_index` (`status`),
  KEY `contacts_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `leads` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(255) NULL DEFAULT NULL,
  `score` VARCHAR(255) NULL DEFAULT NULL,
  `issue` VARCHAR(255) NULL DEFAULT NULL,
  `goal` VARCHAR(255) NULL DEFAULT NULL,
  `source` VARCHAR(255) NOT NULL DEFAULT 'popup',
  `status` VARCHAR(255) NOT NULL DEFAULT 'new',
  `ip` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` VARCHAR(512) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  KEY `leads_email_index` (`email`),
  KEY `leads_status_index` (`status`),
  KEY `leads_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `onboarding_submissions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `firstname` VARCHAR(255) NOT NULL,
  `lastname` VARCHAR(255) NOT NULL,
  `middlename` VARCHAR(255) NULL DEFAULT NULL,
  `suffix` VARCHAR(255) NULL DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `street_address` VARCHAR(255) NULL DEFAULT NULL,
  `city` VARCHAR(255) NULL DEFAULT NULL,
  `state` VARCHAR(2) NULL DEFAULT NULL,
  `zip` VARCHAR(10) NULL DEFAULT NULL,
  `ssn_encrypted` TEXT NOT NULL,
  `ssn_last4` VARCHAR(4) NOT NULL,
  `birth_date` DATE NOT NULL,
  `crc_status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  `crc_id` VARCHAR(255) NULL DEFAULT NULL,
  `crc_response` TEXT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'new',
  `ip` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` VARCHAR(512) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  KEY `onboarding_submissions_email_index` (`email`),
  KEY `onboarding_submissions_status_index` (`status`),
  KEY `onboarding_submissions_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `funding_applications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(255) NOT NULL,
  `last_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `amount` VARCHAR(255) NULL DEFAULT NULL,
  `confirmed` VARCHAR(255) NULL DEFAULT NULL,
  `limits` VARCHAR(255) NULL DEFAULT NULL,
  `usage` VARCHAR(255) NULL DEFAULT NULL,
  `fico` VARCHAR(255) NULL DEFAULT NULL,
  `situation` VARCHAR(255) NULL DEFAULT NULL,
  `income` VARCHAR(255) NULL DEFAULT NULL,
  `negatives` JSON NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'new',
  `ip` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` VARCHAR(512) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  KEY `funding_applications_email_index` (`email`),
  KEY `funding_applications_status_index` (`status`),
  KEY `funding_applications_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mentorship_leads` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(255) NOT NULL,
  `last_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `situation` VARCHAR(255) NULL DEFAULT NULL,
  `timeline` VARCHAR(255) NULL DEFAULT NULL,
  `hours` VARCHAR(255) NULL DEFAULT NULL,
  `investment` VARCHAR(255) NULL DEFAULT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'new',
  `ip` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` VARCHAR(512) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  KEY `mentorship_leads_email_index` (`email`),
  KEY `mentorship_leads_status_index` (`status`),
  KEY `mentorship_leads_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────────────
-- Subscriptions + payments + webhooks
-- ────────────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NULL DEFAULT NULL,
  `address` VARCHAR(255) NULL DEFAULT NULL,
  `city` VARCHAR(100) NULL DEFAULT NULL,
  `state` VARCHAR(10) NULL DEFAULT NULL,
  `zip` VARCHAR(20) NULL DEFAULT NULL,
  `plan_key` VARCHAR(32) NOT NULL,
  `plan_label` VARCHAR(80) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `recurring_amount` DECIMAL(10,2) NULL DEFAULT NULL,
  `invoice_number` VARCHAR(64) NOT NULL,
  `transaction_id` VARCHAR(64) NULL DEFAULT NULL,
  `auth_code` VARCHAR(32) NULL DEFAULT NULL,
  `arb_subscription_id` VARCHAR(64) NULL DEFAULT NULL,
  `customer_profile_id` VARCHAR(64) NULL DEFAULT NULL,
  `customer_payment_profile_id` VARCHAR(64) NULL DEFAULT NULL,
  `referral_code` VARCHAR(50) NULL DEFAULT NULL,
  `status` VARCHAR(24) NOT NULL DEFAULT 'active',
  `failed_payment_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `first_failed_at` TIMESTAMP NULL DEFAULT NULL,
  `grace_period_ends_at` TIMESTAMP NULL DEFAULT NULL,
  `subscribed_at` TIMESTAMP NULL DEFAULT NULL,
  `next_billing_date` TIMESTAMP NULL DEFAULT NULL,
  `terminated_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `subscriptions_invoice_number_unique` (`invoice_number`),
  KEY `subscriptions_transaction_id_index` (`transaction_id`),
  KEY `subscriptions_arb_subscription_id_index` (`arb_subscription_id`),
  KEY `subscriptions_referral_code_index` (`referral_code`),
  KEY `subscriptions_status_index` (`status`),
  KEY `subscriptions_email_index` (`email`),
  KEY `subscriptions_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `subscription_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `transaction_id` VARCHAR(64) NULL DEFAULT NULL,
  `invoice_number` VARCHAR(64) NULL DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `type` VARCHAR(20) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'captured',
  `event_type_raw` VARCHAR(80) NULL DEFAULT NULL,
  `charged_at` TIMESTAMP NULL DEFAULT NULL,
  `raw_payload` JSON NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `payments_txn_type_unique` (`transaction_id`, `type`),
  KEY `payments_subscription_id_foreign` (`subscription_id`),
  KEY `payments_invoice_number_index` (`invoice_number`),
  KEY `payments_charged_at_index` (`charged_at`),
  KEY `payments_status_index` (`status`),
  CONSTRAINT `payments_subscription_id_foreign`
    FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `subscription_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `subscription_id` BIGINT UNSIGNED NOT NULL,
  `event_type` VARCHAR(40) NOT NULL,
  `payload` JSON NULL,
  `note` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  KEY `subscription_events_subscription_id_foreign` (`subscription_id`),
  KEY `subscription_events_event_type_index` (`event_type`),
  KEY `subscription_events_created_at_index` (`created_at`),
  CONSTRAINT `subscription_events_subscription_id_foreign`
    FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `webhook_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `notification_id` VARCHAR(64) NULL DEFAULT NULL,
  `event_type` VARCHAR(80) NOT NULL,
  `entity_id` VARCHAR(64) NULL DEFAULT NULL,
  `amount` DECIMAL(10,2) NULL DEFAULT NULL,
  `invoice_number` VARCHAR(64) NULL DEFAULT NULL,
  `arb_status` VARCHAR(32) NULL DEFAULT NULL,
  `response_code` VARCHAR(8) NULL DEFAULT NULL,
  `signature_valid` TINYINT(1) NULL DEFAULT NULL,
  `source_ip` VARCHAR(45) NULL DEFAULT NULL,
  `received_at` TIMESTAMP NULL DEFAULT NULL,
  `payload` JSON NULL,
  `matched_subscription_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `customer_first_name` VARCHAR(100) NULL DEFAULT NULL,
  `customer_last_name` VARCHAR(100) NULL DEFAULT NULL,
  `customer_email` VARCHAR(150) NULL DEFAULT NULL,
  `description` VARCHAR(500) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `webhook_events_notification_id_unique` (`notification_id`),
  KEY `webhook_events_event_type_index` (`event_type`),
  KEY `webhook_events_invoice_number_index` (`invoice_number`),
  KEY `webhook_events_matched_subscription_id_index` (`matched_subscription_id`),
  KEY `webhook_events_received_at_index` (`received_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────────────
-- eBooks: catalog + orders
-- ────────────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `ebooks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(80) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `subtitle` VARCHAR(255) NULL DEFAULT NULL,
  `price` DECIMAL(8,2) NOT NULL,
  `cover_image` VARCHAR(255) NULL DEFAULT NULL,
  `drive_link` TEXT NULL,
  `features` JSON NULL,
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `ebooks_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ebook_orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `ebook_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `ebook_slug` VARCHAR(80) NOT NULL,
  `ebook_title` VARCHAR(200) NOT NULL,
  `amount` DECIMAL(8,2) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NULL DEFAULT NULL,
  `address` VARCHAR(255) NULL DEFAULT NULL,
  `city` VARCHAR(100) NULL DEFAULT NULL,
  `state` VARCHAR(10) NULL DEFAULT NULL,
  `zip` VARCHAR(20) NULL DEFAULT NULL,
  `invoice_number` VARCHAR(64) NOT NULL,
  `transaction_id` VARCHAR(64) NULL DEFAULT NULL,
  `auth_code` VARCHAR(32) NULL DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'paid',
  `marketing_opt_in` TINYINT(1) NOT NULL DEFAULT 0,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` VARCHAR(255) NULL DEFAULT NULL,
  `charged_at` TIMESTAMP NULL DEFAULT NULL,
  `downloaded_at` TIMESTAMP NULL DEFAULT NULL,
  `download_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `raw_payload` JSON NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `ebook_orders_invoice_number_unique` (`invoice_number`),
  KEY `ebook_orders_ebook_id_foreign` (`ebook_id`),
  KEY `ebook_orders_email_index` (`email`),
  KEY `ebook_orders_ebook_slug_index` (`ebook_slug`),
  KEY `ebook_orders_charged_at_index` (`charged_at`),
  KEY `ebook_orders_status_index` (`status`),
  CONSTRAINT `ebook_orders_ebook_id_foreign`
    FOREIGN KEY (`ebook_id`) REFERENCES `ebooks`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────────────
-- Seed: 4 starter eBooks (matches database/migrations/2026_05_14_120000_*.php)
-- ────────────────────────────────────────────────────────────────────────────

INSERT IGNORE INTO `ebooks`
    (`slug`, `title`, `subtitle`, `price`, `cover_image`, `drive_link`, `features`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
    (
        '100k-funding-in-90-days',
        'Steps to Get $100K+ in 90 Days',
        'The exact playbook clients use to unlock 6-figure business funding fast.',
        47.00,
        'images/100kinfundingebookcover.png',
        NULL,
        JSON_ARRAY(
            'The 90-day funding stack — step by step',
            'How to position your profile for lender approval',
            'Common mistakes that get applications denied',
            'Lifetime updates as the lender list evolves'
        ),
        1, 1, NOW(), NOW()
    ),
    (
        'hard-inquiries-gone',
        'Get Hard Inquiries Gone',
        'Remove hard inquiries from your credit report in as little as one day.',
        7.47,
        'images/hardinquiriesebookcover.png',
        NULL,
        JSON_ARRAY(
            'The 1-day inquiry removal method',
            'Letter templates you can send today',
            'When to dispute vs. when to call directly',
            'Avoid the mistakes that re-add inquiries'
        ),
        2, 1, NOW(), NOW()
    ),
    (
        'real-estate-terms-cheat-sheet',
        'Real Estate Terms Exam Cheat Sheet',
        'Pass the real estate licensing exam — terms decoded, no fluff.',
        19.47,
        'images/realestatetermscheatsheetebookcover.png',
        NULL,
        JSON_ARRAY(
            'Every key term you need on exam day',
            'Plain-English definitions, fast to memorize',
            'Common exam traps and how to spot them',
            'Quick-recall mnemonics included'
        ),
        3, 1, NOW(), NOW()
    ),
    (
        'realtor-roadmap-to-success',
        'The Realtor Roadmap to Success',
        'The first-90-days playbook for new real estate agents who want to win.',
        24.47,
        'images/realtorroadmaptosuccessebookcover.png',
        NULL,
        JSON_ARRAY(
            'Your 90-day launch plan as a new agent',
            'How to fill your pipeline without ad spend',
            'Scripts for buyer + listing conversations',
            'Build a 6-figure book of business'
        ),
        4, 1, NOW(), NOW()
    );

-- ────────────────────────────────────────────────────────────────────────────
-- Mark all 14 Laravel migrations as executed so `php artisan migrate`
-- becomes a no-op on this database.
-- ────────────────────────────────────────────────────────────────────────────

INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
    ('0001_01_01_000000_create_users_table',                      1),
    ('0001_01_01_000001_create_cache_table',                      1),
    ('0001_01_01_000002_create_jobs_table',                       1),
    ('2026_05_11_210210_create_contacts_table',                   1),
    ('2026_05_11_210210_create_leads_table',                      1),
    ('2026_05_11_210210_create_onboarding_submissions_table',     1),
    ('2026_05_11_211310_create_funding_applications_table',       1),
    ('2026_05_12_101518_create_mentorship_leads_table',           1),
    ('2026_05_13_120000_create_subscriptions_table',              1),
    ('2026_05_13_120001_create_payments_table',                   1),
    ('2026_05_13_120002_create_subscription_events_table',        1),
    ('2026_05_13_120003_create_webhook_events_table',             1),
    ('2026_05_14_120000_create_ebooks_table',                     1),
    ('2026_05_14_120001_create_ebook_orders_table',               1);

SET FOREIGN_KEY_CHECKS = 1;

-- ────────────────────────────────────────────────────────────────────────────
-- After running this file, create your first admin user:
--
--   1.  ssh into the server and `cd` into the project root
--   2.  php artisan tinker
--   3.  >>> \App\Models\User::create([
--           'name'     => 'Admin',
--           'email'    => 'admin@victoriousopportunities.com',
--           'password' => bcrypt('CHANGE-ME-STRONG-PASSWORD'),
--       ]);
--
--   Or run the equivalent SQL with a bcrypt hash you generated elsewhere:
--     INSERT INTO `users` (name, email, password, created_at, updated_at)
--     VALUES ('Admin', 'admin@victoriousopportunities.com',
--             '$2y$12$replace.with.real.bcrypt.hash...........', NOW(), NOW());
-- ────────────────────────────────────────────────────────────────────────────

-- ────────────────────────────────────────────────────────────────────────────
-- Verification queries (run after the above to confirm everything exists):
--
--   SHOW TABLES;
--   -- expect 20 rows including: users, sessions, password_reset_tokens,
--   -- cache, cache_locks, jobs, job_batches, failed_jobs, migrations,
--   -- contacts, leads, onboarding_submissions, funding_applications,
--   -- mentorship_leads, subscriptions, payments, subscription_events,
--   -- webhook_events, ebooks, ebook_orders
--
--   SELECT COUNT(*) FROM `ebooks`;       -- expect 4
--   SELECT COUNT(*) FROM `migrations`;   -- expect 14
-- ────────────────────────────────────────────────────────────────────────────
