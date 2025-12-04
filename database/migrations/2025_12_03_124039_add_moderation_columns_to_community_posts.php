<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('community_posts', 'is_approved')) {
                $table->boolean('is_approved')->default(true)->after('is_pinned');
            }
            if (!Schema::hasColumn('community_posts', 'moderated_by')) {
                $table->foreignId('moderated_by')->nullable()->after('is_approved')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('community_posts', 'moderated_at')) {
                $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            }
            if (!Schema::hasColumn('community_posts', 'moderation_notes')) {
                $table->text('moderation_notes')->nullable()->after('moderated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropColumn(['is_approved', 'moderated_by', 'moderated_at', 'moderation_notes']);
        });
    }
};