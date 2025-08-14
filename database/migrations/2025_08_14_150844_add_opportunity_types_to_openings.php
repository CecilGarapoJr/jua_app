<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // We don't need to modify the schema as the 'type' field already exists
        // Instead, we'll update existing job types in the database to the new opportunity types
        
        // First, let's create a mapping of old job types to new opportunity types
        $typeMapping = [
            'Full Time' => 'job_full_time',
            'Part Time' => 'job_part_time',
            'Hourly-Contract' => 'job_hourly_contract',
            'Fixed-Price' => 'job_fixed_price',
        ];
        
        // Update existing records
        foreach ($typeMapping as $oldType => $newType) {
            DB::table('openings')
                ->where('type', $oldType)
                ->update(['type' => $newType]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the type changes
        $typeMapping = [
            'job_full_time' => 'Full Time',
            'job_part_time' => 'Part Time',
            'job_hourly_contract' => 'Hourly-Contract',
            'job_fixed_price' => 'Fixed-Price',
        ];
        
        // Update records back to original types
        foreach ($typeMapping as $newType => $oldType) {
            DB::table('openings')
                ->where('type', $newType)
                ->update(['type' => $oldType]);
        }
    }
};
