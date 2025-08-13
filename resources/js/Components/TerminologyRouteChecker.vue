<template>
  <div class="bg-white shadow-md rounded-lg p-6 mb-8">
    <h2 class="text-xl font-medium mb-4">Route Terminology Check</h2>
    
    <div class="mb-4">
      <p class="mb-2">This tool checks for inconsistent terminology in route names and link URLs.</p>
      <div class="flex flex-wrap gap-2">
        <button 
          @click="checkRoutes" 
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
        >
          Check Routes
        </button>
        <button 
          @click="checkLinks" 
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
        >
          Check Links
        </button>
      </div>
    </div>
    
    <!-- Routes Check Results -->
    <div v-if="routeResults && routeResults.success" class="mt-6">
      <h3 class="text-lg font-medium mb-2">Route Check Results:</h3>
      
      <div v-if="routeResults.inconsistencyCount > 0" class="bg-gray-50 p-4 rounded overflow-auto max-h-96">
        <div class="text-amber-600 font-medium mb-2">
          Found {{ routeResults.inconsistencyCount }} route inconsistencies
        </div>
        
        <div v-for="(issue, index) in routeResults.inconsistencies" :key="'route-' + index" class="mb-4 pb-4 border-b border-gray-200 last:border-0">
          <div class="font-medium">Issue #{{ index + 1 }}</div>
          <div class="mb-1"><span class="font-medium">Route Name:</span> {{ issue.routeName }}</div>
          <div class="mb-1"><span class="font-medium">Term to Replace:</span> "{{ issue.oldTerm }}"</div>
          <div class="mb-1"><span class="font-medium">Replacement Term:</span> "{{ issue.newTerm }}"</div>
          <div class="mb-1">
            <span class="font-medium">Suggested Route Name:</span> {{ issue.suggestion }}
          </div>
        </div>
      </div>
      
      <div v-else class="text-green-600 font-medium">
        No route terminology inconsistencies found!
      </div>
    </div>
    
    <!-- Links Check Results -->
    <div v-if="linkResults && linkResults.success" class="mt-6">
      <h3 class="text-lg font-medium mb-2">Link Check Results:</h3>
      
      <div v-if="linkResults.inconsistencyCount > 0" class="bg-gray-50 p-4 rounded overflow-auto max-h-96">
        <div class="text-amber-600 font-medium mb-2">
          Found {{ linkResults.inconsistencyCount }} link inconsistencies
        </div>
        
        <div v-for="(issue, index) in linkResults.inconsistencies" :key="'link-' + index" class="mb-4 pb-4 border-b border-gray-200 last:border-0">
          <div class="font-medium">Issue #{{ index + 1 }}</div>
          <div class="mb-1"><span class="font-medium">Type:</span> {{ issue.type === 'link_href' ? 'Link URL' : 'Link Text' }}</div>
          <div v-if="issue.type === 'link_href'" class="mb-1">
            <span class="font-medium">URL:</span> {{ issue.href }}
          </div>
          <div v-else class="mb-1">
            <span class="font-medium">Text:</span> {{ issue.text }}
          </div>
          <div class="mb-1"><span class="font-medium">Term to Replace:</span> "{{ issue.oldTerm }}"</div>
          <div class="mb-1"><span class="font-medium">Replacement Term:</span> "{{ issue.newTerm }}"</div>
          <div class="mb-1">
            <span class="font-medium">Suggested Value:</span> {{ issue.suggestion }}
          </div>
          <div>
            <span class="font-medium">Element:</span>
            <pre class="mt-1 p-2 bg-gray-100 rounded text-sm overflow-x-auto">{{ getElementPreview(issue.element) }}</pre>
          </div>
        </div>
      </div>
      
      <div v-else class="text-green-600 font-medium">
        No link terminology inconsistencies found!
      </div>
    </div>
    
    <div class="mt-6 p-4 bg-gray-50 rounded">
      <h3 class="text-lg font-medium mb-2">Recommendations:</h3>
      <ul class="list-disc pl-5 space-y-2">
        <li>
          <strong>Route Names:</strong> Use the enhanced route mapping system for all route generation
          <pre class="mt-1 p-2 bg-gray-100 rounded text-sm">$route('opportunities.index') // Instead of route('jobs.index')</pre>
        </li>
        <li>
          <strong>Link URLs:</strong> Generate links using the enhanced route function
          <pre class="mt-1 p-2 bg-gray-100 rounded text-sm">&lt;a :href="$route('opportunities.show', opportunity)"&gt;View Opportunity&lt;/a&gt;</pre>
        </li>
        <li>
          <strong>Link Text:</strong> Apply terminology replacement to link text
          <pre class="mt-1 p-2 bg-gray-100 rounded text-sm">&lt;a v-persona&gt;View Job Details&lt;/a&gt;</pre>
        </li>
        <li>
          <strong>Navigation Components:</strong> Ensure all navigation components use the enhanced route function
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { auditAllRoutes, auditDomLinks } from '@/Utils/routeMapping';

const routeResults = ref(null);
const linkResults = ref(null);

// Check routes for terminology inconsistencies
function checkRoutes() {
  routeResults.value = auditAllRoutes();
}

// Check links for terminology inconsistencies
function checkLinks() {
  linkResults.value = auditDomLinks();
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
