# Reskin Analysis: Jobs to Opportunities Platform

## Executive Summary

This document outlines the comprehensive strategy for reskinning the JUA platform from a traditional jobs platform to a broader opportunity platform. The reskin involves updating terminology, adapting UI components, extending data models, and ensuring consistent user experience while maintaining the core functionality of the system.

## STRICT IMPLEMENTATION RULES

**IMPORTANT: This implementation follows these strict rules:**

1. **NO NEW UI COMPONENTS WILL BE CREATED** - We are only updating existing UI components
2. **ONLY RENAME EXISTING FIELDS** - Update labels, placeholders, and terminology
3. **ONLY ADD NEW FIELDS TO EXISTING FORMS** - Extend current forms with additional fields
4. **MAINTAIN EXISTING UI STRUCTURE** - No changes to the overall UI layout or design
5. **PRESERVE ALL CURRENT FUNCTIONALITY** - Ensure backward compatibility

All implementation work must adhere to these rules without exception. The goal is to reskin the platform with minimal changes to the existing codebase and UI structure.

## Current Understanding

The JUA platform is being transformed from a jobs-focused system to an opportunities-focused platform that encompasses various opportunity types including jobs, scholarships, grants, training programs, and internships. This transformation requires updates to form fields, UI elements, terminology, and data structures.

## Key Terminology Changes

From the `terminologyMapping.js` file, the following terminology mapping is already in place:

```javascript
export const terminologyMap = {
  // Singular forms
  'Company': 'Organisation',
  'company': 'organisation',
  'Candidate': 'Applicant',
  'candidate': 'applicant',
  'Employer': 'Organisation',
  'employer': 'organisation',
  'Job': 'Opportunity',
  'job': 'opportunity',
  
  // Plural forms
  'Companies': 'Organisations',
  'companies': 'organisations',
  'Candidates': 'Applicants',
  'candidates': 'applicants',
  'Employers': 'Organisations',
  'employers': 'organisations',
  'Jobs': 'Opportunities',
  'jobs': 'opportunities',
};
```

## Key Areas Requiring Updates

### 1. Form Field Updates

| Current Field | New Field | Notes |
|---------------|-----------|-------|
| Job title | Opportunity title | Update labels and placeholders |
| Job description | Opportunity description | Update labels and placeholders |
| Job type | Opportunity type | Expand options to include all opportunity types |
| Job categories | Opportunity categories | Update to reflect broader categories |
| Salary range | Value range | Adapt to support different value types (salary, grant amount, etc.) |
| Experience | Eligibility criteria | Expand to include various eligibility requirements |

### 2. UI Component Updates

| Component | Required Changes |
|-----------|------------------|
| Job listings | Rename to Opportunity listings; Update filters for opportunity types |
| Job details | Rename to Opportunity details; Show different fields based on opportunity type |
| Job application forms | Rename to Opportunity application forms; Adapt fields based on opportunity type |
| Job search filters | Rename to Opportunity search filters; Add filters for opportunity types |

### 3. Route Updates

| Route Type | Changes Implemented |
|------------|---------------------|
| Web Routes | Added `/opportunities` resource routes with actions for bookmarking and applying |
| Admin Routes | Added `opportunity` resource routes and related category/service routes |
| Employer Routes | Added opportunity management routes for employers |
| Terminology Routes | Updated to use new OpportunityController instead of JobController |
| Frontend Route Mapping | Added JavaScript utilities to map between legacy and new routes |

#### Route Mapping Table

| Current Route | New Route |
|---------------|-----------|
| `/jobs/*` | `/opportunities/*` |
| `employer.jobs.*` | `organisation.opportunities.*` |
| `candidate.*` | `applicant.*` |
| `job-category/*` | `opportunity-category/*` |
| `job-service/*` | `opportunity-service/*` |

### 4. Component Names and References

| Current Name | New Name |
|--------------|----------|
| `JobController.php` | `OpportunityController.php` |
| `JobsItemList.vue` | `OpportunitiesItemList.vue` |
| `JobsItemGrid.vue` | `OpportunitiesItemGrid.vue` |
| `JobIntroSection.vue` | `OpportunityIntroSection.vue` |

## Data Model Understanding

### Current Job Model (Opening.php)

The current Opening model already has a flexible structure with fields that can be adapted for different opportunity types:

```php
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
```

### New Opportunity Model

The opportunity model should be extended to support various opportunity types:

```php
const OPPORTUNITY_TYPES = [
    'Job',
    'Scholarship',
    'Grant',
    'Training',
    'Internship',
];
```

Additional fields can be stored in the existing `meta` or `fields` JSON columns to maintain backward compatibility while supporting new opportunity types.

## Implementation Strategy

### 1. Terminology Updates

1. Ensure all frontend components use the `replaceTerminology()` function or `v-persona` directive
2. Update all hardcoded text in templates to use translation functions
3. Update translation files to reflect new terminology

### 2. Form Field Updates

1. Update form field labels and placeholders
2. Add new fields specific to different opportunity types
3. Create dynamic forms that show different fields based on opportunity type
4. Update validation rules to accommodate new fields

### 3. UI Updates

1. Rename and update labels in existing components
2. Add opportunity type options to existing filters
3. Update existing display components to conditionally show fields based on opportunity type
4. **NO NEW UI COMPONENTS WILL BE CREATED**

### 4. Route and Controller Updates

1. Update route names and paths
2. Rename controllers and update method names
3. Update route references in components
4. Ensure backward compatibility for existing links

### 5. Database Updates

1. Add new opportunity types to the Opening model
2. Update category types to reflect opportunity categories
3. Create migrations for any necessary schema changes
4. Migrate existing data to new structure

## Testing Strategy

1. Create test cases for each opportunity type
2. Verify form submission and validation for all opportunity types
3. Test search and filtering functionality
4. Ensure backward compatibility with existing data
5. Test user journeys for different user types (organizations, applicants)

## Rollout Plan

1. Update backend models and controllers
2. Update database schema and migrate data
3. Update frontend components and terminology
4. Test thoroughly in staging environment
5. Deploy to production with monitoring
6. Provide user documentation and support

## Conclusion

The reskin from a jobs platform to an opportunities platform primarily involves terminology changes, UI updates, and extending the data model to support different opportunity types. The existing code structure is well-organized and should accommodate these changes without major architectural modifications.
