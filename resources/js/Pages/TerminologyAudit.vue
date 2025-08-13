<template>
  <div class="container mx-auto py-8 px-4">
    <h1 class="text-3xl font-semibold mb-6">Terminology Audit Tool</h1>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
      <h2 class="text-xl font-medium mb-4">Terminology Map</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
          <thead>
            <tr>
              <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Original Term</th>
              <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Replacement Term</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(newTerm, oldTerm) in terminologyMap" :key="oldTerm" class="hover:bg-gray-50">
              <td class="py-2 px-4 border-b border-gray-200">{{ oldTerm }}</td>
              <td class="py-2 px-4 border-b border-gray-200">{{ newTerm }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Placeholder Checker Component -->
    <TerminologyPlaceholderChecker />
    
    <!-- Attribute Checker Component -->
    <TerminologyAttributeChecker />
    
    <!-- Route Checker Component -->
    <TerminologyRouteChecker />
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
      <h2 class="text-xl font-medium mb-4">DOM Content Audit</h2>
      <p class="mb-4">Click the button below to scan the current page for potential terminology issues in DOM content.</p>
      <button 
        @click="runAudit" 
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
      >
        Run Content Audit
      </button>
    </div>
    
    <div v-if="auditResults" class="bg-white shadow-md rounded-lg p-6 mb-8">
      <h2 class="text-xl font-medium mb-4">Audit Results</h2>
      <div class="mb-4">
        <span class="font-medium">Issues Found:</span> {{ auditResults.issueCount }}
      </div>
      
      <div v-if="auditResults.issueCount > 0">
        <h3 class="text-lg font-medium mb-2">Potential Issues:</h3>
        <div class="bg-gray-50 p-4 rounded overflow-auto max-h-96">
          <div v-for="(issue, index) in auditResults.issues" :key="index" class="mb-4 pb-4 border-b border-gray-200 last:border-0">
            <div class="font-medium">Issue #{{ index + 1 }}</div>
            <div class="mb-1"><span class="font-medium">Terms:</span> {{ issue.terms.join(', ') }}</div>
            <div>
              <span class="font-medium">Element:</span>
              <pre class="mt-1 p-2 bg-gray-100 rounded text-sm overflow-x-auto">{{ getElementPreview(issue.element) }}</pre>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-green-600 font-medium">
        No terminology issues found on this page!
      </div>
    </div>
    
    <!-- API Processor Component -->
    <TerminologyApiProcessor />
    
    <!-- Terminology Tester Component -->
    <TerminologyTester />
    
    <!-- Dynamic Content Checker Component -->
    <TerminologyDynamicChecker />
    
    <!-- Translation Check -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
      <h2 class="text-xl font-medium mb-4">Translation System Check</h2>
      <p class="mb-4">This section tests if the terminology replacement is properly applied to translated content.</p>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="border rounded-md p-4">
          <h3 class="font-medium mb-2">Original Translation</h3>
          <p>{{ trans('Companies post Jobs for Candidates to apply.') }}</p>
          <p>{{ trans('Employers can view Candidate profiles.') }}</p>
        </div>
        
        <div class="border rounded-md p-4">
          <h3 class="font-medium mb-2">Manual Replacement</h3>
          <p>{{ manuallyReplace('Companies post Jobs for Candidates to apply.') }}</p>
          <p>{{ manuallyReplace('Employers can view Candidate profiles.') }}</p>
        </div>
      </div>
      
      <div class="mt-4 p-4 bg-gray-50 rounded">
        <p v-if="transSystemWorking" class="text-green-600 font-medium">
          ✓ Translation system is correctly applying terminology replacement!
        </p>
        <p v-else class="text-red-600 font-medium">
          ✗ Translation system is NOT correctly applying terminology replacement!
        </p>
      </div>
    </div>
    
    <!-- Test Components Section -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
      <h2 class="text-xl font-medium mb-4">Test Components</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Without Terminology Replacement -->
        <div class="border rounded-md p-4">
          <h3 class="font-medium mb-2">Without Replacement</h3>
          <p>Companies post Jobs for Candidates to apply.</p>
          <p>Employers can view Candidate profiles.</p>
          <input type="text" placeholder="Enter Job Title" class="mt-2 border rounded p-2 w-full" />
        </div>
        
        <!-- With v-persona Directive -->
        <div class="border rounded-md p-4">
          <h3 class="font-medium mb-2">With v-persona</h3>
          <p v-persona>Companies post Jobs for Candidates to apply.</p>
          <p v-persona>Employers can view Candidate profiles.</p>
          <input type="text" v-persona placeholder="Enter Job Title" class="mt-2 border rounded p-2 w-full" />
        </div>
        
        <!-- With TerminologyText Component -->
        <div class="border rounded-md p-4">
          <h3 class="font-medium mb-2">With TerminologyText</h3>
          <TerminologyText tag="p" text="Companies post Jobs for Candidates to apply." />
          <TerminologyText tag="p" text="Employers can view Candidate profiles." />
          <input type="text" :placeholder="$replaceTerminology('Enter Job Title')" class="mt-2 border rounded p-2 w-full" />
        </div>
        
        <!-- With T Component -->
        <div class="border rounded-md p-4">
          <h3 class="font-medium mb-2">With T Component</h3>
          <T tag="p">Companies post Jobs for Candidates to apply.</T>
          <T tag="p">Employers can view Candidate profiles.</T>
          <input type="text" :placeholder="$replaceTerminology('Enter Job Title')" class="mt-2 border rounded p-2 w-full" />
        </div>
      </div>
    </div>
    
    <!-- Recommendations -->
    <div class="bg-white shadow-md rounded-lg p-6">
      <h2 class="text-xl font-medium mb-4">Recommendations for Complete Terminology Replacement</h2>
      
      <div class="space-y-4">
        <div>
          <h3 class="text-lg font-medium mb-2">1. Text Content</h3>
          <ul class="list-disc pl-5 space-y-1">
            <li>Use the <code>v-persona</code> directive on elements with static text</li>
            <li>Use the <code>T</code> component for slot-based content</li>
            <li>Use <code>TerminologyText</code> for prop-based text</li>
            <li>Always use <code>trans()</code> for translated text</li>
          </ul>
        </div>
        
        <div>
          <h3 class="text-lg font-medium mb-2">2. Input Placeholders</h3>
          <ul class="list-disc pl-5 space-y-1">
            <li>Use <code>:placeholder="$replaceTerminology('Your placeholder')"</code></li>
            <li>Or use <code>:placeholder="trans('Your placeholder')"</code></li>
          </ul>
        </div>
        
        <div>
          <h3 class="text-lg font-medium mb-2">3. Dynamic Content</h3>
          <ul class="list-disc pl-5 space-y-1">
            <li>Process API responses through <code>replaceTerminology()</code> before displaying</li>
            <li>Use computed properties with <code>replaceTerminology()</code> for dynamic content</li>
          </ul>
        </div>
        
        <div>
          <h3 class="text-lg font-medium mb-2">4. Common Issues</h3>
          <ul class="list-disc pl-5 space-y-1">
            <li>Hardcoded placeholders in input fields</li>
            <li>Text in attributes like <code>title</code> and <code>aria-label</code></li>
            <li>Dynamic content from API responses</li>
            <li>Content in theme components (headers, footers, navigation)</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { terminologyMap, replaceTerminology } from '@/Utils/terminologyMapping';
import terminologyAudit from '@/Utils/terminologyAudit';
import TerminologyPlaceholderChecker from '@/Components/TerminologyPlaceholderChecker.vue';
import TerminologyAttributeChecker from '@/Components/TerminologyAttributeChecker.vue';
import TerminologyApiProcessor from '@/Components/TerminologyApiProcessor.vue';
import TerminologyRouteChecker from '@/Components/TerminologyRouteChecker.vue';
import TerminologyTester from '@/Components/TerminologyTester.vue';
import TerminologyDynamicChecker from '@/Components/TerminologyDynamicChecker.vue';

const auditResults = ref(null);

// Run the terminology audit on the current page
function runAudit() {
  auditResults.value = terminologyAudit.runTerminologyAudit();
}

// Get a preview of an element for display
function getElementPreview(element) {
  if (!element) return 'Element not available';
  
  const outerHTML = element.outerHTML || '';
  return outerHTML.length > 200 
    ? outerHTML.substring(0, 200) + '...' 
    : outerHTML;
}

// Manually replace terminology in a string for comparison
function manuallyReplace(text) {
  return replaceTerminology(text);
}

// Check if the translation system is correctly applying terminology replacement
const transSystemWorking = computed(() => {
  const original = 'Companies post Jobs for Candidates to apply.';
  const translated = trans(original);
  const manuallyReplaced = manuallyReplace(original);
  
  return translated === manuallyReplaced;
});

// Install the terminology audit tool on the window object for console access
if (typeof window !== 'undefined') {
  terminologyAudit.installTerminologyAuditTool();
}
</script>
