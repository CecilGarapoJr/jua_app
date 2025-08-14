<template>
  <div class="opportunity-type-fields">
    <!-- Job-specific fields -->
    <div v-if="isJobType" class="job-fields">
      <div class="form-group mb-3">
        <label for="salary_type" class="form-label">{{ $t('Salary Type') }}</label>
        <select
          id="salary_type"
          v-model="localFields.salary_type"
          class="form-select"
          :class="{ 'is-invalid': errors.salary_type }"
          @change="updateFields"
        >
          <option value="">{{ $t('Select Salary Type') }}</option>
          <option value="fixed">{{ $t('Fixed') }}</option>
          <option value="range">{{ $t('Range') }}</option>
          <option value="negotiable">{{ $t('Negotiable') }}</option>
        </select>
        <div v-if="errors.salary_type" class="invalid-feedback">
          {{ errors.salary_type }}
        </div>
      </div>

      <div v-if="localFields.salary_type === 'fixed' || localFields.salary_type === 'range'" class="form-group mb-3">
        <label for="salary_range" class="form-label">{{ $t('Salary Range') }}</label>
        <div class="input-group">
          <span class="input-group-text">{{ currencySymbol }}</span>
          <input
            id="salary_range"
            v-model="localFields.salary_range"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors.salary_range }"
            :placeholder="$t('Enter salary range')"
            @input="updateFields"
          />
        </div>
        <div v-if="errors.salary_range" class="invalid-feedback">
          {{ errors.salary_range }}
        </div>
      </div>

      <div class="form-group mb-3">
        <label for="experience" class="form-label">{{ $t('Experience Required') }}</label>
        <select
          id="experience"
          v-model="localFields.experience"
          class="form-select"
          :class="{ 'is-invalid': errors.experience }"
          @change="updateFields"
        >
          <option value="">{{ $t('Select Experience') }}</option>
          <option value="entry">{{ $t('Entry Level') }}</option>
          <option value="intermediate">{{ $t('Intermediate') }}</option>
          <option value="expert">{{ $t('Expert') }}</option>
        </select>
        <div v-if="errors.experience" class="invalid-feedback">
          {{ errors.experience }}
        </div>
      </div>
    </div>

    <!-- Scholarship-specific fields -->
    <div v-if="isScholarshipType" class="scholarship-fields">
      <div class="form-group mb-3">
        <label for="award_amount" class="form-label">{{ $t('Award Amount') }}</label>
        <div class="input-group">
          <span class="input-group-text">{{ currencySymbol }}</span>
          <input
            id="award_amount"
            v-model="localFields.award_amount"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors.award_amount }"
            :placeholder="$t('Enter award amount')"
            @input="updateFields"
          />
        </div>
        <div v-if="errors.award_amount" class="invalid-feedback">
          {{ errors.award_amount }}
        </div>
      </div>

      <div class="form-group mb-3">
        <label for="eligibility_criteria" class="form-label">{{ $t('Eligibility Criteria') }}</label>
        <textarea
          id="eligibility_criteria"
          v-model="localFields.eligibility_criteria"
          class="form-control"
          :class="{ 'is-invalid': errors.eligibility_criteria }"
          :placeholder="$t('Enter eligibility criteria')"
          rows="3"
          @input="updateFields"
        ></textarea>
        <div v-if="errors.eligibility_criteria" class="invalid-feedback">
          {{ errors.eligibility_criteria }}
        </div>
      </div>
    </div>

    <!-- Grant-specific fields -->
    <div v-if="isGrantType" class="grant-fields">
      <div class="form-group mb-3">
        <label for="grant_amount" class="form-label">{{ $t('Grant Amount') }}</label>
        <div class="input-group">
          <span class="input-group-text">{{ currencySymbol }}</span>
          <input
            id="grant_amount"
            v-model="localFields.grant_amount"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors.grant_amount }"
            :placeholder="$t('Enter grant amount')"
            @input="updateFields"
          />
        </div>
        <div v-if="errors.grant_amount" class="invalid-feedback">
          {{ errors.grant_amount }}
        </div>
      </div>

      <div class="form-group mb-3">
        <label for="funding_source" class="form-label">{{ $t('Funding Source') }}</label>
        <input
          id="funding_source"
          v-model="localFields.funding_source"
          type="text"
          class="form-control"
          :class="{ 'is-invalid': errors.funding_source }"
          :placeholder="$t('Enter funding source')"
          @input="updateFields"
        />
        <div v-if="errors.funding_source" class="invalid-feedback">
          {{ errors.funding_source }}
        </div>
      </div>
    </div>

    <!-- Training-specific fields -->
    <div v-if="isTrainingType" class="training-fields">
      <div class="form-group mb-3">
        <label for="duration" class="form-label">{{ $t('Duration') }}</label>
        <input
          id="duration"
          v-model="localFields.duration"
          type="text"
          class="form-control"
          :class="{ 'is-invalid': errors.duration }"
          :placeholder="$t('Enter duration (e.g., 3 months)')"
          @input="updateFields"
        />
        <div v-if="errors.duration" class="invalid-feedback">
          {{ errors.duration }}
        </div>
      </div>

      <div class="form-group mb-3">
        <label for="format" class="form-label">{{ $t('Format') }}</label>
        <select
          id="format"
          v-model="localFields.format"
          class="form-select"
          :class="{ 'is-invalid': errors.format }"
          @change="updateFields"
        >
          <option value="">{{ $t('Select Format') }}</option>
          <option value="online">{{ $t('Online') }}</option>
          <option value="in-person">{{ $t('In-person') }}</option>
          <option value="hybrid">{{ $t('Hybrid') }}</option>
        </select>
        <div v-if="errors.format" class="invalid-feedback">
          {{ errors.format }}
        </div>
      </div>
    </div>

    <!-- Internship-specific fields -->
    <div v-if="isInternshipType" class="internship-fields">
      <div class="form-group mb-3">
        <label for="internship_duration" class="form-label">{{ $t('Duration') }}</label>
        <input
          id="internship_duration"
          v-model="localFields.internship_duration"
          type="text"
          class="form-control"
          :class="{ 'is-invalid': errors.internship_duration }"
          :placeholder="$t('Enter duration (e.g., 3 months)')"
          @input="updateFields"
        />
        <div v-if="errors.internship_duration" class="invalid-feedback">
          {{ errors.internship_duration }}
        </div>
      </div>

      <div class="form-group mb-3">
        <label for="stipend" class="form-label">{{ $t('Stipend') }}</label>
        <div class="input-group">
          <span class="input-group-text">{{ currencySymbol }}</span>
          <input
            id="stipend"
            v-model="localFields.stipend"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors.stipend }"
            :placeholder="$t('Enter stipend amount')"
            @input="updateFields"
          />
        </div>
        <div v-if="errors.stipend" class="invalid-feedback">
          {{ errors.stipend }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch } from 'vue';
