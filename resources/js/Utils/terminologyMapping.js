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
  
  // Opportunity-specific terms
  'Full Time Job': 'Full Time Opportunity',
  'full time job': 'full time opportunity',
  'Part Time Job': 'Part Time Opportunity',
  'part time job': 'part time opportunity',
  'Contract Job': 'Contract Opportunity',
  'contract job': 'contract opportunity',
  'Temporary Job': 'Temporary Opportunity',
  'temporary job': 'temporary opportunity',
  'Internship': 'Internship Opportunity',
  'internship': 'internship opportunity',
  'Scholarship': 'Scholarship Opportunity',
  'scholarship': 'scholarship opportunity',
  'Grant': 'Grant Opportunity',
  'grant': 'grant opportunity',
  'Training': 'Training Opportunity',
  'training': 'training opportunity',
  
  // Field-specific terms
  'Salary': 'Value',
  'salary': 'value',
  'Salary Range': 'Value Range',
  'salary range': 'value range',
  'Experience': 'Eligibility Criteria',
  'experience': 'eligibility criteria',
  'Job Description': 'Opportunity Description',
  'job description': 'opportunity description',
  'Job Requirements': 'Opportunity Requirements',
  'job requirements': 'opportunity requirements',
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
