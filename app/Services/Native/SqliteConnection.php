<?php

namespace App\Services\Native;

use PDO;

class SqliteConnection
{
    public static function pdo(?string $path = null): PDO
    {
        $path ??= self::path();
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $pdo = new PDO('sqlite:'.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('PRAGMA journal_mode=WAL; PRAGMA synchronous=NORMAL; PRAGMA foreign_keys=ON;');

        return $pdo;
    }

    public static function path(?string $override = null): string
    {
        if ($override) {
            return $override;
        }
        $base = env('NATIVE_SQLITE_PATH');
        if ($base) {
            return $base;
        }

        return storage_path('native/kasiva-offline.sqlite');
    }

    public static function migrate(?string $path = null): void
    {
        $pdo = self::pdo($path);
        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS catalog (
                id TEXT PRIMARY KEY, name TEXT, sku TEXT, price REAL, hpp REAL,
                current_stock REAL, is_active INTEGER, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS carts (
                id TEXT PRIMARY KEY, payload TEXT NOT NULL, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS members (
                id TEXT PRIMARY KEY, name TEXT, phone TEXT, qr_code TEXT, status TEXT,
                email TEXT, stamps_count INTEGER DEFAULT 0, total_visits INTEGER DEFAULT 0, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS loyalty_snapshot (
                id TEXT PRIMARY KEY, payload TEXT NOT NULL, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS transactions_local (
                id TEXT PRIMARY KEY, receipt_number TEXT UNIQUE, payment_method TEXT,
                total_amount REAL, total_hpp REAL, paid_amount REAL, change_amount REAL,
                loyalty_member_id TEXT, sync_status TEXT, payment_confirmed_manually INTEGER,
                cashier_name TEXT, payload TEXT, created_at TEXT
            );
            CREATE TABLE IF NOT EXISTS transaction_items_local (
                id TEXT PRIMARY KEY, transaction_id TEXT, product_id TEXT, product_name TEXT,
                unit_price REAL, unit_hpp REAL, quantity INTEGER, subtotal REAL
            );
            CREATE TABLE IF NOT EXISTS expenses_local (
                id TEXT PRIMARY KEY, title TEXT, amount REAL, category TEXT,
                expense_date TEXT, notes TEXT, sync_status TEXT, created_at TEXT, payload TEXT
            );
            CREATE TABLE IF NOT EXISTS pending_operations (
                id TEXT PRIMARY KEY, type TEXT, payload TEXT, status TEXT,
                last_error TEXT, attempts INTEGER DEFAULT 0, created_at TEXT, synced_at TEXT
            );
            CREATE TABLE IF NOT EXISTS sync_meta (
                id TEXT PRIMARY KEY, value TEXT, updated_at TEXT
            );
            CREATE INDEX IF NOT EXISTS idx_catalog_sku ON catalog(sku);
            CREATE INDEX IF NOT EXISTS idx_members_qr ON members(qr_code);
            CREATE INDEX IF NOT EXISTS idx_members_phone ON members(phone);
            CREATE INDEX IF NOT EXISTS idx_pending_status ON pending_operations(status);
            SQL);
    }
}
