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
        // Jika tabel users sudah ada, kita modifikasi
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Tambahkan kolom yang mungkin belum ada
                if (!Schema::hasColumn('users', 'email_verified_at')) {
                    $table->timestamp('email_verified_at')->nullable()->after('email');
                }
                
                // Pastikan kolom role menggunakan enum yang benar
                $table->enum('role', ['admin', 'technician', 'user', 'headmaster'])
                      ->default('user')
                      ->change();
                
                // Pastikan kolom is_active ada
                if (!Schema::hasColumn('users', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('role');
                }
                
                // Pastikan kolom phone ada
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone', 20)->nullable()->after('role');
                }
                
                // Tambahkan soft deletes jika belum ada
                if (!Schema::hasColumn('users', 'deleted_at')) {
                    $table->softDeletes();
                }
                
                // Remove kolom yang tidak diperlukan lagi
                if (Schema::hasColumn('users', 'email_verification_token')) {
                    $table->dropColumn('email_verification_token');
                }
                
                // Pastikan timestamps menggunakan Laravel default
                $table->timestamps();
            });
        } else {
            // Jika tabel users belum ada, buat baru
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->enum('role', ['admin', 'technician', 'user', 'headmaster'])->default('user');
                $table->string('phone', 20)->nullable();
                $table->boolean('is_active')->default(true);
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan perubahan jika perlu
            $table->dropSoftDeletes();
        });
    }
};