-- ============================================================================
-- VictoriaLove — Create / reset the admin login on the LIVE database.
--
-- Email:    victoria@creditrepair.com
-- Password: #gvH&SQA2NAg2x7
--
-- The password stored below is the bcrypt hash of the plaintext password
-- (cost 12, matching BCRYPT_ROUNDS=12 in .env). The plaintext is NEVER
-- stored in the DB.
--
-- HOW TO RUN ON THE LIVE SERVER:
--   mysql -u victoriauser -p victoria < setup-admin.sql
--
-- OR paste the INSERT below into phpMyAdmin → SQL tab on the live DB.
--
-- This file is idempotent: re-running it just updates the password to the
-- same hash, so it's safe to run as many times as you need.
-- ============================================================================

USE `victoria`;

INSERT INTO `users` (`name`, `email`, `password`, `created_at`, `updated_at`)
VALUES (
    'Victoria',
    'victoria@creditrepair.com',
    '$2y$12$AHi34LG0UW6ZxBbVWygE1.rGFPb/NUx7dkWRBMmvNT2l.8xi8vlsW',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    `password`   = VALUES(`password`),
    `updated_at` = NOW();

-- Verify it exists:
SELECT id, name, email, created_at, updated_at FROM `users` WHERE email = 'victoria@creditrepair.com';
