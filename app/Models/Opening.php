<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opening extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'slug',
        'description',
        'short_description',
        'type',
        'category_id',
        'salary_type',
        'salary_range',
        'experience',
        'expertise',
        'featured_expire_at',
        'attachment',
        'address',
        'status',
        'apply_type',
        'meta',
        'fields',
        'expired_at',
        'live_expire_at',
        'currency',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'fields' => 'json',
        
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_bookmarked', 'is_expired'];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    const JOB_TYPES = [
        'Full Time',
        'Part Time',
        'Hourly-Contract',
        'Fixed-Price',
    ];
    
    const OPPORTUNITY_TYPES = [
        // Job types
        'job_full_time' => 'Full Time Job',
        'job_part_time' => 'Part Time Job',
        'job_hourly_contract' => 'Hourly Contract Job',
        'job_fixed_price' => 'Fixed Price Job',
        
        // Scholarship types
        'scholarship_full' => 'Full Scholarship',
        'scholarship_partial' => 'Partial Scholarship',
        'scholarship_merit' => 'Merit Scholarship',
        
        // Grant types
        'grant_research' => 'Research Grant',
        'grant_project' => 'Project Grant',
        'grant_business' => 'Business Grant',
        
        // Training types
        'training_course' => 'Training Course',
        'training_workshop' => 'Workshop',
        'training_certification' => 'Certification Program',
        
        // Internship types
        'internship_paid' => 'Paid Internship',
        'internship_unpaid' => 'Unpaid Internship',
        'internship_academic' => 'Academic Internship',
    ];
    
    const OPPORTUNITY_CATEGORIES = [
        'job' => ['job_full_time', 'job_part_time', 'job_hourly_contract', 'job_fixed_price'],
        'scholarship' => ['scholarship_full', 'scholarship_partial', 'scholarship_merit'],
        'grant' => ['grant_research', 'grant_project', 'grant_business'],
        'training' => ['training_course', 'training_workshop', 'training_certification'],
        'internship' => ['internship_paid', 'internship_unpaid', 'internship_academic'],
    ];

    // accessor
    public function getIsBookmarkedAttribute($value): bool
    {
        if (auth()->check()) {
            /**
             * @var \App\Models\User
             */
            $user = auth()->user();
            return (bool) $user->jobBookmarks->where('id', $this->id)->count();
        }
        return false;
    }

    public function getCreatedAtDateAttribute()
    {
        return $this->created_at?->format('d F Y');
    }

    public function getIsExpiredAttribute($value): bool
    {
        $expired_at = $this->expired_at;

        if (!$expired_at) {
            return true;
        }

        return Carbon::make($this->expired_at) < now();
    }

    
    // scopes
    public function scopeActive($query): Builder
    {
        return $query
            ->where('status', 1);
    }

    public function scopeInActive($query): Builder
    {
        return $query
            ->where('status', '!=', 1)
            ->where('expired_at', '>', today());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function service()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_opening', 'opening_id', 'category_id')
            ->where('type', 'job_category');
    }
    public function tags()
    {
        return $this->belongsToMany(Category::class, 'category_opening', 'opening_id', 'category_id')
            ->where('type', 'job_tag');
    }

    public function categoryopening() {
        return $this->hasMany(Categoryopening::class);
    }

    public function country()
    {
        return $this->belongsToMany(Location::class, 'location_opening', 'opening_id', 'country_id')
            ->whereNull('location_id');
    }

    public function state()
    {
        return $this->belongsToMany(Location::class, 'location_opening', 'opening_id', 'state_id')
            ->whereNotNull('location_id');
    }
    public function visits()
    {
        return $this->hasMany(Visitor::class);
    }

    public function appliedApplicants()
    {
        return $this->belongsToMany(User::class, 'userjobs', 'opening_id', 'user_id')->withPivot(['meta', 'is_hired', 'seen_at']);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CompanyReview::class, 'job_id');
    }
    
    /**
     * Get the main category of the opportunity (job, scholarship, grant, etc.)
     *
     * @return string
     */
    public function getOpportunityCategory(): string
    {
        $type = $this->type;
        
        foreach (self::OPPORTUNITY_CATEGORIES as $category => $types) {
            if (in_array($type, $types)) {
                return $category;
            }
        }
        
        // Default to job if no match found
        return 'job';
    }
    
    /**
     * Check if the opportunity is of a specific category
     *
     * @param string $category
     * @return bool
     */
    public function isOpportunityCategory(string $category): bool
    {
        return $this->getOpportunityCategory() === $category;
    }
    
    /**
     * Get the display name for the opportunity type
     *
     * @return string
     */
    public function getOpportunityTypeDisplay(): string
    {
        return self::OPPORTUNITY_TYPES[$this->type] ?? $this->type;
    }
    
    /**
     * Scope a query to only include opportunities of a specific category
     *
     * @param Builder $query
     * @param string $category
     * @return Builder
     */
    public function scopeOfCategory($query, string $category): Builder
    {
        $types = self::OPPORTUNITY_CATEGORIES[$category] ?? [];
        
        return $query->whereIn('type', $types);
    }
}
