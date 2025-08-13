<template>
  <component :is="tag" :class="className" ref="componentEl">
    <slot />
  </component>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { replaceTerminology } from '@/Utils/terminologyMapping';

const props = defineProps({
  /**
   * HTML tag to render
   */
  tag: {
    type: String,
    default: 'span'
  },
  
  /**
   * CSS classes to apply
   */
  class: {
    type: String,
    default: ''
  }
});

// Pass through any classes
const className = computed(() => props.class);

// Reference to the component element
const componentEl = ref(null);

// Process the slot content with terminology replacement
const processSlotContent = () => {
  if (componentEl.value) {
    componentEl.value.innerHTML = replaceTerminology(componentEl.value.innerHTML);
  }
};

// Process content after mounting and when slot content changes
onMounted(() => {
  processSlotContent();
});

// Watch for slot content changes
watch(() => componentEl.value?.innerHTML, () => {
  processSlotContent();
});
</script>
