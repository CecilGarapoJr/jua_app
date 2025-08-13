<template>
  <div class="container mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Terminology Frontend Test</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <!-- Using the v-persona directive -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold mb-4">Using v-persona Directive</h2>
        
        <div class="space-y-4">
          <div class="p-4 border rounded">
            <h3 class="font-medium mb-2">Original Text</h3>
            <p>Companies post Jobs for Candidates to apply.</p>
            <p>Employers can view Candidate profiles.</p>
          </div>
          
          <div class="p-4 border rounded bg-gray-50">
            <h3 class="font-medium mb-2">With v-persona</h3>
            <p v-persona>Companies post Jobs for Candidates to apply.</p>
            <p v-persona>Employers can view Candidate profiles.</p>
          </div>
        </div>
      </div>
      
      <!-- Using the TerminologyText component -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold mb-4">Using TerminologyText Component</h2>
        
        <div class="space-y-4">
          <div class="p-4 border rounded">
            <h3 class="font-medium mb-2">Original Text</h3>
            <p>Companies post Jobs for Candidates to apply.</p>
            <p>Employers can view Candidate profiles.</p>
          </div>
          
          <div class="p-4 border rounded bg-gray-50">
            <h3 class="font-medium mb-2">With TerminologyText</h3>
            <TerminologyText tag="p" text="Companies post Jobs for Candidates to apply." />
            <TerminologyText tag="p" text="Employers can view Candidate profiles." />
          </div>
        </div>
      </div>
    </div>
    
    <!-- Using the T shorthand component -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold mb-4">Using T Shorthand Component</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 border rounded">
          <h3 class="font-medium mb-2">Original Text</h3>
          <p>Companies post Jobs for Candidates to apply.</p>
          <p>Employers can view Candidate profiles.</p>
        </div>
        
        <div class="p-4 border rounded bg-gray-50">
          <h3 class="font-medium mb-2">With T Component</h3>
          <T tag="p">Companies post Jobs for Candidates to apply.</T>
          <T tag="p">Employers can view Candidate profiles.</T>
        </div>
      </div>
    </div>
    
    <!-- UI Components with Terminology -->
    <div class="bg-white rounded-lg shadow-sm p-6">
      <h2 class="text-xl font-semibold mb-4">UI Components with Terminology</h2>
      
      <div class="space-y-6">
        <!-- Cards -->
        <div class="grid md:grid-cols-2 gap-4">
          <div class="border rounded-lg p-4">
            <T tag="h3" class="font-medium mb-2">Job Listing</T>
            <T tag="p">This Company is looking for Candidates to fill this Job position.</T>
            <div class="mt-3">
              <button class="px-4 py-2 bg-blue-600 text-white rounded">
                <T>Apply for Job</T>
              </button>
            </div>
          </div>
          
          <div class="border rounded-lg p-4">
            <T tag="h3" class="font-medium mb-2">Candidate Profile</T>
            <T tag="p">View Candidate details and contact the Employer about this Candidate.</T>
            <div class="mt-3">
              <button class="px-4 py-2 bg-green-600 text-white rounded">
                <T>Contact Employer</T>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-100">
                <th class="border p-2 text-left"><T>Job Title</T></th>
                <th class="border p-2 text-left"><T>Company</T></th>
                <th class="border p-2 text-left"><T>Candidates</T></th>
                <th class="border p-2 text-left">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="border p-2"><T>Senior Developer Job</T></td>
                <td class="border p-2"><T>Tech Company</T></td>
                <td class="border p-2"><T>5 Candidates</T></td>
                <td class="border p-2">
                  <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm">
                    <T>View Job</T>
                  </button>
                </td>
              </tr>
              <tr>
                <td class="border p-2"><T>Marketing Job</T></td>
                <td class="border p-2"><T>Media Company</T></td>
                <td class="border p-2"><T>3 Candidates</T></td>
                <td class="border p-2">
                  <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm">
                    <T>View Job</T>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// No additional imports needed as we're using global components and directives
</script>
