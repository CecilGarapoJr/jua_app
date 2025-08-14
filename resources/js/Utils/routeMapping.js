/**
 * Route mapping utility to handle frontend naming changes while preserving backend routes
 * This allows us to use the new terminology in the frontend while keeping the original terms in the backend
 * 
 * Original terms: User, Employer, Company, Candidate, Job
 * New terms: Seeker, Organisation, Applicant, Opportunity
 */

// Import terminology mapping to ensure consistency
import { terminologyMap } from './terminologyMapping';

/**
 * Map frontend route names to backend route names
 * @param {string} routeName - Frontend route name with new terminology
 * @return {string} - Backend route name with original terminology
 */
export const mapRouteToBackend = (routeName) => {
  if (!routeName || typeof routeName !== 'string') return routeName;
  
  return routeName
    // Original mappings
    .replace(/seeker\./g, 'user.')
    
    // New terminology mappings
    .replace(/organisation\./g, 'employer.')
    .replace(/applicant\./g, 'candidate.')
    .replace(/opportunity\./g, 'job.')
    .replace(/opportunities\./g, 'jobs.')
    .replace(/applicants\./g, 'candidates.')
    .replace(/organisations\./g, 'companies.')
    
    // Opportunity type mappings
    .replace(/opportunity-job-full-time/g, 'job-full-time')
    .replace(/opportunity-job-part-time/g, 'job-part-time')
    .replace(/opportunity-job-contract/g, 'job-contract')
    .replace(/opportunity-job-temporary/g, 'job-temporary')
    .replace(/opportunity-internship/g, 'job-internship')
    .replace(/opportunity-scholarship/g, 'scholarship')
    .replace(/opportunity-grant/g, 'grant')
    .replace(/opportunity-training/g, 'training');
};

/**
 * Map backend route names to frontend route names
 * @param {string} routeName - Backend route name with original terminology
 * @return {string} - Frontend route name with new terminology
 */
export const mapRouteToFrontend = (routeName) => {
  if (!routeName || typeof routeName !== 'string') return routeName;
  
  return routeName
    // Original mappings
    .replace(/user\./g, 'seeker.')
    
    // New terminology mappings
    .replace(/employer\./g, 'organisation.')
    .replace(/candidate\./g, 'applicant.')
    .replace(/job\./g, 'opportunity.')
    .replace(/jobs\./g, 'opportunities.')
    .replace(/candidates\./g, 'applicants.')
    .replace(/companies\./g, 'organisations.')
    
    // Opportunity type mappings
    .replace(/job-full-time/g, 'opportunity-job-full-time')
    .replace(/job-part-time/g, 'opportunity-job-part-time')
    .replace(/job-contract/g, 'opportunity-job-contract')
    .replace(/job-temporary/g, 'opportunity-job-temporary')
    .replace(/job-internship/g, 'opportunity-internship')
    .replace(/scholarship/g, 'opportunity-scholarship')
    .replace(/grant/g, 'opportunity-grant')
    .replace(/training/g, 'opportunity-training');
};

/**
 * Enhanced route function that handles frontend/backend route name mapping
 * Maps new terminology in the frontend to original terminology in the backend
 * @param {string} name - Route name (can be frontend or backend)
 * @param {Object} params - Route parameters
 * @param {Object} options - Route options
 * @return {string} - Generated route URL
 */
export const enhancedRoute = (name, params = {}, options = {}) => {
  // Map frontend route names to backend route names
  const backendRouteName = mapRouteToBackend(name);
  
  // Use the original route function with the mapped name
  return window.route(backendRouteName, params, options);
};

/**
 * Register the enhanced route function globally
 * @param {Object} app - Vue application instance
 */
export const registerEnhancedRoute = (app) => {
  app.config.globalProperties.$route = enhancedRoute;
};

/**
 * Route terminology mapping for audit purposes
 * Maps original terms to new terminology terms in routes
 */
