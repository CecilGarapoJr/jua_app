# Opportunity Platform Implementation Plan

## Overview

This document outlines the comprehensive implementation plan for transforming the JUA platform from a jobs-focused system to an opportunities-focused platform. The plan combines insights from both the reskin analysis and the service/category structure analysis to provide a clear roadmap for development.

## STRICT IMPLEMENTATION RULES

**IMPORTANT: This implementation follows these strict rules:**

1. **NO NEW UI COMPONENTS WILL BE CREATED** - We are only updating existing UI components
2. **ONLY RENAME EXISTING FIELDS** - Update labels, placeholders, and terminology
3. **ONLY ADD NEW FIELDS TO EXISTING FORMS** - Extend current forms with additional fields
4. **MAINTAIN EXISTING UI STRUCTURE** - No changes to the overall UI layout or design
5. **PRESERVE ALL CURRENT FUNCTIONALITY** - Ensure backward compatibility

All implementation work must adhere to these rules without exception. The goal is to reskin the platform with minimal changes to the existing codebase and UI structure.

## Implementation Phases

### Phase 1: Backend Structure Updates

#### 1.1 Database and Model Updates

1. **Update Opening Model**
   - Add new opportunity types constant
   ```php
   const OPPORTUNITY_TYPES = [
       'Job',
       'Scholarship',
       'Grant',
       'Training',
       'Internship',
   ];
   ```
   - Ensure the `fields` JSON column is properly utilized for type-specific attributes

2. **Create Database Migration**
   - Create a migration to update existing category types
   ```php
   public function up(): void
   {
       // Update existing category types
       DB::table('categories')
           ->where('type', 'service')
           ->update(['type' => 'opportunity_service']);
           
       DB::table('categories')
           ->where('type', 'job_category')
           ->update(['type' => 'opportunity_category']);
           
       DB::table('categories')
           ->where('type', 'job_tag')
           ->update(['type' => 'opportunity_tag']);
   }
   ```

3. **Create New Category Types**
   - Add new categories for opportunity-specific attributes
   - Create seed data for common opportunity types

#### 1.2 Controller Updates

1. **Create/Rename Controllers**
   - Rename `JobController.php` to `OpportunityController.php`
   - Update `JobServiceController.php` to `OpportunityServiceController.php`
   - Update `JobCategoryController.php` to `OpportunityCategoryController.php`
   - Update `JobTagController.php` to `OpportunityTagController.php`

2. **Update Controller Methods**
   - Update validation rules to accommodate new fields
   - Modify store/update methods to handle opportunity-specific data
   - Update index/show methods to display opportunity-specific information

#### 1.3 Route Updates

1. **Update Route Names** ✅
   - Add new opportunity routes in `web.php`, `admin.php`, and `employer.php`
   - Maintain backward compatibility by keeping existing job routes
   - Update terminology routes to use new OpportunityController

2. **Create Route Documentation** ✅
   - Document all route changes in `docs/opportunity-routes.md`
   - Create route tests to verify functionality

3. **Update Frontend Route Mapping** ✅
   - Update JavaScript route mapping utilities in `resources/js/Utils/routeMapping.js`
   - Add opportunity-specific route mappings
   - Ensure proper mapping between legacy and new routes

### Phase 2: Frontend Updates

#### 2.1 Terminology Updates

1. **Update Terminology Map** ⏳
   - Ensure all job-related terms are mapped to opportunity-related terms
   - Add new terms specific to different opportunity types
   - Update terminology mapping to include opportunity-specific terms

2. **Apply Terminology Updates** ⏳
   - Ensure all components use the `replaceTerminology()` function
   - Update hardcoded text in templates
   - Replace all instances of "job" with "opportunity" in UI text

3. **Next Steps**
   - Update the frontend components to use the new opportunity routes
   - Implement conditional rendering based on opportunity type
   - Test all terminology changes to ensure consistency

#### 2.2 File Renaming (No UI Changes)

1. **Rename Component Files**
   - Rename `JobsItemList.vue` to `OpportunitiesItemList.vue`
   - Rename `JobsItemGrid.vue` to `OpportunitiesItemGrid.vue`
   - Rename `JobIntroSection.vue` to `OpportunityIntroSection.vue`
   - Update all imports and references
   - **IMPORTANT: Maintain identical functionality and appearance**

2. **Update References**
   - Update import statements in all files
   - Update component registrations
   - Ensure backward compatibility with existing code

#### 2.3 Form Field Updates

1. **Extend Existing Forms**
   - Add opportunity type selector to existing job creation/edit forms
   - **DO NOT create new form components**

2. **Update Form Fields**
   - Update labels and placeholders in existing forms
   - Add new fields for different opportunity types to existing forms
   - **Maintain existing form structure and layout**

### Phase 3: UI Field and Label Updates

#### 3.1 Search and Filter Updates

1. **Update Existing Search Filters**
   - Add opportunity type option to existing filter dropdowns
   - Update category filter labels to reflect new terminology
   - **DO NOT modify the filter UI structure**

2. **Update Results Display Text**
   - Update text and labels in existing result components
   - Show/hide fields based on opportunity type using existing conditional rendering
   - **NO new display components will be created**

#### 3.2 Dashboard Updates

1. **Update Organization Dashboard Labels**
   - Rename statistics labels from "Jobs" to "Opportunities"
   - Add type-specific labels to existing statistics panels
   - **Maintain existing dashboard layout and components**

2. **Update Applicant Dashboard Labels**
   - Update text from "Job recommendations" to "Opportunity recommendations"
   - Update application tracking labels
   - **NO structural changes to dashboard**

#### 3.3 Detail Page Updates

