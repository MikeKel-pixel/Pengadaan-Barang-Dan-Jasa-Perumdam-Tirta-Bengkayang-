<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-4">Loading...</h1>
            <p>Anda akan dialihkan ke dashboard sesuai role.</p>
            <script>
                setTimeout(function() {
                    window.location.href = "/dashboard";
                }, 1000);
            </script>
        </div>
    </div>
</body>
</html>