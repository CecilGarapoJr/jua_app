# Service and Category Structure Analysis for Opportunity Platform Adaptation

## Executive Summary

This document analyzes the existing service and category structure in the JUA platform and outlines how to adapt it for the opportunity platform. The analysis includes the current database schema, relationships between models, and recommendations for extending the structure to support various opportunity types while maintaining backward compatibility.

## STRICT IMPLEMENTATION RULES

**IMPORTANT: This implementation follows these strict rules:**

1. **NO NEW UI COMPONENTS WILL BE CREATED** - We are only updating existing UI components
2. **ONLY RENAME EXISTING FIELDS** - Update labels, placeholders, and terminology
3. **ONLY ADD NEW FIELDS TO EXISTING FORMS** - Extend current forms with additional fields
4. **MAINTAIN EXISTING UI STRUCTURE** - No changes to the overall UI layout or design
5. **PRESERVE ALL CURRENT FUNCTIONALITY** - Ensure backward compatibility

All implementation work must adhere to these rules without exception. The goal is to reskin the platform with minimal changes to the existing codebase and UI structure.

## Current Structure

The codebase uses a flexible category system that supports multiple types of categories through a single `Category` model with a `type` field to differentiate between different category types.

### Category Model

```php
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
```

Key features:
- Versatile model with `type` field to distinguish different category types
- Supports hierarchical relationships (parent-child categories)
- Has fields for title, slug, icon, preview, status, featured status

### Category Types

The system currently supports the following category types:

| Type | Description | Controller |
|------|-------------|-----------|
| `blog_category` | For blog posts | `CategoryController` |
| `service` | For job services | `JobServiceController` |
| `job_category` | For job categories | `JobCategoryController` |
| `job_tag` | For job skills/tags | `JobTagController` |

### Relationships

The current relationship structure is as follows:

1. **Services** (`type='service'`) are top-level categories
2. **Job categories** (`type='job_category'`) belong to services
3. **Job tags** (`type='job_tag'`) belong to job categories
4. **Jobs** (Openings) are linked to categories via a many-to-many relationship in the `category_opening` table

### Database Schema

The categories table has the following structure:

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->nullable();
    $table->string('icon')->nullable();
    $table->string('preview')->nullable();
    $table->string('type')->default('category');
    $table->integer('status')->default(1);
    $table->integer('is_featured')->default(1);
    $table->integer('is_menu_item')->default(0);
    $table->string('lang')->default('en');
    $table->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnDelete();
    $table->timestamps();
});
```

The `category_opening` pivot table links categories to openings:

```php
Schema::create('category_opening', function (Blueprint $table) {
    $table->foreignId('opening_id')->constrained('openings')->cascadeOnDelete();
    $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
});
```

## Adaptation Strategy for Opportunity Platform

### 1. Extend Category Types

The current category structure is flexible enough to support the opportunity platform with minimal changes. We recommend adding new category types:

```php
// New opportunity-related category types
const OPPORTUNITY_TYPES = [
    'opportunity_service',     // Top-level opportunity services (replacing 'service')
    'opportunity_category',    // Opportunity categories (replacing 'job_category')
    'opportunity_tag',         // Opportunity tags/skills (replacing 'job_tag')
    'opportunity_eligibility', // New type for eligibility criteria
    'opportunity_funding',     // New type for funding sources
];
```

### 2. Rename Existing Category Types

Update the existing category types to reflect the new terminology:

| Current Type | New Type |
|--------------|----------|
| `service` | `opportunity_service` |
| `job_category` | `opportunity_category` |
| `job_tag` | `opportunity_tag` |

### 3. Create New Controllers

Create new controllers or rename existing ones:

| Current Controller | New Controller |
|-------------------|----------------|
| `JobServiceController` | `OpportunityServiceController` |
| `JobCategoryController` | `OpportunityCategoryController` |
| `JobTagController` | `OpportunityTagController` |
| - | `OpportunityEligibilityController` (new) |
| - | `OpportunityFundingController` (new) |

### 4. Update Opening Model

The Opening model should be extended to support all opportunity types:

```php
// In Opening model
const OPPORTUNITY_TYPES = [
    'Job',
    'Scholarship',
    'Grant',
    'Training',
    'Internship',
];

