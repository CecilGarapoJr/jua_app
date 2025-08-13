/**
 * Global component registration
 * This file registers components that should be available throughout the application
 */

import TerminologyText from './TerminologyText.vue';
import T from './T.vue';

/**
 * Register global components
 * @param {Object} app - Vue application instance
 */
export function registerGlobalComponents(app) {
  // Register the TerminologyText component globally
  app.component('TerminologyText', TerminologyText);
  
  // Register the T component for slot-based terminology replacement
  app.component('T', T);
}

export default {
  registerGlobalComponents
};
