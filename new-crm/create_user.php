<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New User | CRM</title>
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
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Create New User</h1>

        <?php
        // Include database connection
        require_once 'config.php';
        require_once 'db_connect.php'; // Include the file with connect_db()

        $conn = null; // Initialize connection variable

        try {
            $conn = connect_db(); // Establish database connection
        } catch (PDOException $e) {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                    <strong class='font-bold'>Error!</strong>
                    <span class='block sm:inline'>Database connection failed: " . $e->getMessage() . "</span>
                  </div>";
            // Exit if database connection fails, as we can't proceed
            exit();
        }

        $companies = [];
        try {
            // Fetch all companies to populate the dropdown
            $stmt = $conn->prepare("SELECT id, companyName FROM companies ORDER BY companyName ASC");
            $stmt->execute();
            $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                    <strong class='font-bold'>Error!</strong>
                    <span class='block sm:inline'>Error fetching companies: " . $e->getMessage() . "</span>
                  </div>";
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $companyId = filter_input(INPUT_POST, 'company_id', FILTER_VALIDATE_INT);
            $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);

            $errors = [];

            if (!$companyId || $companyId <= 0) {
                $errors[] = "Please select a valid company.";
            }
            if (empty($firstName)) {
                $errors[] = "First Name is required.";
            }
            if (empty($lastName)) {
                $errors[] = "Last Name is required.";
            }
            // Email is optional, but if provided, it should be valid
            if (!empty($_POST['email']) && !$email) {
                $errors[] = "Please enter a valid email address.";
            }

            if (empty($errors)) {
                try {
                    $stmt = $conn->prepare("INSERT INTO contacts (company_id, first_name, last_name, email, phone, title) VALUES (:company_id, :first_name, :last_name, :email, :phone, :title)");
                    $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
                    $stmt->bindParam(':first_name', $firstName);
                    $stmt->bindParam(':last_name', $lastName);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':phone', $phone);
                    $stmt->bindParam(':title', $title);

                    if ($stmt->execute()) {
                        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>
                                <strong class='font-bold'>Success!</strong>
                                <span class='block sm:inline'>User added successfully.</span>
                              </div>";
                        // Optionally, clear form fields or redirect
                    } else {
                        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                                <strong class='font-bold'>Error!</strong>
                                <span class='block sm:inline'>Failed to add user.</span>
                              </div>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                            <strong class='font-bold'>Error!</strong>
                            <span class='block sm:inline'>Database error: " . $e->getMessage() . "</span>
                          </div>";
                }
            } else {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                        <strong class='font-bold'>Validation Error(s):</strong>
                        <ul class='mt-2 list-disc list-inside'>";
                foreach ($errors as $error) {
                    echo "<li>" . $error . "</li>";
                }
                echo "</ul></div>";
            }
        }
        ?>

        <form method="POST" action="create_user.php" class="space-y-4">
            <div>
                <label for="company_id" class="block text-sm font-medium text-gray-700">Company:</label>
                <select id="company_id" name="company_id" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">-- Select Company --</option>
                    <?php foreach ($companies as $company): ?>
                        <option value="<?php echo htmlspecialchars($company['id']); ?>">
                            <?php echo htmlspecialchars($company['companyName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name:</label>
                <input type="text" id="first_name" name="first_name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone:</label>
                <input type="text" id="phone" name="phone" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title:</label>
                <input type="text" id="title" name="title" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div class="flex justify-between items-center">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-150 ease-in-out">
                    Add User
                </button>
                <a href="index.php" class="text-blue-600 hover:underline">Back to Companies</a>
            </div>
        </form>
    </div>
</body>
</html>