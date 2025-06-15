<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::rename('facility_category', 'facility_categories');
    }

    public function down()
    {
        Schema::rename('facility_categories', 'facility_category');
    }
};
