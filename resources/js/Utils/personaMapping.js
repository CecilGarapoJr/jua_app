/**
 * Utility for mapping between backend and frontend user persona terminology
 * Backend: User/Employer/Company/Candidate/Job (original case study terms)
 * Frontend: Seeker/Organisation/Applicant/Opportunity (new terms for UI display)
 *
 * @deprecated Use terminologyMapping.js instead for consistent terminology replacement
 */

// Mapping for text replacement
export const personaMap = {
  // Original persona mappings
  'User': 'Seeker',
  'user': 'seeker',
  'Users': 'Seekers',
  'users': 'seekers',
  
  // New terminology mappings
  'Employer': 'Organisation',
  'employer': 'organisation',
  'Employers': 'Organisations',
  'employers': 'organisations',
  
  'Candidate': 'Applicant',
  'candidate': 'applicant',
  'Candidates': 'Applicants',
  'candidates': 'applicants',
  
  'Company': 'Organisation',
  'company': 'organisation',
  'Companies': 'Organisations',
  'companies': 'organisations',
  
  'Job': 'Opportunity',
  'job': 'opportunity',
  'Jobs': 'Opportunities',
  'jobs': 'opportunities'
};

// Import the consistent terminology mapping function
import { replaceTerminology } from './terminologyMapping';

/**
 * Replace persona terms in text for frontend display
 * @param {string} text - Original text with backend terminology
 * @return {string} - Text with frontend terminology
 * @deprecated Use replaceTerminology from terminologyMapping.js instead
 */
export const displayText = (text) => {
  if (!text || typeof text !== 'string') return text;
  
  // Use the consistent terminology mapping function
  return replaceTerminology(text);
};

/**
 * Vue directive for automatic text replacement
 * Usage: v-persona directive implementation
 */
export const personaDirective = {
  // Called when the bound element's parent component is mounted
  mounted(el) {
    // Store original content to avoid processing already processed content
    if (!el._original) {
      el._original = el.innerHTML;
    }
    el.innerHTML = replaceTerminology(el._original);
  },
  
  // Called when the bound element's parent component is updated
  updated(el) {
    // Update original content if it changes
    if (el._original !== el.innerHTML) {
      el._original = el.innerHTML;
    }
    el.innerHTML = replaceTerminology(el._original);
  }
};

/**
 * Register the persona directive globally
 * @param {Object} app - Vue application instance
 */
export const registerPersonaDirective = (app) => {
  app.directive('persona', personaDirective);
};