export const routeTerminologyMap = {
  // Core route mappings
  'job': 'opportunity',
  'jobs': 'opportunities',
  'candidate': 'applicant',
  'candidates': 'applicants',
  'company': 'organisation',
  'companies': 'organisations',
  'employer': 'organisation',
  'employers': 'organisations',
  
  // Specific route segment mappings
  'job-categories': 'opportunity-categories',
  'job-services': 'opportunity-services',
  'job-application': 'opportunity-application',
  'job-applicants': 'opportunity-applicants',
  'job-reviews': 'opportunity-reviews',
  'candidate-reviews': 'applicant-reviews',
  'company-reviews': 'organisation-reviews',
  
  // New opportunity-specific route mappings
  'job-full-time': 'opportunity-job-full-time',
  'job-part-time': 'opportunity-job-part-time',
  'job-contract': 'opportunity-job-contract',
  'job-temporary': 'opportunity-job-temporary',
  'job-internship': 'opportunity-internship',
  'scholarship': 'opportunity-scholarship',
  'grant': 'opportunity-grant',
  'training': 'opportunity-training',
  'saved-candidates': 'saved-applicants',
  'hire-candidate': 'hire-applicant'
};

/**
 * Audit routes for terminology consistency
 * @param {Array} routes - Array of route objects or route names to audit
 * @return {Object} - Audit results with inconsistencies found
 */
export const auditRoutes = (routes) => {
  if (!routes || !Array.isArray(routes)) {
    console.error('Routes must be provided as an array');
    return { success: false, message: 'Routes must be provided as an array' };
  }
  
  const inconsistencies = [];
  const routeNames = routes.map(route => typeof route === 'string' ? route : route.name);
  
  // Check for inconsistent terminology in route names
  routeNames.forEach(routeName => {
    if (!routeName) return;
    
    // Check for original terminology in route names that should use new terminology
    Object.entries(routeTerminologyMap).forEach(([oldTerm, newTerm]) => {
      const regex = new RegExp(`\\b${oldTerm}\\b`, 'g');
      if (regex.test(routeName)) {
        inconsistencies.push({
          routeName,
          type: 'route_name',
          oldTerm,
          newTerm,
          suggestion: routeName.replace(regex, newTerm)
        });
      }
    });
  });
  
  return {
    success: true,
    inconsistencyCount: inconsistencies.length,
    inconsistencies
  };
};

/**
 * Audit all registered routes in the application
 * @return {Object} - Audit results with inconsistencies found
 */
export const auditAllRoutes = () => {
  if (typeof window === 'undefined' || !window.route || !window.route().routes) {
    return { success: false, message: 'Route function not available or no routes registered' };
  }
  
  const allRoutes = Object.values(window.route().routes);
  return auditRoutes(allRoutes);
};

/**
 * Audit links in the DOM for terminology consistency
 * @return {Object} - Audit results with inconsistencies found
 */
export const auditDomLinks = () => {
  if (typeof document === 'undefined') {
    return { success: false, message: 'Document not available' };
  }
  
  const inconsistencies = [];
  const links = document.querySelectorAll('a[href]');
  
  links.forEach(link => {
    const href = link.getAttribute('href');
    if (!href) return;
    
    // Check for original terminology in URLs that should use new terminology
    Object.entries(routeTerminologyMap).forEach(([oldTerm, newTerm]) => {
      const regex = new RegExp(`\\b${oldTerm}\\b`, 'g');
      if (regex.test(href)) {
        inconsistencies.push({
          element: link,
          href,
          type: 'link_href',
          oldTerm,
          newTerm,
          suggestion: href.replace(regex, newTerm)
        });
      }
    });
    
    // Check link text for terminology inconsistencies
    const linkText = link.textContent;
    if (linkText) {
      Object.entries(terminologyMap).forEach(([oldTerm, newTerm]) => {
        const regex = new RegExp(`\\b${oldTerm}\\b`, 'g');
        if (regex.test(linkText)) {
          inconsistencies.push({
            element: link,
            text: linkText,
            type: 'link_text',
            oldTerm,
            newTerm,
            suggestion: linkText.replace(regex, newTerm)
          });
        }
      });
    }
  });
  
  return {
    success: true,
    inconsistencyCount: inconsistencies.length,
    inconsistencies
  };
};