import { transWithTerminology } from '@/Utils/terminologyMapping';

export default {
  name: 'OpportunityTypeFields',
  
  props: {
    type: {
      type: String,
      required: true
    },
    fields: {
      type: Object,
      default: () => ({})
    },
    errors: {
      type: Object,
      default: () => ({})
    },
    currency: {
      type: String,
      default: 'USD'
    }
  },
  
  setup(props, { emit }) {
    const localFields = ref({...props.fields});
    
    // Watch for changes in props.fields and update localFields
    watch(() => props.fields, (newFields) => {
      localFields.value = {...newFields};
    }, { deep: true });
    
    // Computed properties to determine opportunity type
    const isJobType = computed(() => {
      return props.type && ['job_full_time', 'job_part_time', 'job_contract', 'job_temporary'].includes(props.type);
    });
    
    const isScholarshipType = computed(() => {
      return props.type === 'scholarship';
    });
    
    const isGrantType = computed(() => {
      return props.type === 'grant';
    });
    
    const isTrainingType = computed(() => {
      return props.type === 'training';
    });
    
    const isInternshipType = computed(() => {
      return props.type === 'internship';
    });
    
    // Currency symbol based on currency prop
    const currencySymbol = computed(() => {
      const symbols = {
        'USD': '$',
        'EUR': '€',
        'GBP': '£',
        'JPY': '¥',
        // Add more currencies as needed
      };
      
      return symbols[props.currency] || props.currency;
    });
    
    // Method to emit updated fields to parent
    const updateFields = () => {
      emit('update:fields', localFields.value);
    };
    
    // Custom translation function that applies terminology mapping
    const $t = (key) => {
      return transWithTerminology(key);
    };
    
    return {
      localFields,
      isJobType,
      isScholarshipType,
      isGrantType,
      isTrainingType,
      isInternshipType,
      currencySymbol,
      updateFields,
      $t
    };
  }
};
</script>

<style scoped>
.opportunity-type-fields {
  margin-bottom: 1.5rem;
}

.invalid-feedback {
  display: block;
}
</style>
