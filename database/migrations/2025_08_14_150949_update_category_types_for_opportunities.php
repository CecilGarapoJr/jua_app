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
        // Define the mapping of old category types to new opportunity-related types
        $categoryTypeMapping = [
            'service' => 'opportunity_service',
            'job_category' => 'opportunity_category',
            'job_tag' => 'opportunity_tag',
        ];
        
        // Update existing categories with new types
        foreach ($categoryTypeMapping as $oldType => $newType) {
            DB::table('categories')
                ->where('type', $oldType)
                ->update(['type' => $newType]);
        }
        
        // Add new opportunity-specific category types if needed
        // These will be empty initially and populated through the admin interface
        $newCategoryTypes = [
            // Scholarship-specific categories
            ['title' => 'Academic Scholarships', 'slug' => 'academic-scholarships', 'type' => 'opportunity_category', 'status' => 1],
            ['title' => 'Need-Based Scholarships', 'slug' => 'need-based-scholarships', 'type' => 'opportunity_category', 'status' => 1],
            
            // Grant-specific categories
            ['title' => 'Research Grants', 'slug' => 'research-grants', 'type' => 'opportunity_category', 'status' => 1],
            ['title' => 'Business Grants', 'slug' => 'business-grants', 'type' => 'opportunity_category', 'status' => 1],
            
            // Training-specific categories
            ['title' => 'Professional Development', 'slug' => 'professional-development', 'type' => 'opportunity_category', 'status' => 1],
            ['title' => 'Technical Training', 'slug' => 'technical-training', 'type' => 'opportunity_category', 'status' => 1],
        ];
        
        // Insert new categories
        foreach ($newCategoryTypes as $category) {
            // Check if the category already exists to avoid duplicates
            $exists = DB::table('categories')
                ->where('slug', $category['slug'])
                ->exists();
                
            if (!$exists) {
                DB::table('categories')->insert($category);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the category type changes
        $categoryTypeMapping = [
            'opportunity_service' => 'service',
            'opportunity_category' => 'job_category',
            'opportunity_tag' => 'job_tag',
        ];
        
        // Update categories back to original types
        foreach ($categoryTypeMapping as $newType => $oldType) {
            DB::table('categories')
                ->where('type', $newType)
                ->update(['type' => $oldType]);
        }
        
        // Remove the new opportunity-specific categories
        $opportunitySlugs = [
            'academic-scholarships',
            'need-based-scholarships',
            'research-grants',
            'business-grants',
            'professional-development',
            'technical-training',
        ];
        
        DB::table('categories')
            ->whereIn('slug', $opportunitySlugs)
            ->delete();
    }
};
