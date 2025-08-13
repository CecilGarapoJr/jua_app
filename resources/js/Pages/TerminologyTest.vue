<template>
  <div class="container mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Terminology Replacement Test Page</h1>
    
    <div class="mb-8">
      <TerminologyTest />
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold mb-4">Common UI Elements with Terminology</h2>
      
      <div class="space-y-6">
        <!-- Cards -->
        <div class="grid md:grid-cols-2 gap-4">
          <div class="border rounded-lg p-4">
            <h3 class="font-medium mb-2" v-persona>Job Listing</h3>
            <p v-persona>This Company is looking for Candidates to fill this Job position.</p>
            <div class="mt-3">
              <button class="px-4 py-2 bg-blue-600 text-white rounded" v-persona>Apply for Job</button>
            </div>
          </div>
          
          <div class="border rounded-lg p-4">
            <h3 class="font-medium mb-2" v-persona>Candidate Profile</h3>
            <p v-persona>View Candidate details and contact the Employer about this Candidate.</p>
            <div class="mt-3">
              <button class="px-4 py-2 bg-green-600 text-white rounded" v-persona>Contact Employer</button>
            </div>
          </div>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-100">
                <th class="border p-2 text-left" v-persona>Job Title</th>
                <th class="border p-2 text-left" v-persona>Company</th>
                <th class="border p-2 text-left" v-persona>Candidates</th>
                <th class="border p-2 text-left">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="border p-2" v-persona>Senior Developer Job</td>
                <td class="border p-2" v-persona>Tech Company</td>
                <td class="border p-2" v-persona>5 Candidates</td>
                <td class="border p-2">
                  <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm" v-persona>View Job</button>
                </td>
              </tr>
              <tr>
                <td class="border p-2" v-persona>Marketing Job</td>
                <td class="border p-2" v-persona>Media Company</td>
                <td class="border p-2" v-persona>3 Candidates</td>
                <td class="border p-2">
                  <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm" v-persona>View Job</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Form -->
        <div class="border rounded-lg p-4">
          <h3 class="font-medium mb-4" v-persona>Create Job Listing</h3>
          <div class="space-y-4">
            <div>
              <label class="block mb-1" v-persona>Job Title</label>
              <input type="text" class="w-full border rounded p-2" placeholder="Enter job title" />
            </div>
            <div>
              <label class="block mb-1" v-persona>Company Information</label>
              <textarea class="w-full border rounded p-2" rows="3" placeholder="Enter company details"></textarea>
            </div>
            <div>
              <label class="block mb-1" v-persona>Required Candidate Skills</label>
              <input type="text" class="w-full border rounded p-2" placeholder="Enter required skills" />
            </div>
            <div>
              <button class="px-4 py-2 bg-blue-600 text-white rounded" v-persona>Post Job</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import TerminologyTest from '@/Components/TerminologyTest.vue';
</script>
