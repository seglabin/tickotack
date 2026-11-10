<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('conteneurs', 'idetat')) {
            return;
        }

        Schema::table('conteneurs', function (Blueprint $table) {
            $table->unsignedBigInteger('idetat')->nullable()->after('poidstotal');
            $table->foreign('idetat')
                ->references('id')
                ->on('elements')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('conteneurs', 'idetat')) {
            return;
        }

        Schema::table('conteneurs', function (Blueprint $table) {
            $table->dropForeign(['idetat']);
            $table->dropColumn('idetat');
        });
    }
};