1. **Update Existing Detail Pages**
   - Add conditional fields to existing detail page templates
   - Update labels and section titles
   - **Maintain existing page structure**

2. **Update Existing Application Forms**
   - Add conditional fields to existing application forms
   - Update validation rules for different application types
   - **NO new form components will be created**

## Technical Implementation Details

### Field Updates for Existing Components

**IMPORTANT: We will only update existing components, not create new ones.**

#### Updating Existing Job Form

We will update the existing job creation/edit form with additional fields for different opportunity types:

```vue
<!-- Example of updating the existing job form (NOT creating a new component) -->
<template>
  <!-- Use existing form structure -->
  <div class="form-group">
    <label>{{ trans('Opportunity Type') }}</label>
    <!-- Add new field to existing form -->
    <select v-model="formData.opportunityType" @change="updateVisibleFields">
      <option value="job">{{ trans('Job') }}</option>
      <option value="scholarship">{{ trans('Scholarship') }}</option>
      <option value="grant">{{ trans('Grant') }}</option>
      <option value="training">{{ trans('Training') }}</option>
      <option value="internship">{{ trans('Internship') }}</option>
    </select>
  </div>
  
  <!-- Existing job fields - keep as is -->
  <div class="form-group">
    <label>{{ trans('Title') }}</label>
    <input type="text" v-model="formData.title" required />
  </div>
  
  <!-- Conditionally show additional fields based on type -->
  <div v-if="formData.opportunityType === 'scholarship'" class="form-group">
    <label>{{ trans('Scholarship Amount') }}</label>
    <input type="number" v-model="formData.scholarshipAmount" />
  </div>
  
  <div v-if="formData.opportunityType === 'grant'" class="form-group">
    <label>{{ trans('Grant Amount') }}</label>
    <input type="number" v-model="formData.grantAmount" />
  </div>
  
  <!-- Keep existing submit button and form structure -->
</template>

<script setup>
// Use existing component setup with additional fields
import { ref, watch } from 'vue';

// Initialize with existing form data structure
const formData = ref({
  // Existing fields
  title: '',
  description: '',
  // New fields
  opportunityType: 'job',
  scholarshipAmount: null,
  grantAmount: null,
  // etc.
});

// Function to update visible fields based on type
const updateVisibleFields = () => {
  // Show/hide fields based on opportunity type
  // No changes to component structure
};
</script>
```

#### Updating Existing Job Card/List Item

Update the existing job card or list item component to display different fields based on opportunity type:

```vue
<!-- Example of updating existing job list item (NOT creating a new component) -->
<template>
  <!-- Keep existing structure -->
  <div class="job-list-item">
    <div class="job-title">
      <!-- Update label only -->
      <h3>{{ job.title }}</h3>
      
      <!-- Add opportunity type badge to existing structure -->
      <span class="badge">{{ job.opportunityType }}</span>
    </div>
    
    <!-- Existing fields - keep structure -->
    <div class="job-details">
      <p>{{ job.description }}</p>
      
      <!-- Conditionally show different fields based on type -->
      <div v-if="job.opportunityType === 'job'">
        <!-- Existing job fields -->
        <p>{{ job.salary }}</p>
      </div>
      
      <div v-if="job.opportunityType === 'scholarship'">
        <!-- New scholarship fields in existing structure -->
        <p>{{ trans('Amount') }}: {{ job.scholarshipAmount }}</p>
      </div>
    </div>
    
    <!-- Keep existing buttons and actions -->
  </div>
</template>
```

### Field Updates for Different Opportunity Types

For each opportunity type, we'll add these fields to the existing forms:

| Opportunity Type | Fields to Add to Existing Forms |
|------------------|--------------------------------|
| Job | (Existing fields - no changes) |
| Scholarship | Scholarship Amount, Eligibility Criteria, Application Deadline |
| Grant | Grant Amount, Funding Source, Application Requirements |
| Training | Duration, Format (Online/In-person), Prerequisites |
| Internship | Duration, Stipend, Learning Objectives |

## Testing and Quality Assurance

### Unit Testing

1. **Model Tests**
   - Test Opening model with different opportunity types
   - Verify category relationships

2. **Controller Tests**
   - Test CRUD operations for opportunities
   - Verify validation rules

### Integration Testing

1. **Form Submission Tests**
   - Test opportunity creation with different types
   - Verify data is stored correctly

2. **Search and Filter Tests**
   - Test filtering by opportunity type
   - Verify search results

### User Acceptance Testing

1. **Organization User Journey**
   - Create different types of opportunities
   - Manage applications

2. **Applicant User Journey**
   - Search for opportunities
   - Apply to different opportunity types

## Rollout Strategy

### 1. Development Environment

- Implement backend changes
- Create new components
- Test with sample data

### 2. Staging Environment

- Migrate production data
- Test with real data
- Verify backward compatibility

### 3. Production Deployment

- Deploy database migrations
- Deploy backend changes
- Deploy frontend changes
- Monitor for issues

## Conclusion

This implementation plan provides a comprehensive roadmap for transforming the JUA platform from a jobs-focused system to an opportunities-focused platform. By following this plan, we can ensure a smooth transition while maintaining backward compatibility and enhancing the user experience.

**STRICT ADHERENCE TO EXISTING UI:**

1. We will NOT create any new UI components
2. We will ONLY update existing UI with new fields or renamed fields
3. We will maintain the existing UI structure and layout
4. We will extend current forms with additional fields as needed
5. We will update terminology consistently throughout the platform

This approach minimizes risk by leveraging the existing architecture while extending it to support various opportunity types. The implementation focuses on terminology updates and field additions without changing the fundamental UI structure or creating new components.
