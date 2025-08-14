<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    
    /**
     * Category type constants for the opportunity platform
     */
    const TYPE_OPPORTUNITY_SERVICE = 'opportunity_service';
    const TYPE_OPPORTUNITY_CATEGORY = 'opportunity_category';
    const TYPE_OPPORTUNITY_TAG = 'opportunity_tag';
    
    /**
     * Legacy category type constants (for backward compatibility)
     */
    const TYPE_SERVICE = 'service';
    const TYPE_JOB_CATEGORY = 'job_category';
    const TYPE_JOB_TAG = 'job_tag';
    const TYPE_BLOG_CATEGORY = 'blog_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'status',
        'type',
        'is_featured',
        'category_id',
        'is_menu_item',
        'lang',
        'icon',
        'preview',
    ];

    //
    public function categories()
    {
        return $this->hasMany(Category::class, 'category_id', 'id');
    }

    //parent category
    public function parent()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    //nasted
    public function childrenCategories()
    {
        return $this->hasMany(Category::class, 'category_id', 'id')->with('categories');
    }

    public function postcategory()
    {
        return $this->belongsTo(Postcategory::class);
    }

    public function postcategories()
    {
        return $this->hasMany(Postcategory::class);
    }

    public function posts()
    {
        return $this->belongsToMany('App\Models\Post', 'postcategories', 'post_id', 'category_id');
    }

    public function companies()
    {
        return $this->hasMany(User::class, 'category_id', 'id');
    }

    public function scopeActive($query): Builder
    {
        return $query->where('status', 1);
    }

    public function jobs()
    {
        return $this->belongsToMany(Opening::class, 'category_opening', 'category_id', 'opening_id');
    }
    
    /**
     * Get opportunities associated with this category
     * This is an alias for jobs() to maintain backward compatibility
     */
    public function opportunities()
    {
        return $this->jobs();
    }
    
    /**
     * Scope a query to only include opportunity services
     */
    public function scopeOpportunityServices($query)
    {
        return $query->where('type', self::TYPE_OPPORTUNITY_SERVICE);
    }
    
    /**
     * Scope a query to only include opportunity categories
     */
    public function scopeOpportunityCategories($query)
    {
        return $query->where('type', self::TYPE_OPPORTUNITY_CATEGORY);
    }
    
    /**
     * Scope a query to only include opportunity tags
     */
    public function scopeOpportunityTags($query)
    {
        return $query->where('type', self::TYPE_OPPORTUNITY_TAG);
    }
    
    /**
     * Check if this category is an opportunity service
     */
    public function isOpportunityService(): bool
    {
        return $this->type === self::TYPE_OPPORTUNITY_SERVICE;
    }
    
    /**
     * Check if this category is an opportunity category
     */
    public function isOpportunityCategory(): bool
    {
        return $this->type === self::TYPE_OPPORTUNITY_CATEGORY;
    }
    
    /**
     * Check if this category is an opportunity tag
     */
    public function isOpportunityTag(): bool
    {
        return $this->type === self::TYPE_OPPORTUNITY_TAG;
    }
}
