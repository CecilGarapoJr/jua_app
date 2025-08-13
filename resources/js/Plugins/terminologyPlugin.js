import { terminologyMap, replaceTerminology } from '@/Utils/terminologyMapping';

/**
 * Vue plugin to handle terminology changes throughout the application
 * This plugin overrides the global trans function to apply terminology mapping
 */
export default {
  install(app) {
    // Store the original trans function
    const originalTrans = window.trans;
    
    // Override the global trans function to apply terminology mapping
    window.trans = function(key, params = {}) {
      // Get the original translation
      const translated = originalTrans ? originalTrans(key, params) : key;
      
      // Apply terminology mapping
      return replaceTerminology(translated);
    };
    
    // Add a global property for direct access in components
    app.config.globalProperties.$terminology = terminologyMap;
    
    // Add a global method to replace terminology in any string
    app.config.globalProperties.$replaceTerminology = replaceTerminology;
  }
};
