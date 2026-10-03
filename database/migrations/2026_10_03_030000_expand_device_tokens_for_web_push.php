<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('device_tokens')) {
            return;
        }

        if (!Schema::hasColumn('device_tokens', 'token_hash')) {
            Schema::table('device_tokens', function (Blueprint $table) {
                $table
                    ->char('token_hash', 64)
                    ->nullable()
                    ->after('token');
            });
        }

        DB::table('device_tokens')
            ->whereNull('token_hash')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('device_tokens')
                        ->where('id', $row->id)
                        ->update([
                            'token_hash' => hash(
                                'sha256',
                                (string) $row->token
                            ),
                        ]);
                }
            });

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $indexes = DB::select(
                "SHOW INDEX FROM device_tokens WHERE Key_name = 'device_tokens_token_unique'"
            );

            if (!empty($indexes)) {
                DB::statement(
                    'ALTER TABLE device_tokens DROP INDEX device_tokens_token_unique'
                );
            }

            DB::statement(
                'ALTER TABLE device_tokens MODIFY token TEXT NOT NULL'
            );
        }

        if (!$this->hasIndex('device_tokens', 'device_tokens_token_hash_unique')) {
            Schema::table('device_tokens', function (Blueprint $table) {
                $table
                    ->unique(
                        'token_hash',
                        'device_tokens_token_hash_unique'
                    );
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('device_tokens')) {
            return;
        }

        if ($this->hasIndex('device_tokens', 'device_tokens_token_hash_unique')) {
            Schema::table('device_tokens', function (Blueprint $table) {
                $table->dropUnique('device_tokens_token_hash_unique');
            });
        }

        if (Schema::hasColumn('device_tokens', 'token_hash')) {
            Schema::table('device_tokens', function (Blueprint $table) {
                $table->dropColumn('token_hash');
            });
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            return !empty(
                DB::select(
                    "SHOW INDEX FROM {$table} WHERE Key_name = ?",
                    [$indexName]
                )
            );
        }

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$table}')");

            foreach ($indexes as $index) {
                if (($index->name ?? null) === $indexName) {
                    return true;
                }
            }
        }

        return false;
    }
};
