<template>
  <div class="bg-white shadow-md rounded-lg p-6 mb-8">
    <h2 class="text-xl font-medium mb-4">API Response Terminology Processor</h2>
    
    <div class="mb-4">
      <p class="mb-2">This tool demonstrates how to process API responses to ensure terminology replacement is applied to dynamic content.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div class="border rounded-md p-4">
        <h3 class="font-medium mb-2">Original API Response</h3>
        <pre class="bg-gray-50 p-3 rounded text-sm overflow-x-auto">{{ JSON.stringify(mockApiResponse, null, 2) }}</pre>
      </div>
      
      <div class="border rounded-md p-4">
        <h3 class="font-medium mb-2">Processed API Response</h3>
        <pre class="bg-gray-50 p-3 rounded text-sm overflow-x-auto">{{ JSON.stringify(processedApiResponse, null, 2) }}</pre>
      </div>
    </div>
    
    <div class="bg-gray-50 p-4 rounded mb-6">
      <h3 class="text-lg font-medium mb-2">Implementation Example</h3>
      <pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto">
// Process API responses with terminology replacement
function processApiResponse(data) {
  if (!data) return data;
  
  // Handle arrays
  if (Array.isArray(data)) {
    return data.map(item => processApiResponse(item));
  }
  
  // Handle objects
  if (typeof data === 'object') {
    const result = {};
    for (const [key, value] of Object.entries(data)) {
      result[key] = processApiResponse(value);
    }
    return result;
  }
  
  // Handle strings (apply terminology replacement)
  if (typeof data === 'string') {
    return replaceTerminology(data);
  }
  
  // Return other types unchanged
  return data;
}
      </pre>
    </div>
    
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-yellow-700">
            <strong>Important:</strong> API responses should be processed before displaying in the UI to ensure consistent terminology replacement.
          </p>
        </div>
      </div>
    </div>
    
    <div class="mt-6">
      <h3 class="text-lg font-medium mb-2">Integration Recommendations</h3>
      <ul class="list-disc pl-5 space-y-2">
        <li>
          <strong>API Service Layer:</strong> Process responses in API service composables before returning data to components
        </li>
        <li>
          <strong>Global Interceptor:</strong> Add a response interceptor to process all API responses automatically
        </li>
        <li>
          <strong>Component Level:</strong> Use computed properties to process API data before rendering
        </li>
        <li>
          <strong>Store Actions:</strong> Process API data in Vuex/Pinia store actions before committing to state
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { replaceTerminology } from '@/Utils/terminologyMapping';

// Mock API response with terminology that needs replacement
const mockApiResponse = ref({
  status: "success",
  data: {
    jobs: [
      {
        id: 1,
        title: "Senior Software Engineer",
        company: "Tech Company Inc.",
        description: "We are looking for a Candidate with experience in Vue.js and Laravel.",
        requirements: "The ideal Candidate should have 3+ years of experience.",
        employer: {
          name: "Tech Company Recruitment",
          type: "Employer"
        }
      },
      {
        id: 2,
        title: "Marketing Manager",
        company: "Marketing Solutions",
        description: "Job requires coordination with multiple teams.",
        candidates: ["John Doe", "Jane Smith"],
        employer: {
          name: "Marketing Company",
          type: "Employer"
        }
      }
    ],
    message: "Successfully retrieved Jobs for Candidates"
  }
});

// Process the API response to apply terminology replacement
const processedApiResponse = computed(() => {
  return processApiResponse(mockApiResponse.value);
});

// Recursive function to process API responses with terminology replacement
function processApiResponse(data) {
  if (!data) return data;
  
  // Handle arrays
  if (Array.isArray(data)) {
    return data.map(item => processApiResponse(item));
  }
  
  // Handle objects
  if (typeof data === 'object' && data !== null) {
    const result = {};
    for (const [key, value] of Object.entries(data)) {
      result[key] = processApiResponse(value);
    }
    return result;
  }
  
  // Handle strings (apply terminology replacement)
  if (typeof data === 'string') {
    return replaceTerminology(data);
  }
  
  // Return other types unchanged
  return data;
}
</script>
