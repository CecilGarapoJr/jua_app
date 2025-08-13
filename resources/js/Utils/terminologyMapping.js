/**
 * Terminology mapping for the application
 * This file centralizes all terminology changes to ensure consistency
 */

export const terminologyMap = {
  // Singular forms
  'Company': 'Organisation',
  'company': 'organisation',
  'Candidate': 'Applicant',
  'candidate': 'applicant',
  'Employer': 'Organisation',
  'employer': 'organisation',
  'Job': 'Opportunity',
  'job': 'opportunity',
  
  // Plural forms
  'Companies': 'Organisations',
  'companies': 'organisations',
  'Candidates': 'Applicants',
  'candidates': 'applicants',
  'Employers': 'Organisations',
  'employers': 'organisations',
  'Jobs': 'Opportunities',
  'jobs': 'opportunities',
};

/**
 * Replace terminology in a string based on the mapping
 * @param {string} text - The text to process
 * @return {string} - The processed text with updated terminology
 */
export function replaceTerminology(text) {
  if (!text) return text;
  
  let result = text;
  Object.entries(terminologyMap).forEach(([oldTerm, newTerm]) => {
    // Use word boundary to avoid partial replacements
    const regex = new RegExp(`\\b${oldTerm}\\b`, 'g');
    result = result.replace(regex, newTerm);
  });
  
  return result;
}

/**
 * Custom translation function that applies terminology mapping
 * @param {string} key - The translation key
 * @param {Object} params - Translation parameters
 * @return {string} - The translated text with updated terminology
 */
export function transWithTerminology(key, params = {}) {
  // First get the regular translation
  const translated = window.trans ? window.trans(key, params) : key;
  
  // Then apply terminology mapping
  return replaceTerminology(translated);
}

export default {
  terminologyMap,
  replaceTerminology,
  transWithTerminology
};
