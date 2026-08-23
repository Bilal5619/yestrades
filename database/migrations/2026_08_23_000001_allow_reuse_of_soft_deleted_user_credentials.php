<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    if (!Schema::hasTable('users')) {
      return;
    }

    $this->dropIndexIfExists('users', 'users_email_unique');
    $this->dropIndexIfExists('users', 'users_username_unique');

    if (!Schema::hasColumn('users', 'active_email')) {
      DB::statement("ALTER TABLE users ADD active_email VARCHAR(255) GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN email ELSE NULL END) STORED");
    }

    if (!Schema::hasColumn('users', 'active_username')) {
      DB::statement("ALTER TABLE users ADD active_username VARCHAR(255) GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN username ELSE NULL END) STORED");
    }

    $this->createUniqueIndexIfMissing('users', 'users_active_email_unique', 'active_email');
    $this->createUniqueIndexIfMissing('users', 'users_active_username_unique', 'active_username');
  }

  public function down(): void
  {
    if (!Schema::hasTable('users')) {
      return;
    }

    $this->dropIndexIfExists('users', 'users_active_email_unique');
    $this->dropIndexIfExists('users', 'users_active_username_unique');

    if (Schema::hasColumn('users', 'active_email')) {
      DB::statement('ALTER TABLE users DROP COLUMN active_email');
    }

    if (Schema::hasColumn('users', 'active_username')) {
      DB::statement('ALTER TABLE users DROP COLUMN active_username');
    }

    $this->createUniqueIndexIfMissing('users', 'users_email_unique', 'email');
    $this->createUniqueIndexIfMissing('users', 'users_username_unique', 'username');
  }

  private function dropIndexIfExists(string $table, string $index): void
  {
    if ($this->indexExists($table, $index)) {
      DB::statement("ALTER TABLE {$table} DROP INDEX {$index}");
    }
  }

  private function createUniqueIndexIfMissing(string $table, string $index, string $column): void
  {
    if (!$this->indexExists($table, $index)) {
      DB::statement("CREATE UNIQUE INDEX {$index} ON {$table} ({$column})");
    }
  }

  private function indexExists(string $table, string $index): bool
  {
    $database = DB::getDatabaseName();

    return DB::table('information_schema.statistics')
      ->where('table_schema', $database)
      ->where('table_name', $table)
      ->where('index_name', $index)
      ->exists();
  }
};
