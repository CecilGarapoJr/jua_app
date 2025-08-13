<template>
  <div class="p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-xl font-semibold mb-4">Terminology Replacement Test</h2>
    
    <div class="space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div class="p-3 border rounded">
          <h3 class="font-medium mb-2">Original Text</h3>
          <ul class="space-y-2">
            <li v-for="(value, key) in originalTerms" :key="key">
              {{ key }}: <span class="font-medium">{{ value }}</span>
            </li>
          </ul>
        </div>
        
        <div class="p-3 border rounded bg-gray-50">
          <h3 class="font-medium mb-2">Replaced Text (using v-persona)</h3>
          <ul class="space-y-2">
            <li v-for="(value, key) in originalTerms" :key="key">
              {{ key }}: <span class="font-medium" v-persona>{{ value }}</span>
            </li>
          </ul>
        </div>
      </div>
      
      <div class="mt-6">
        <h3 class="font-medium mb-2">Translation Test</h3>
        <div class="grid grid-cols-2 gap-4">
          <div class="p-3 border rounded">
            <p>Original trans: {{ originalTrans }}</p>
          </div>
          <div class="p-3 border rounded bg-gray-50">
            <p>With persona: {{ trans('We have many Jobs and Candidates for Companies and Employers') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { displayText } from '@/Utils/personaMapping';

const originalTerms = {
  'Singular': 'Company, Candidate, Employer, Job',
  'Plural': 'Companies, Candidates, Employers, Jobs',
  'Lowercase': 'company, candidate, employer, job',
  'Lowercase Plural': 'companies, candidates, employers, jobs',
  'In Context': 'A Company posted a Job for Candidates to apply',
  'Mixed Case': 'The COMPANY needs JOB applicants',
};

const originalTrans = ref('We have many Jobs and Candidates for Companies and Employers');
</script>