// Additional fields for different opportunity types can be stored in the 'fields' JSON column
```

### 5. Create Migration for Category Updates

Create a migration to update existing category types:

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
        
    // Add new category types for different opportunity types
    // (Add seed data for eligibility criteria, funding sources, etc.)
}
```

## Implementation Plan

### 1. Database Updates

1. Create a migration to rename existing category types
2. Add new categories for opportunity types
3. Update the Opening model to support new opportunity types
4. Ensure backward compatibility with existing data

### 2. Controller Updates

1. Create new controllers for opportunity-specific categories
2. Update existing controllers to handle new category types
3. Implement methods for managing opportunity-specific data

### 3. UI Component Updates

1. Create forms for managing opportunity categories
2. Update category selection components to support new types
3. Implement filters for opportunity types in search forms

### 4. Frontend Integration

1. Update category selection dropdowns in opportunity creation forms
2. Implement dynamic form fields based on opportunity type
3. Create filters for opportunity categories in search interfaces

## Dynamic Form Fields Based on Opportunity Type

Here's an example of how to implement dynamic form fields based on opportunity type:

```vue
<template>
  <div>
    <!-- Opportunity Type Selector -->
    <div class="form-group">
      <label>{{ trans('Opportunity Type') }}</label>
      <select v-model="opportunityType" @change="updateFormFields">
        <option v-for="type in opportunityTypes" :key="type" :value="type">
          {{ type }}
        </option>
      </select>
    </div>
    
    <!-- Common Fields for All Opportunity Types -->
    <div class="form-group">
      <label>{{ trans('Title') }}</label>
      <input type="text" v-model="formData.title" required />
    </div>
    
    <div class="form-group">
      <label>{{ trans('Description') }}</label>
      <textarea v-model="formData.description" required></textarea>
    </div>
    
    <!-- Dynamic Fields Based on Opportunity Type -->
    <div v-if="opportunityType === 'Job'">
      <div class="form-group">
        <label>{{ trans('Salary Range') }}</label>
        <div class="d-flex">
          <input type="number" v-model="formData.salaryMin" />
          <span class="mx-2">-</span>
          <input type="number" v-model="formData.salaryMax" />
        </div>
      </div>
      
      <div class="form-group">
        <label>{{ trans('Employment Type') }}</label>
        <select v-model="formData.employmentType">
          <option value="Full Time">{{ trans('Full Time') }}</option>
          <option value="Part Time">{{ trans('Part Time') }}</option>
          <option value="Contract">{{ trans('Contract') }}</option>
        </select>
      </div>
    </div>
    
    <div v-if="opportunityType === 'Scholarship'">
      <div class="form-group">
        <label>{{ trans('Scholarship Amount') }}</label>
        <input type="number" v-model="formData.scholarshipAmount" />
      </div>
      
      <div class="form-group">
        <label>{{ trans('Eligibility Criteria') }}</label>
        <textarea v-model="formData.eligibilityCriteria"></textarea>
      </div>
      
      <div class="form-group">
        <label>{{ trans('Application Deadline') }}</label>
        <input type="date" v-model="formData.applicationDeadline" />
      </div>
    </div>
    
    <div v-if="opportunityType === 'Grant'">
      <div class="form-group">
        <label>{{ trans('Grant Amount') }}</label>
        <input type="number" v-model="formData.grantAmount" />
      </div>
      
      <div class="form-group">
        <label>{{ trans('Funding Source') }}</label>
        <input type="text" v-model="formData.fundingSource" />
      </div>
    </div>
    
    <div v-if="opportunityType === 'Training'">
      <div class="form-group">
        <label>{{ trans('Training Duration') }}</label>
        <div class="d-flex align-items-center">
          <input type="number" v-model="formData.trainingDuration" />
          <select v-model="formData.durationUnit">
            <option value="days">{{ trans('Days') }}</option>
            <option value="weeks">{{ trans('Weeks') }}</option>
            <option value="months">{{ trans('Months') }}</option>
          </select>
        </div>
      </div>
      
      <div class="form-group">
        <label>{{ trans('Training Format') }}</label>
        <select v-model="formData.trainingFormat">
          <option value="In-person">{{ trans('In-person') }}</option>
          <option value="Online">{{ trans('Online') }}</option>
          <option value="Hybrid">{{ trans('Hybrid') }}</option>
        </select>
      </div>
    </div>
    
    <div v-if="opportunityType === 'Internship'">
      <div class="form-group">
        <label>{{ trans('Internship Duration') }}</label>
        <div class="d-flex align-items-center">
          <input type="number" v-model="formData.internshipDuration" />
          <select v-model="formData.durationUnit">
            <option value="weeks">{{ trans('Weeks') }}</option>
            <option value="months">{{ trans('Months') }}</option>
          </select>
        </div>
      </div>
      
      <div class="form-group">
        <label>{{ trans('Stipend') }}</label>
        <input type="number" v-model="formData.stipend" />
      </div>
    </div>
    
    <!-- Category Selection -->
    <div class="form-group">
      <label>{{ trans('Categories') }}</label>
      <select v-model="formData.categories" multiple>
        <option v-for="category in filteredCategories" :key="category.id" :value="category.id">
          {{ category.title }}
        </option>
      </select>
    </div>
    
    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary">{{ trans('Save Opportunity') }}</button>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
  opportunityTypes: {
    type: Array,
    default: () => ['Job', 'Scholarship', 'Grant', 'Training', 'Internship'],
  },
});

const opportunityType = ref('Job');
const formData = ref({
  title: '',
  description: '',
  categories: [],
  // Job-specific fields
  salaryMin: null,
  salaryMax: null,
  employmentType: 'Full Time',
  // Scholarship-specific fields
  scholarshipAmount: null,
  eligibilityCriteria: '',
  applicationDeadline: null,
  // Grant-specific fields
  grantAmount: null,
  fundingSource: '',
  // Training-specific fields
  trainingDuration: null,
  trainingFormat: 'In-person',
  // Internship-specific fields
  internshipDuration: null,
  stipend: null,
  // Common fields
  durationUnit: 'months',
});

const filteredCategories = computed(() => {
  // Filter categories based on opportunity type
  return props.categories.filter(category => {
    if (opportunityType.value === 'Job') {
      return category.type === 'opportunity_category' && 
             category.parent?.type === 'opportunity_service' &&
             category.parent?.title.toLowerCase().includes('job');
    } else {
      return category.type === 'opportunity_category' && 
             category.parent?.type === 'opportunity_service' &&
             category.parent?.title.toLowerCase().includes(opportunityType.value.toLowerCase());
    }
  });
});

const updateFormFields = () => {
  // Reset form fields when opportunity type changes
  formData.value = {
    ...formData.value,
    categories: [],
  };
};

const form = useForm(formData);

const submit = () => {
  // Prepare data for submission
  const data = {
    title: formData.value.title,
    description: formData.value.description,
    type: opportunityType.value,
    categories: formData.value.categories,
    fields: {}, // Store type-specific fields in JSON
  };
  
  // Add type-specific fields to the JSON fields property
  switch (opportunityType.value) {
    case 'Job':
      data.fields = {
        salaryMin: formData.value.salaryMin,
        salaryMax: formData.value.salaryMax,
        employmentType: formData.value.employmentType,
      };
      break;
    case 'Scholarship':
      data.fields = {
        scholarshipAmount: formData.value.scholarshipAmount,
        eligibilityCriteria: formData.value.eligibilityCriteria,
        applicationDeadline: formData.value.applicationDeadline,
      };
      break;
    // Add cases for other opportunity types
  }
  
  // Submit the form
  form.post(route('organisation.opportunities.store'), data);
};
</script>
```

## Benefits of This Approach

1. **Minimal Database Changes**: The existing structure is flexible enough to support the new opportunity types.
2. **Reuse Existing Code**: Most of the existing code can be reused with minor modifications.
3. **Consistent UI**: The UI components can be adapted to show different fields based on opportunity type.
4. **Scalability**: The system can easily accommodate new opportunity types in the future.
5. **Backward Compatibility**: Existing jobs data will continue to work with the new structure.

## Conclusion

The service and category structure in the JUA platform is well-designed and flexible enough to support the transition to an opportunity platform with minimal changes. By extending the category types and adapting the UI components, we can create a comprehensive opportunity platform that supports various opportunity types while maintaining backward compatibility with existing data.
