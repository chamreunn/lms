<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }

        .error-container {
            max-width: 600px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #d9534f;
            margin-bottom: 20px; /* Add margin for separation */
        }

        p {
            margin-bottom: 20px; /* Add margin for separation */
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Error</h1>
        <p><?php echo isset($_SESSION['error']) ? $_SESSION['error'] : 'An unexpected error occurred.'; ?></p>
    </div>
</body>
</html>
