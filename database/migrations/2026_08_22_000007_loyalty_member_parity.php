<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // loyalty_members — tambah kolom parity Ngepos
        Schema::table('loyalty_members', function (Blueprint $t) {
            if (! Schema::hasColumn('loyalty_members', 'qr_code')) {
                $t->string('qr_code')->nullable()->unique()->after('phone');
            }
            if (! Schema::hasColumn('loyalty_members', 'status')) {
                $t->string('status')->default('ASSIGNED')->after('qr_code');
            }
            if (! Schema::hasColumn('loyalty_members', 'email')) {
                $t->string('email')->nullable()->after('status');
            }
            if (! Schema::hasColumn('loyalty_members', 'notes')) {
                $t->text('notes')->nullable()->after('email');
            }
            if (! Schema::hasColumn('loyalty_members', 'assigned_at')) {
                $t->dateTime('assigned_at')->nullable()->after('notes');
            }
        });

        // SQLite tidak mendukung ALTER COLUMN; rebuild hanya pada koneksi SQLite.
        // Menjalankan query sqlite_master di PostgreSQL akan meng-abort transaction migration.
        if (DB::getDriverName() === 'sqlite') {
            $info = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='loyalty_members'");
            $sql = $info[0]->sql ?? '';
            $needsRebuild = str_contains($sql, '"name" varchar not null') || str_contains($sql, '\"name\" varchar not null');
            if ($needsRebuild || str_contains($sql, '"phone" varchar not null') || str_contains($sql, '\"phone\" varchar not null')) {
                Schema::create('loyalty_members_new', function (Blueprint $t) {
                    $t->uuid('id')->primary();
                    $t->string('name')->nullable();
                    $t->string('phone')->nullable()->unique();
                    $t->integer('stamps_count')->default(0);
                    $t->integer('total_visits')->default(0);
                    $t->timestamps();
                    $t->string('qr_code')->nullable()->unique();
                    $t->string('status')->default('ASSIGNED');
                    $t->string('email')->nullable();
                    $t->text('notes')->nullable();
                    $t->dateTime('assigned_at')->nullable();
                });
                DB::statement('INSERT INTO loyalty_members_new (id, name, phone, stamps_count, total_visits, created_at, updated_at, qr_code, status, email, notes, assigned_at) SELECT id, name, phone, stamps_count, total_visits, created_at, updated_at, qr_code, status, email, notes, assigned_at FROM loyalty_members');
                Schema::drop('loyalty_members');
                Schema::rename('loyalty_members_new', 'loyalty_members');
            }
        }

        // backfill qr_code untuk row existing jika masih null
        try {
            $members = DB::table('loyalty_members')->whereNull('qr_code')->get();
            foreach ($members as $m) {
                $code = 'KSV-MBR-'.strtoupper(Str::random(8));
                // ensure unique
                while (DB::table('loyalty_members')->where('qr_code', $code)->exists()) {
                    $code = 'KSV-MBR-'.strtoupper(Str::random(8));
                }
                DB::table('loyalty_members')->where('id', $m->id)->update([
                    'qr_code' => $code,
                    'status' => $m->name ? 'ASSIGNED' : 'UNASSIGNED',
                    'assigned_at' => $m->name ? ($m->created_at ?? now()) : null,
                ]);
            }
        } catch (Throwable $e) {
        }

        // loyalty_stamps — tambah program_id + stamped_at
        Schema::table('loyalty_stamps', function (Blueprint $t) {
            if (! Schema::hasColumn('loyalty_stamps', 'program_id')) {
                $t->foreignUuid('program_id')->nullable()->after('loyalty_member_id')->constrained('loyalty_programs')->nullOnDelete();
            }
            if (! Schema::hasColumn('loyalty_stamps', 'stamped_at')) {
                $t->dateTime('stamped_at')->nullable()->after('stamps_earned');
            }
        });

        // backfill stamped_at dari created_at
        try {
            DB::statement('UPDATE loyalty_stamps SET stamped_at = created_at WHERE stamped_at IS NULL');
        } catch (Throwable $e) {
        }

        // customer_rewards — tabel per-customer reward (AVAILABLE/CLAIMED/EXPIRED) seperti Ngepos customerRewards
        if (! Schema::hasTable('customer_rewards')) {
            Schema::create('customer_rewards', function (Blueprint $t) {
                $t->uuid('id')->primary();
                $t->foreignUuid('loyalty_member_id')->constrained('loyalty_members')->cascadeOnDelete();
                $t->foreignUuid('program_id')->constrained('loyalty_programs')->cascadeOnDelete();
                $t->string('status')->default('AVAILABLE'); // AVAILABLE, CLAIMED, EXPIRED
                $t->dateTime('available_at');
                $t->dateTime('expires_at');
                $t->dateTime('claimed_at')->nullable();
                $t->foreignUuid('claimed_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customer_rewards')) {
            Schema::dropIfExists('customer_rewards');
        }
        Schema::table('loyalty_stamps', function (Blueprint $t) {
            if (Schema::hasColumn('loyalty_stamps', 'claimed_transaction_id')) {
                try {
                    $t->dropForeign(['claimed_transaction_id']);
                } catch (Throwable $e) {
                }
            }
            if (Schema::hasColumn('loyalty_stamps', 'program_id')) {
                try {
                    $t->dropForeign(['program_id']);
                } catch (Throwable $e) {
                }
                $t->dropColumn('program_id');
            }
            if (Schema::hasColumn('loyalty_stamps', 'stamped_at')) {
                $t->dropColumn('stamped_at');
            }
        });
        Schema::table('loyalty_members', function (Blueprint $t) {
            foreach (['assigned_at', 'notes', 'email', 'status', 'qr_code'] as $col) {
                if (Schema::hasColumn('loyalty_members', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
