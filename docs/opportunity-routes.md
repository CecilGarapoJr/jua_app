# Opportunity Platform Routes

This document outlines the route structure for the new Opportunity Platform, which extends the existing Jobs platform to support various opportunity types such as jobs, scholarships, grants, training, and internships.

## Route Structure Overview

The route structure follows a dual approach:
1. **Legacy Routes**: Maintaining existing job routes for backward compatibility
2. **New Routes**: Adding opportunity-specific routes that use the new OpportunityController

## Web Routes

### Public-facing Routes

```php
// Opportunity resource routes
Route::resource('/opportunities', Web\OpportunityController::class)
    ->names('opportunity')->only(['index', 'show']);

// Opportunity actions
Route::post('/opportunities/{opportunity}/bookmark', [Web\OpportunityController::class, 'toggleBookmark'])
    ->name('opportunity.bookmark')->middleware('auth');
Route::get('/opportunities/{opportunity}/apply', [Web\OpportunityController::class, 'apply'])
    ->name('opportunity.apply')->middleware('auth');
Route::post('/opportunities/{opportunity}/apply', [Web\OpportunityController::class, 'applyStore'])
    ->name('opportunity.apply')->middleware('auth');

// Opportunity categories and services
Route::get('opportunity-category/{slug}', [Web\OpportunityController::class, 'index'])
    ->name('opportunity-categories.show');
Route::get('opportunity-service/{slug}', [Web\OpportunityController::class, 'index'])
    ->name('opportunity-services.show');
```

## Admin Routes

```php
// Opportunity resource routes
Route::resource('opportunity', ADMIN\OpportunityController::class)->except(['edit']);

// Opportunity categories, services, and skills
Route::resource('opportunity-service', ADMIN\JobServiceController::class);
Route::resource('opportunity-category', ADMIN\JobCategoryController::class);
Route::resource('opportunity-skills', ADMIN\JobTagController::class);
```

## Employer Routes

```php
// Opportunity application management
Route::post('opportunity-application/seen', [Employer\OpportunityController::class, 'updateSeenAt'])
    ->name('opportunity-application-seen');

// Opportunity resource routes
Route::resource('opportunities', Employer\OpportunityController::class);

// Opportunity applicants and reviews
Route::get('opportunity-applicants', [Employer\OpportunityController::class, 'applicants'])
    ->name('opportunity-applicants');
Route::get('opportunity-reviews', [Employer\OpportunityController::class, 'reviews'])
    ->name('opportunity-reviews');
```

## Terminology Routes

The terminology routes map new terminology to the appropriate controllers, allowing for a consistent user experience while maintaining backward compatibility.

```php
// Opportunity routes using OpportunityController
Route::resource('/opportunities', WEB\OpportunityController::class)
    ->only(['index', 'show'])
    ->names('opportunities');
Route::post('/opportunities/{opportunity}/bookmark', [WEB\OpportunityController::class, 'toggleBookmark'])
    ->name('opportunities.bookmark')
    ->middleware('auth');
Route::get('/opportunities/{opportunity}/apply', [WEB\OpportunityController::class, 'apply'])
    ->name('opportunities.apply')
    ->middleware('auth');
Route::post('/opportunities/{opportunity}/apply', [WEB\OpportunityController::class, 'applyStore'])
    ->name('opportunities.apply')
    ->middleware('auth');

// Opportunity categories and services
Route::get('opportunity-category/{slug}', [WEB\OpportunityController::class, 'index'])
    ->name('opportunity-categories.show');
Route::get('opportunity-service/{slug}', [WEB\OpportunityController::class, 'index'])
    ->name('opportunity-services.show');
```

## Frontend Route Mapping

The JavaScript route mapping utilities have been updated to handle the new opportunity routes and types:

```javascript
// Route terminology mapping
export const routeTerminologyMap = {
  // Core route mappings
  'job': 'opportunity',
  'jobs': 'opportunities',
  
  // Specific route segment mappings
  'job-categories': 'opportunity-categories',
  'job-services': 'opportunity-services',
  'job-application': 'opportunity-application',
  'job-applicants': 'opportunity-applicants',
  'job-reviews': 'opportunity-reviews',
  
  // New opportunity-specific route mappings
  'job-full-time': 'opportunity-job-full-time',
  'job-part-time': 'opportunity-job-part-time',
  'job-contract': 'opportunity-job-contract',
  'job-temporary': 'opportunity-job-temporary',
  'job-internship': 'opportunity-internship',
  'scholarship': 'opportunity-scholarship',
  'grant': 'opportunity-grant',
  'training': 'opportunity-training',
};
```

## Next Steps

1. Update frontend components to use the new opportunity routes
2. Test all routes to ensure proper functionality
3. Update documentation and user guides to reflect the new opportunity platform
4. Implement gradual rollout strategy to transition from jobs to opportunities
