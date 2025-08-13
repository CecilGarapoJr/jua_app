<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminology URL Test</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6">Terminology URL Test Page</h1>
        
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-3">Original URLs vs New URLs</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded">
                    <h3 class="font-medium mb-2">Original URLs</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('companies.index') }}" class="text-blue-600 hover:underline">Companies Index</a></li>
                        <li><a href="{{ route('candidates.index') }}" class="text-blue-600 hover:underline">Candidates Index</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="text-blue-600 hover:underline">Jobs Index</a></li>
                        <li><a href="{{ route('employer.dashboard') }}" class="text-blue-600 hover:underline">Employer Dashboard</a></li>
                    </ul>
                </div>
                
                <div class="bg-gray-50 p-4 rounded">
                    <h3 class="font-medium mb-2">New URLs</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('organisations.index') }}" class="text-blue-600 hover:underline">Organisations Index</a></li>
                        <li><a href="{{ route('applicants.index') }}" class="text-blue-600 hover:underline">Applicants Index</a></li>
                        <li><a href="{{ route('opportunities.index') }}" class="text-blue-600 hover:underline">Opportunities Index</a></li>
                        <li><a href="{{ route('organisation.dashboard') }}" class="text-blue-600 hover:underline">Organisation Dashboard</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-3">URL Structure Comparison</h2>
            <table class="min-w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-2 text-left">Term</th>
                        <th class="border border-gray-300 p-2 text-left">Old URL Pattern</th>
                        <th class="border border-gray-300 p-2 text-left">New URL Pattern</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 p-2">Company → Organisation</td>
                        <td class="border border-gray-300 p-2">/companies/...</td>
                        <td class="border border-gray-300 p-2">/organisations/...</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 p-2">Candidate → Applicant</td>
                        <td class="border border-gray-300 p-2">/candidates/...</td>
                        <td class="border border-gray-300 p-2">/applicants/...</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 p-2">Job → Opportunity</td>
                        <td class="border border-gray-300 p-2">/jobs/...</td>
                        <td class="border border-gray-300 p-2">/opportunities/...</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 p-2">Employer → Organisation</td>
                        <td class="border border-gray-300 p-2">/employer/...</td>
                        <td class="border border-gray-300 p-2">/organisation/...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="text-sm text-gray-600">
            <p>Note: This page is for testing purposes only. The new URLs should redirect to the same content as the original URLs.</p>
        </div>
    </div>
</body>
</html>
