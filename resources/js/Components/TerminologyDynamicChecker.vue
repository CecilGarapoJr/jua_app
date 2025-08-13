<template>
  <div class="bg-white shadow-md rounded-lg p-6 mb-8">
    <h2 class="text-xl font-medium mb-4">Dynamic Content Terminology Check</h2>
    
    <div class="mb-4">
      <p class="mb-2">This tool tests terminology replacement in dynamically loaded UI elements like modals, popovers, and tooltips.</p>
    </div>
    
    <!-- Test Controls -->
    <div class="flex flex-wrap gap-2 mb-6">
      <button 
        @click="showModal = true" 
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
      >
        Open Test Modal
      </button>
      
      <button 
        @click="toggleTooltip" 
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
      >
        Toggle Test Tooltip
      </button>
      
      <button 
        @click="togglePopover" 
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
      >
        Toggle Test Popover
      </button>
    </div>
    
    <!-- Test Results -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="border rounded-md p-4">
        <h3 class="font-medium mb-2">Implementation Techniques</h3>
        <ul class="list-disc pl-5 space-y-2">
          <li>
            <strong>Reactive Content:</strong> Apply <code>v-persona</code> to dynamic content containers
          </li>
          <li>
            <strong>Modals:</strong> Use <code>v-persona</code> on modal content or <code>TerminologyText</code> for content
          </li>
          <li>
            <strong>Tooltips:</strong> Use <code>:title="$replaceTerminology('tooltip text')"</code>
          </li>
          <li>
            <strong>Popovers:</strong> Process content with <code>replaceTerminology</code> before setting
          </li>
          <li>
            <strong>Lazy-loaded Content:</strong> Apply <code>v-persona</code> to parent containers
          </li>
        </ul>
      </div>
      
      <div class="border rounded-md p-4">
        <h3 class="font-medium mb-2">Common Issues</h3>
        <ul class="list-disc pl-5 space-y-2">
          <li>
            <strong>Dynamic Updates:</strong> Content updated after initial render may not have terminology applied
          </li>
          <li>
            <strong>Third-party Components:</strong> May not support custom directives like <code>v-persona</code>
          </li>
          <li>
            <strong>Tooltips:</strong> Native tooltips using <code>title</code> attribute need binding with <code>$replaceTerminology</code>
          </li>
          <li>
            <strong>Async Content:</strong> Content loaded after component mounting needs manual processing
          </li>
        </ul>
      </div>
    </div>
    
    <!-- Tooltip Test Element -->
    <div class="relative mt-6">
      <div 
        ref="tooltipTarget"
        class="inline-block p-2 bg-gray-100 rounded border cursor-help"
      >
        Hover for tooltip
      </div>
      
      <div 
        v-if="showTooltip" 
        v-persona
        class="absolute z-10 p-2 bg-gray-800 text-white rounded text-sm max-w-xs"
        :style="tooltipStyle"
      >
        This tooltip contains terminology: Companies post Jobs for Candidates to apply. Employers can view Candidate profiles.
      </div>
    </div>
    
    <!-- Popover Test Element -->
    <div class="relative mt-6">
      <div 
        ref="popoverTarget"
        class="inline-block p-2 bg-gray-100 rounded border cursor-pointer"
        @click="togglePopover"
      >
        Click for popover
      </div>
      
      <div 
        v-if="showPopover" 
        class="absolute z-10 p-4 bg-white border shadow-lg rounded max-w-sm"
        :style="popoverStyle"
      >
        <h4 class="font-medium mb-2">Popover Title</h4>
        <p v-persona>
          This popover contains terminology: Companies post Jobs for Candidates to apply.
        </p>
        <p>
          <TerminologyText text="Employers can view Candidate profiles and post new Jobs." />
        </p>
      </div>
    </div>
    
    <!-- Test Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg max-w-lg w-full mx-4">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-medium">Test Modal</h3>
            <button @click="showModal = false" class="text-gray-500 hover:text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          <div class="mb-6">
            <h4 class="font-medium mb-2">Without Terminology Replacement</h4>
            <div class="p-3 bg-gray-50 rounded mb-4">
              Companies post Jobs for Candidates to apply. Employers can view Candidate profiles.
            </div>
            
            <h4 class="font-medium mb-2">With v-persona Directive</h4>
            <div v-persona class="p-3 bg-gray-50 rounded mb-4">
              Companies post Jobs for Candidates to apply. Employers can view Candidate profiles.
            </div>
            
            <h4 class="font-medium mb-2">With TerminologyText Component</h4>
            <TerminologyText 
              text="Companies post Jobs for Candidates to apply. Employers can view Candidate profiles."
              class="p-3 bg-gray-50 rounded"
            />
          </div>
          
          <div class="flex justify-end">
            <button 
              @click="showModal = false" 
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
            >
              Close Modal
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Recommendations -->
    <div class="mt-6 p-4 bg-gray-50 rounded">
      <h3 class="text-lg font-medium mb-2">Best Practices for Dynamic Content</h3>
      <ul class="list-disc pl-5 space-y-2">
        <li>
          <strong>Global Components:</strong> Create wrapper components for modals, tooltips, and popovers that apply terminology replacement
        </li>
        <li>
          <strong>Directive Hooks:</strong> The <code>v-persona</code> directive should be applied to the outermost container of dynamic content
        </li>
        <li>
          <strong>Watch for Updates:</strong> For content that updates frequently, use watchers to reapply terminology replacement
        </li>
        <li>
          <strong>Custom Events:</strong> Emit events after dynamic content is loaded to trigger terminology processing
        </li>
        <li>
          <strong>Composables:</strong> Create a composable that handles terminology replacement for dynamic content
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import TerminologyText from '@/Components/TerminologyText.vue';

const showModal = ref(false);
const showTooltip = ref(false);
const showPopover = ref(false);
const tooltipTarget = ref(null);
const popoverTarget = ref(null);
const tooltipStyle = ref({});
const popoverStyle = ref({});

// Toggle tooltip visibility
function toggleTooltip() {
  showTooltip.value = !showTooltip.value;
  if (showTooltip.value) {
    updateTooltipPosition();
  }
}

// Toggle popover visibility
function togglePopover() {
  showPopover.value = !showPopover.value;
  if (showPopover.value) {
    updatePopoverPosition();
  }
}

// Update tooltip position relative to target
function updateTooltipPosition() {
  if (!tooltipTarget.value) return;
  
  const rect = tooltipTarget.value.getBoundingClientRect();
  tooltipStyle.value = {
    top: `${rect.bottom + window.scrollY + 10}px`,
    left: `${rect.left + window.scrollX}px`
  };
}

// Update popover position relative to target
function updatePopoverPosition() {
  if (!popoverTarget.value) return;
  
  const rect = popoverTarget.value.getBoundingClientRect();
  popoverStyle.value = {
    top: `${rect.bottom + window.scrollY + 10}px`,
    left: `${rect.left + window.scrollX}px`
  };
}

// Event handlers
onMounted(() => {
  window.addEventListener('resize', handleResize);
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
  window.removeEventListener('scroll', handleScroll);
});

function handleResize() {
  if (showTooltip.value) updateTooltipPosition();
  if (showPopover.value) updatePopoverPosition();
}

function handleScroll() {
  if (showTooltip.value) updateTooltipPosition();
  if (showPopover.value) updatePopoverPosition();
}
</script>
