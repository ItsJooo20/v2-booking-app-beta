<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        // Users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'technician', 'user', 'headmaster'])->default('user');
            $table->string('phone', 20)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });

        // Facility Categories
        Schema::create('facility_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->text('description')->nullable();
            $table->boolean('requires_return')->default(false);
            $table->boolean('return_photo_required')->default(false);
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // Facilities
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('can_be_addon')->default(false);
            $table->boolean('can_have_addon')->default(false);
            $table->foreignId('category_id')->constrained('facility_categories');
            $table->integer('total_items')->default(0);
            $table->integer('available_items')->default(0);
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // Facility Items
        Schema::create('facility_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities');
            $table->string('item_code', 50)->unique()->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_late')->default(false);
            $table->string('condition_status')->nullable();
            $table->timestamps();
        });

        // Facility Item Images
        Schema::create('facility_item_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_item_id')->constrained('facility_items');
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        // Bookings
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('facility_item_id')->constrained('facility_items');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('purpose');
            $table->enum('status', ['pending', 'return submitted', 'approved', 'rejected', 'completed', 'cancelled', 'needs return'])->default('pending');
            $table->timestamps();

            // Indexes
            $table->index(['start_datetime', 'end_datetime']);
            $table->index('status', 'idx_bookings_status');
            $table->index('user_id', 'idx_bookings_user');
        });

        // Booking Equipment Requests
        Schema::create('booking_equipment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('facility_item_id')->constrained('facility_items');
            $table->enum('status', ['pending', 'return submitted', 'approved', 'rejected', 'completed', 'cancelled', 'needs return'])->default('pending');
            $table->timestamps();
        });

        // Equipment Returns
        Schema::create('equipment_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings');
            $table->dateTime('return_date');
            $table->string('return_photo_path')->nullable();
            $table->enum('condition_status', ['pending', 'normal', 'damaged', 'missing'])->default('normal');
            $table->string('user_condition');
            $table->text('notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
        });

        // Damage Reports
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users');
            $table->foreignId('facility_item_id')->constrained('facility_items');
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['reported', 'under_review', 'in_progress', 'resolved', 'wont_fix'])->default('reported');
            $table->string('image_path')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status', 'idx_damage_reports_status');
            $table->index('facility_item_id', 'idx_damage_reports_item');
        });

        // Repair Tasks
        Schema::create('repair_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('damage_report_id')->constrained('damage_reports');
            $table->foreignId('technician_id')->constrained('users');
            $table->text('description');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('repair_notes')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // Password Resets
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email', 50)->primary();
            $table->string('token', 100);
            $table->timestamp('created_at')->nullable();
        });

        // Failed Jobs
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Jobs
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue');
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
            
            $table->index(['queue']);
        });

        // Job Batches
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        // Cache
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // Cache Locks
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // Personal Access Tokens
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to avoid foreign key constraints
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('repair_tasks');
        Schema::dropIfExists('damage_reports');
        Schema::dropIfExists('equipment_returns');
        Schema::dropIfExists('booking_equipment_requests');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('facility_item_images');
        Schema::dropIfExists('facility_items');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('facility_categories');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('users');
    }
};