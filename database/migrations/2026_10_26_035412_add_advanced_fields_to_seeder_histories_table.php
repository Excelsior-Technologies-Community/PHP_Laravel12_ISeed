<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seeder_histories', function (Blueprint $table) {
            $table->string('batch_id')->nullable()->after('id');
            $table->string('file_path')->nullable()->after('seeder_name');
            $table->decimal('file_size_kb', 8, 2)->default(0)->after('records');
            $table->boolean('masking_applied')->default(false)->after('status');
            $table->integer('chunk_size')->nullable()->after('masking_applied');
            $table->json('options')->nullable()->after('chunk_size');
            $table->longText('previous_code')->nullable()->after('options');
            $table->integer('execution_time_ms')->nullable()->after('previous_code');
        });
    }

    public function down(): void
    {
        Schema::table('seeder_histories', function (Blueprint $table) {
            $table->dropColumn([
                'batch_id',
                'file_path',
                'file_size_kb',
                'masking_applied',
                'chunk_size',
                'options',
                'previous_code',
                'execution_time_ms',
            ]);
        });
    }
};
