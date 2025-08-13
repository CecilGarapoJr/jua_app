<template>
  <div class="bg-white shadow-md rounded-lg p-6 mb-8">
    <h2 class="text-xl font-medium mb-4">Terminology Replacement Tester</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <!-- Input Test Section -->
      <div class="border rounded-md p-4">
        <h3 class="font-medium mb-2">Test Input</h3>
        <textarea 
          v-model="testInput" 
          class="w-full h-32 p-2 border rounded resize-none mb-2"
          placeholder="Enter text containing terminology to test replacement..."
        ></textarea>
        <button 
          @click="processText" 
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
        >
          Process Text
        </button>
      </div>
      
      <!-- Output Test Section -->
      <div class="border rounded-md p-4">
        <h3 class="font-medium mb-2">Processed Output</h3>
        <div class="bg-gray-50 p-3 rounded h-32 mb-2 overflow-auto">
          <p v-if="processedText">{{ processedText }}</p>
          <p v-else class="text-gray-500">Processed text will appear here...</p>
        </div>
        <div class="text-sm text-gray-600">
          <span v-if="replacementCount > 0" class="text-green-600 font-medium">
            {{ replacementCount }} term{{ replacementCount !== 1 ? 's' : '' }} replaced
          </span>
          <span v-else-if="processedText" class="text-gray-600">
            No terms replaced
          </span>
        </div>
      </div>
    </div>
    
    <!-- Comprehensive Test Cases -->
    <div class="mb-6">
      <h3 class="text-lg font-medium mb-3">Comprehensive Test Cases</h3>
      <div class="space-y-4">
        <div v-for="(testCase, index) in testCases" :key="index" class="border rounded-md p-4">
          <div class="flex justify-between items-center mb-2">
            <h4 class="font-medium">{{ testCase.name }}</h4>
            <button 
              @click="runTest(index)" 
              class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition"
            >
              Run Test
            </button>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <div class="text-sm font-medium mb-1">Original:</div>
              <div class="bg-gray-50 p-2 rounded text-sm">{{ testCase.input }}</div>
            </div>
            <div>
              <div class="text-sm font-medium mb-1">Expected Output:</div>
              <div class="bg-gray-50 p-2 rounded text-sm">{{ testCase.expected }}</div>
            </div>
          </div>
          
          <div v-if="testCase.result" class="mt-3">
            <div class="text-sm font-medium mb-1">Actual Result:</div>
            <div class="bg-gray-50 p-2 rounded text-sm">{{ testCase.result }}</div>
            
            <div class="mt-2 flex items-center">
              <div v-if="testCase.passed" class="text-green-600 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>Test Passed</span>
              </div>
              <div v-else class="text-red-600 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span>Test Failed</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Dynamic Content Test -->
    <div class="border rounded-md p-4 mb-6">
      <h3 class="text-lg font-medium mb-3">Dynamic Content Test</h3>
      <p class="mb-3">This test simulates dynamic content updates to verify terminology replacement works correctly with reactive data.</p>
      
      <div class="mb-4">
        <button 
          @click="toggleDynamicContent" 
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
        >
          {{ showDynamicContent ? 'Hide' : 'Show' }} Dynamic Content
        </button>
      </div>
      
      <div v-if="showDynamicContent" class="space-y-4">
        <!-- Regular v-text binding -->
        <div class="border rounded p-3">
          <h4 class="font-medium mb-1">v-text Binding:</h4>
          <div v-text="dynamicContent" class="bg-gray-50 p-2 rounded"></div>
          <div class="mt-2 text-sm text-amber-600">
            ⚠️ v-text does not apply terminology replacement by default
          </div>
        </div>
        
        <!-- v-persona directive -->
        <div class="border rounded p-3">
          <h4 class="font-medium mb-1">v-persona Directive:</h4>
          <div v-persona v-text="dynamicContent" class="bg-gray-50 p-2 rounded"></div>
          <div class="mt-2 text-sm text-green-600">
            ✓ v-persona correctly applies terminology replacement to dynamic content
          </div>
        </div>
        
        <!-- TerminologyText component -->
        <div class="border rounded p-3">
          <h4 class="font-medium mb-1">TerminologyText Component:</h4>
          <TerminologyText :text="dynamicContent" class="bg-gray-50 p-2 rounded" />
          <div class="mt-2 text-sm text-green-600">
            ✓ TerminologyText component correctly applies terminology replacement
          </div>
        </div>
        
        <!-- Computed property -->
        <div class="border rounded p-3">
          <h4 class="font-medium mb-1">Computed Property:</h4>
          <div class="bg-gray-50 p-2 rounded">{{ processedDynamicContent }}</div>
          <div class="mt-2 text-sm text-green-600">
            ✓ Computed property with replaceTerminology correctly applies replacement
          </div>
        </div>
      </div>
    </div>
    
    <!-- Recommendations -->
    <div class="bg-gray-50 p-4 rounded">
      <h3 class="text-lg font-medium mb-2">Best Practices for Complete Coverage</h3>
      <ul class="list-disc pl-5 space-y-2">
        <li>
          <strong>Static Content:</strong> Use <code>v-persona</code> directive or <code>T</code> component
        </li>
        <li>
          <strong>Dynamic Content:</strong> Use <code>v-persona</code> directive, <code>TerminologyText</code> component, or computed properties with <code>replaceTerminology</code>
        </li>
        <li>
          <strong>API Data:</strong> Process all API responses through <code>replaceTerminology</code> before displaying
        </li>
        <li>
          <strong>Form Inputs:</strong> Use <code>:placeholder="$replaceTerminology('text')"</code> or <code>:placeholder="trans('text')"</code>
        </li>
        <li>
          <strong>HTML Attributes:</strong> Use binding with <code>$replaceTerminology</code> for attributes like title, aria-label
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { replaceTerminology } from '@/Utils/terminologyMapping';
import TerminologyText from '@/Components/TerminologyText.vue';

