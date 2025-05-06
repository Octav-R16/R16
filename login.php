<?php
 session_start();
 if (isset($_SESSION['username'])) {
     header("Location: proses_komplain.php");
     exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: url('uploads/rm222batch2-mind-03.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-sm p-4">
        <!-- Error Message -->
        <?php if (isset($_GET['error'])): ?>
            <div id="errorAlert" class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r text-sm flex items-center animate-fade-in">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="flex-1">
                    <?= $_GET['error'] == 1 ? 'Username atau password salah' : 
                       ($_GET['error'] == 2 ? 'Sesi telah berakhir, silakan login kembali' : 
                       htmlspecialchars($_GET['error'])) ?>
                </span>
                <button onclick="dismissError()" class="ml-2 text-red-500 hover:text-red-700 focus:outline-none">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if (isset($_GET['message'])): ?>
            <div id="successMessage" class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r text-sm flex items-center animate-fade-in">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="flex-1">
                    <?= $_GET['message'] === 'logout' ? 'Anda berhasil logout' : 
                       ($_GET['message'] === 'login_required' ? 'Silakan login untuk melanjutkan' : 
                       htmlspecialchars($_GET['message'])) ?>
                </span>
                <button onclick="dismissSuccess()" class="ml-2 text-green-500 hover:text-green-700 focus:outline-none">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <!-- Login Card -->
        <div id="loginCard" class="bg-white p-8 rounded-lg shadow-md w-full opacity-0 transition-opacity duration-500">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Login</h2>

            <form action="proses_login.php" method="POST" class="space-y-4">
                <div>
                    <label for="UserID" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="UserID" id="UserID" placeholder="Masukkan username" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition duration-200">
                </div>

                <div>
                    <label for="User_Password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="User_Password" id="User_Password" placeholder="••••••••" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition duration-200">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Login
                </button>
            </form>
        </div>
    </div>

        <script>
        // Show login card with fade-in effect
        document.addEventListener('DOMContentLoaded', () => {
            const loginCard = document.getElementById('loginCard');
            if (loginCard) {
                loginCard.classList.remove('opacity-0');
                loginCard.classList.add('opacity-100');
            }

            // Auto dismiss success message after 5 seconds
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.classList.add('opacity-0', 'transition', 'duration-500');
                    setTimeout(() => successMessage.remove(), 500);
                }, 5000);
            }
        });

        // Manual dismiss for error alert
        function dismissError() {
            const errorAlert = document.getElementById('errorAlert');
            if (errorAlert) {
                errorAlert.classList.add('opacity-0', 'transition', 'duration-300');
                setTimeout(() => errorAlert.remove(), 300);
            }
        }

        // Manual dismiss for success message
        function dismissSuccess() {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.classList.add('opacity-0', 'transition', 'duration-300');
                setTimeout(() => successMessage.remove(), 300);
            }
        }
    </script>
</body>
</html>
