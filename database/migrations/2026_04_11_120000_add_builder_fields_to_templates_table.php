<?php

use App\Models\Template;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('render_mode', 20)->default(Template::RENDER_MODE_BLADE)->after('html_path');
            $table->json('builder_config')->nullable()->after('render_mode');
            $table->string('builder_layout')->nullable()->after('builder_config');
            $table->unsignedInteger('schema_version')->default(1)->after('builder_layout');
        });

        DB::table('templates')
            ->whereNull('render_mode')
            ->update([
                'render_mode' => Template::RENDER_MODE_BLADE,
                'schema_version' => 1,
            ]);
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn([
                'render_mode',
                'builder_config',
                'builder_layout',
                'schema_version',
            ]);
        });
    }
};
