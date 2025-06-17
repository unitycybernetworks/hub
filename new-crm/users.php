<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users for Company</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="p-4 sm:p-6 md:p-8">

    <div class="w-full bg-white shadow-lg rounded-xl p-6 md:p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Users for <span id="company-name-display"></span></h1>

        <div id="loading" class="text-center text-gray-600 mb-4" style="display: none;">
            Loading users...
        </div>

        <div id="error-message" class="text-center text-red-600 mb-4" style="display: none;">
            Failed to load users.
        </div>

        <div id="user-list-container" class="mt-6">
            </div>

        <div class="mt-6 flex justify-center">
            <a href="index.php" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-150 ease-in-out">
                Back to Company List
            </a>
        </div>
    </div>

    <script>
        const API_URL = 'api.php'; // Path to your PHP API file

        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const companyId = urlParams.get('companyId');
            const companyName = urlParams.get('companyName');

            const companyNameDisplay = document.getElementById('company-name-display');
            const loadingElement = document.getElementById('loading');
            const errorElement = document.getElementById('error-message');
            const userListContainer = document.getElementById('user-list-container');

            if (companyName) {
                companyNameDisplay.textContent = decodeURIComponent(companyName);
            } else {
                companyNameDisplay.textContent = 'Unknown Company';
            }

            if (companyId) {
                fetchUsersByCompanyId(companyId);
            } else {
                errorElement.textContent = 'No company ID provided.';
                errorElement.style.display = 'block';
            }
        });

        async function fetchUsersByCompanyId(companyId) {
            const loadingElement = document.getElementById('loading');
            const errorElement = document.getElementById('error-message');
            const userListContainer = document.getElementById('user-list-container');

            loadingElement.style.display = 'block';
            errorElement.style.display = 'none';
            userListContainer.innerHTML = ''; // Clear existing user data

            try {
                const response = await fetch(`${API_URL}?action=getUsersByCompanyId&companyId=${companyId}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();

                console.log('API Response for Users (Full JSON):', JSON.stringify(result, null, 2));

                if (result.status === 'success' && result.data && result.data.length > 0) {
                    // Changed grid columns to display 6 contacts per row on large screens
                    let usersHtml = '<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">';
                    result.data.forEach(user => {
                        usersHtml += `
                            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                <p><strong class="font-semibold">Name:</strong> ${user.first_name} ${user.last_name}</p>
                                <p><strong class="font-semibold">Email:</strong> ${user.email || 'N/A'}</p>
                                <p><strong class="font-semibold">Phone:</strong> ${user.phone || 'N/A'}</p>
                                <p><strong class="font-semibold">Title:</strong> ${user.title || 'N/A'}</p>
                            </div>
                        `;
                    });
                    usersHtml += '</div>';
                    userListContainer.innerHTML = usersHtml;
                } else if (result.status === 'success' && result.data.length === 0) {
                    userListContainer.innerHTML = `<p class="text-gray-700 text-center">No users found for this company.</p>`;
                } else {
                    errorElement.textContent = result.message || 'An unknown error occurred.';
                    errorElement.style.display = 'block';
                }
            } catch (error) {
                console.error('Error fetching users:', error);
                errorElement.textContent = `Error fetching users: ${error.message}.`;
                errorElement.style.display = 'block';
            } finally {
                loadingElement.style.display = 'none';
            }
        }
    </script>
</body>
</html>