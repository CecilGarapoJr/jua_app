/**
 * Terminology Audit Tool
 * 
 * This script helps identify potential places in the codebase where terminology
 * might not be properly replaced by the terminology replacement system.
 */

import { terminologyMap } from './terminologyMapping';

// Terms that should be checked for proper replacement
const termsToCheck = Object.keys(terminologyMap);

/**
 * Check if a string contains any terms that should be replaced
 * @param {string} text - Text to check for terminology terms
 * @returns {Array} - Array of terms found in the text
 */
export function findUnreplacedTerms(text) {
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

/**
 * Check if an element might be missing terminology replacement
 * @param {HTMLElement} element - DOM element to check
 * @returns {Object} - Results of the check
 */
export function checkElementForTerminology(element) {
  // Skip elements that already have terminology handling
  if (element.hasAttribute('v-persona') || 
      element.tagName.toLowerCase() === 't' || 
      element.closest('terminology-text')) {
    return { safe: true, element, terms: [] };
  }
  
  // Check text content for terminology terms
  const terms = findUnreplacedTerms(element.textContent);
  
  // Check attributes that might contain text
  const attributesToCheck = ['placeholder', 'title', 'aria-label'];
  attributesToCheck.forEach(attr => {
    if (element.hasAttribute(attr)) {
      const attrTerms = findUnreplacedTerms(element.getAttribute(attr));
      terms.push(...attrTerms);
    }
  });
  
  return {
    safe: terms.length === 0,
    element,
    terms: [...new Set(terms)] // Remove duplicates
  };
}

/**
 * Scan the DOM for potential terminology issues
 * @returns {Array} - Array of elements with potential issues
 */
export function scanDomForTerminologyIssues() {
  const results = [];
  const elements = document.querySelectorAll('*');
  
  elements.forEach(element => {
    const checkResult = checkElementForTerminology(element);
    if (!checkResult.safe) {
      results.push(checkResult);
    }
  });
  
  return results;
}

/**
 * Format the results of a terminology scan for display
 * @param {Array} results - Results from scanDomForTerminologyIssues
 * @returns {string} - Formatted results
 */
export function formatScanResults(results) {
  if (results.length === 0) {
    return 'No terminology issues found.';
  }
  
  let output = `Found ${results.length} potential terminology issues:\n\n`;
  
  results.forEach((result, index) => {
    const elementPreview = result.element.outerHTML.substring(0, 100) + 
                          (result.element.outerHTML.length > 100 ? '...' : '');
    
    output += `${index + 1}. Terms: ${result.terms.join(', ')}\n`;
    output += `   Element: ${elementPreview}\n\n`;
  });
  
  return output;
}

/**
 * Run a terminology audit on the current page
 * @returns {Object} - Audit results
 */
export function runTerminologyAudit() {
  const issues = scanDomForTerminologyIssues();
  const formattedResults = formatScanResults(issues);
  
  return {
    issueCount: issues.length,
    issues,
    formattedResults
  };
}

// Export a function to add the audit tool to the window for easy console access
export function installTerminologyAuditTool() {
  window.terminologyAudit = {
    run: runTerminologyAudit,
    findUnreplacedTerms,
    checkElementForTerminology,
    scanDomForTerminologyIssues,
    formatScanResults
  };
  
  console.log('Terminology Audit Tool installed. Run window.terminologyAudit.run() to check for issues.');
}

export default {
  findUnreplacedTerms,
  checkElementForTerminology,
  scanDomForTerminologyIssues,
  formatScanResults,
  runTerminologyAudit,
  installTerminologyAuditTool
};