const testInput = ref('');
const processedText = ref('');
const replacementCount = ref(0);
const showDynamicContent = ref(false);
const dynamicContent = ref('Companies post Jobs for Candidates to apply. Employers can view Candidate profiles.');

// Comprehensive test cases
const testCases = ref([
  {
    name: 'Basic Terminology',
    input: 'Companies post Jobs for Candidates to apply.',
    expected: 'Organisations post Opportunities for Applicants to apply.',
    result: null,
    passed: false
  },
  {
    name: 'Mixed Case Handling',
    input: 'The COMPANY has a new JOB for a Candidate.',
    expected: 'The ORGANISATION has a new OPPORTUNITY for a Applicant.',
    result: null,
    passed: false
  },
  {
    name: 'Plural Forms',
    input: 'Companies have Jobs for Candidates.',
    expected: 'Organisations have Opportunities for Applicants.',
    result: null,
    passed: false
  },
  {
    name: 'Word Boundaries',
    input: 'MyCompany is not a Company but PreCandidate is not a Candidate.',
    expected: 'MyCompany is not a Organisation but PreCandidate is not a Applicant.',
    result: null,
    passed: false
  },
  {
    name: 'Sentence Structure',
    input: 'When a Candidate applies for a Job at a Company, the Employer reviews the application.',
    expected: 'When a Applicant applies for a Opportunity at a Organisation, the Organisation reviews the application.',
    result: null,
    passed: false
  }
]);

// Process the test input text
function processText() {
  if (!testInput.value) return;
  
  const original = testInput.value;
  const processed = replaceTerminology(original);
  processedText.value = processed;
  
  // Count replacements
  let count = 0;
  Object.keys(terminologyMap).forEach(term => {
    const regex = new RegExp(`\\b${term}\\b`, 'g');
    const matches = original.match(regex);
    if (matches) {
      count += matches.length;
    }
  });
  
  replacementCount.value = count;
}

// Run a specific test case
function runTest(index) {
  const testCase = testCases.value[index];
  testCase.result = replaceTerminology(testCase.input);
  testCase.passed = testCase.result === testCase.expected;
}

// Toggle dynamic content display
function toggleDynamicContent() {
  showDynamicContent.value = !showDynamicContent.value;
}

// Computed property for dynamic content with terminology replacement
const processedDynamicContent = computed(() => {
  return replaceTerminology(dynamicContent.value);
});
</script>
