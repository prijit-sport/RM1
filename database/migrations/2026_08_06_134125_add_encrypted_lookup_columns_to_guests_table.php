<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            // Ciphertext columns (encrypted values using Laravel encrypt())
            $table->text('email_ciphertext')->nullable()->after('email');
            $table->text('id_number_ciphertext')->nullable()->after('id_number');

            // Blind index hash columns for exact-match lookup (unique)
            $table->string('email_hash', 64)->nullable()->after('email_ciphertext');
            $table->string('id_number_hash', 64)->nullable()->after('id_number_ciphertext');

            // Unique indexes for the hash lookup columns
            $table->unique('email_hash');
            $table->unique('id_number_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropUnique(['email_hash']);
            $table->dropUnique(['id_number_hash']);

            $table->dropColumn([
                'email_hash',
                'id_number_hash',
                'id_number_ciphertext',
                'email_ciphertext',
            ]);
        });
    }
};
