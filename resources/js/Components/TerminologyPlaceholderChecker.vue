<template>
  <div class="bg-white shadow-md rounded-lg p-6 mb-8">
    <h2 class="text-xl font-medium mb-4">Placeholder Terminology Check</h2>
    
    <div class="mb-4">
      <p class="mb-2">This tool checks for input placeholders that might contain terminology that should be replaced.</p>
      <button 
        @click="checkPlaceholders" 
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
      >
        Check Placeholders
      </button>
    </div>
    
    <div v-if="results.length > 0" class="mt-4">
      <h3 class="text-lg font-medium mb-2">Found {{ results.length }} placeholders with terminology:</h3>
      
      <div class="bg-gray-50 p-4 rounded overflow-auto max-h-96">
        <div v-for="(result, index) in results" :key="index" class="mb-4 pb-4 border-b border-gray-200 last:border-0">
          <div class="font-medium">Issue #{{ index + 1 }}</div>
          <div class="mb-1"><span class="font-medium">Terms:</span> {{ result.terms.join(', ') }}</div>
          <div class="mb-1">
            <span class="font-medium">Original Placeholder:</span> "{{ result.originalPlaceholder }}"
          </div>
          <div class="mb-1">
            <span class="font-medium">Suggested Replacement:</span> "{{ result.suggestedReplacement }}"
          </div>
          <div>
            <span class="font-medium">Element:</span>
            <pre class="mt-1 p-2 bg-gray-100 rounded text-sm overflow-x-auto">{{ getElementPreview(result.element) }}</pre>
          </div>
        </div>
      </div>
      
      <div class="mt-4">
        <h3 class="text-lg font-medium mb-2">Recommended Fixes:</h3>
        <div class="bg-gray-50 p-4 rounded">
          <p class="mb-2">For each placeholder, use one of these approaches:</p>
          <ol class="list-decimal pl-5 space-y-2">
            <li>Use the <code>trans()</code> function with terminology replacement: <code>:placeholder="trans('Enter Job Title')"</code></li>
            <li>Use the <code>$replaceTerminology</code> method: <code>:placeholder="$replaceTerminology('Enter Job Title')"</code></li>
            <li>Add the <code>v-persona</code> directive to the input element</li>
          </ol>
        </div>
      </div>
    </div>
    
    <div v-else-if="checked" class="text-green-600 font-medium mt-4">
      No placeholder terminology issues found on this page!
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { terminologyMap } from '@/Utils/terminologyMapping';

const results = ref([]);
const checked = ref(false);

// Terms that should be checked for proper replacement
const termsToCheck = Object.keys(terminologyMap);

// Check if a string contains any terms that should be replaced
function findUnreplacedTerms(text) {
  if (!text || typeof text !== 'string') return [];
  
  const foundTerms = [];
  
  termsToCheck.forEach(term => {
    const regex = new RegExp(`\\b${term}\\b`, 'g');
    if (regex.test(text)) {
      foundTerms.push(term);
    }
  });
  
  return foundTerms;
}

// Replace terminology in a string
function replaceTerminology(text) {
  if (!text) return text;
  
  let result = text;
  Object.entries(terminologyMap).forEach(([oldTerm, newTerm]) => {
    const regex = new RegExp(`\\b${oldTerm}\\b`, 'g');
    result = result.replace(regex, newTerm);
  });
  
  return result;
}

// Check all input elements on the page for placeholders with terminology
function checkPlaceholders() {
  results.value = [];
  checked.value = true;
  
  const inputs = document.querySelectorAll('input, textarea');
  
  inputs.forEach(input => {
    const placeholder = input.getAttribute('placeholder');
    if (placeholder) {
      const terms = findUnreplacedTerms(placeholder);
      
      if (terms.length > 0) {
        results.value.push({
          element: input,
          originalPlaceholder: placeholder,
          suggestedReplacement: replaceTerminology(placeholder),
          terms
        });
      }
    }
  });
}

// Get a preview of an element for display
function getElementPreview(element) {
  if (!element) return 'Element not available';
  
  const outerHTML = element.outerHTML || '';
  return outerHTML.length > 200 
    ? outerHTML.substring(0, 200) + '...' 
    : outerHTML;
}
</script>
