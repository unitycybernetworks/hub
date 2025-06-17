<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM | Skye Cloud Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        /* Modal Overlay */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.7);
        }
        /* Scrollbar for modal content */
        .modal-content-scrollable {
            max-height: 80vh; /* Adjust as needed */
            overflow-y: auto;
            -webkit-overflow-scrolling: touch; /* For smoother scrolling on iOS */
        }
    </style>
</head>
<body class="p-4 sm:p-6 md:p-8">

    <div class="w-full bg-white shadow-lg rounded-xl p-6 md:p-8">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 text-center sm:text-left mb-4 sm:mb-0">Customer Relations Manager</h1>
            <div class="flex space-x-2">
                <input type="text" id="search-input" placeholder="Search companies..." class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button id="search-button" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-150 ease-in-out">
                    Search
                </button>
            </div>
        </div>

        <div id="loading" class="text-center text-gray-600 mb-4" style="display: none;">
            Loading companies...
        </div>

        <div id="error-message" class="text-center text-red-600 mb-4" style="display: none;">
            Failed to load companies. Please try again later.
        </div>

        <div class="overflow-x-auto rounded-lg shadow-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">ID</th>
                        <th class="px-4 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company Name</th>
                        <th class="px-4 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</th>
                        <th class="px-4 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website</th>
                        <th class="px-4 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                        <th class="px-4 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                        <th class="px-4 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">Actions</th>
                    </tr>
                </thead>
                <tbody id="company-table-body" class="bg-white divide-y divide-gray-200">
                    </tbody>
            </table>
        </div>
    </div>

    <div id="company-details-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 modal-overlay" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl p-6 w-11/12 md:w-2/3 lg:w-1/2 relative flex flex-col max-h-[90vh]">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Company Details</h2>
            <div id="modal-company-data" class="modal-content-scrollable flex-grow">
                <p class="text-gray-700">Loading details...</p>
            </div>
            <div class="mt-6 flex justify-end">
                <button id="close-modal-btn" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-150 ease-in-out">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        const API_URL = 'api.php'; // Path to your PHP API file

        document.addEventListener('DOMContentLoaded', () => {
            fetchCompanies();

            // Close company details modal button event listener
            document.getElementById('close-modal-btn').addEventListener('click', () => {
                document.getElementById('company-details-modal').style.display = 'none';
            });

            // Search button event listener
            document.getElementById('search-button').addEventListener('click', () => {
                searchCompanies();
            });

            // Allow searching by pressing Enter in the search input
            document.getElementById('search-input').addEventListener('keypress', (event) => {
                if (event.key === 'Enter') {
                    searchCompanies();
                }
            });
        });

        async function fetchCompanies(searchQuery = '') {
            const loadingElement = document.getElementById('loading');
            const errorElement = document.getElementById('error-message');
            const tableBody = document.getElementById('company-table-body');

            loadingElement.style.display = 'block';
            errorElement.style.display = 'none';
            tableBody.innerHTML = ''; // Clear existing table data

            let url = `${API_URL}?action=getAllCompanies`;
            if (searchQuery) {
                url += `&search=${encodeURIComponent(searchQuery)}`;
            }

            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();

                if (result.status === 'success' && result.data.length > 0) {
                    result.data.forEach(company => {
                        const row = document.createElement('tr');
                        row.classList.add('hover:bg-gray-50');

                        const addressParts = [];
                        if (company.address1) addressParts.push(company.address1);
                        if (company.address2) addressParts.push(company.address2);
                        if (company.city) addressParts.push(company.city);
                        if (company.postalCode) addressParts.push(company.postalCode);
                        const fullAddress = addressParts.join(', ') || 'N/A';

                        row.innerHTML = `
                            <td class="px-4 py-2 sm:px-6 sm:py-4 whitespace-nowrap text-sm font-medium text-gray-900">${company.id}</td>
                            <td class="px-4 py-2 sm:px-6 sm:py-4 whitespace-nowrap text-sm text-gray-700">${company.companyName}</td>
                            <td class="px-4 py-2 sm:px-6 sm:py-4 whitespace-nowrap text-sm text-gray-700">${company.phone || 'N/A'}</td>
                            <td class="px-4 py-2 sm:px-6 sm:py-4 whitespace-nowrap text-sm text-blue-600 hover:underline">
                                ${company.webAddress ? `<a href="${company.webAddress}" target="_blank">${company.webAddress}</a>` : 'N/A'}
                            </td>
                            <td class="px-4 py-2 sm:px-6 sm:py-4 whitespace-normal text-sm text-gray-700 max-w-xs">${fullAddress}</td>
                            <td class="px-4 py-2 sm:px-6 sm:py-4 whitespace-nowrap text-sm font-medium">
                                <a href="users.php?companyId=${company.id}&companyName=${encodeURIComponent(company.companyName)}" class="px-4 py-2 bg-purple-600 text-white rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-75 transition duration-150 ease-in-out">
                                    View Users
                                </a>
                            </td>
                            <td class="px-4 py-2 sm:px-6 sm:py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="viewCompany(${company.id})" class="px-4 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-75 transition duration-150 ease-in-out">
                                    View Company
                                </button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else if (result.status === 'success' && result.data.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-4 py-2 sm:px-6 sm:py-4 text-center text-gray-500">No companies found.</td>
                        </tr>
                    `;
                } else {
                    errorElement.textContent = result.message || 'An unknown error occurred.';
                    errorElement.style.display = 'block';
                }
            } catch (error) {
                console.error('Error fetching companies:', error);
                errorElement.textContent = `Error: ${error.message}. Please check api.php and your database connection.`;
                errorElement.style.display = 'block';
            } finally {
                loadingElement.style.display = 'none';
            }
        }

        function searchCompanies() {
            const searchQuery = document.getElementById('search-input').value.trim();
            fetchCompanies(searchQuery);
        }

        async function viewCompany(id) {
            const modal = document.getElementById('company-details-modal');
            const modalContent = document.getElementById('modal-company-data');
            modalContent.innerHTML = '<p class="text-gray-700">Loading details...</p>';
            modal.style.display = 'flex'; // Show the modal

            try {
                const response = await fetch(`${API_URL}?action=getCompanyById&id=${id}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();

                if (result.status === 'success' && result.data) {
                    const company = result.data;
                    let detailsHtml = `
                        <p><strong class="font-semibold">ID:</strong> ${company.id}</p>
                        <p><strong class="font-semibold">Company Name:</strong> ${company.companyName}</p>
                        <p><strong class="font-semibold">Phone Number:</strong> ${company.phone || 'N/A'}</p>
                        <p><strong class="font-semibold">Website:</strong> ${company.webAddress ? `<a href="${company.webAddress}" target="_blank" class="text-blue-600 hover:underline">${company.webAddress}</a>` : 'N/A'}</p>
                        <p><strong class="font-semibold">SharePoint URL:</strong> ${company.sharepointSite ? `<a href="${company.sharepointSite}" target="_blank" class="text-blue-600 hover:underline">${company.sharepointSite}</a>` : 'N/A'}</p>
                        <h3 class="text-lg font-semibold mt-4 mb-2 border-b pb-1">Address Information:</h3>
                        <p><strong class="font-semibold">Address 1:</strong> ${company.address1 || 'N/A'}</p>
                        <p><strong class="font-semibold">Address 2:</strong> ${company.address2 || 'N/A'}</p>
                        <p><strong class="font-semibold">City:</strong> ${company.city || 'N/A'}</p>
                        <p><strong class="font-semibold">Postal Code:</strong> ${company.postalCode || 'N/A'}</p>
                    `;
                    modalContent.innerHTML = detailsHtml;
                } else {
                    modalContent.innerHTML = `<p class="text-red-600">Error: ${result.message || 'Company details not found.'}</p>`;
                }
            } catch (error) {
                console.error('Error fetching company details:', error);
                modalContent.innerHTML = `<p class="text-red-600">Error fetching details: ${error.message}.</p>`;
            }
        }
    </script>
</body>
</html>