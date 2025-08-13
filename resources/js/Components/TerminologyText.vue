<template>
  <component :is="tag" :class="className">
    <slot v-if="!text" :processText="processText"></slot>
    <template v-else>{{ processedText }}</template>
  </component>
</template>

<script setup>
import { computed } from 'vue';
import { replaceTerminology } from '@/Utils/terminologyMapping';

const props = defineProps({
  /**
   * The text to display with terminology replacement
   * If not provided, will use slot content
   */
  text: {
    type: String,
    default: null
  },
  
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

// Function to process text with terminology replacement
const processText = (text) => {
  return replaceTerminology(text);
};

// Apply terminology mapping to the text prop
const processedText = computed(() => {
  return processText(props.text);
});

// Pass through any classes
const className = computed(() => props.class);

// Expose the processText function to the slot
defineExpose({
  processText
});
</script>
