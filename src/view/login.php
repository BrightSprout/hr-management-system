<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link href="public/style.css" rel="stylesheet">
    <link href="public/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen grid grid-cols-1 md:grid-cols-2 ">
        <!-- Left Panel -->
        <div class="bg-pos flex items-center justify-center text-white px-6 py-10 ">
            <div class="text-center">
                <img src="public/assets/images/logo-1.png" alt="MAMATID-LOGO" class="w-36 h-auto mx-auto mb-6">
                <h2 class="text-3xl font-bold mb-2">Welcome to Mamatid HR Management System</h2>
                <p class="text-lg text-white/90 font-medium">Explore the HR management with good-looking developers.</p>
            </div>
        </div>

        <!-- Right Panel (Login Form) -->
        <div class="bg-pos-1 flex items-center justify-center px-6 py-12 order-1 lg:order-2 ">
            <div class="w-full max-w-md fade-in glass-panel p-8">
                <div class="text-center mb-8">
                    <img src="public/assets/images/logo-2.png" alt="Mamatid Logo" class="w-36 h-auto mx-auto mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Sign in to your account</h2>
                    <p class="text-gray-600 text-sm">Enter your credentials to access the system</p>
                </div>
                <form id="loginForm">
                    <!-- User ID -->
                    <div class="mb-6">
                        <label for="userID" class="block text-sm font-medium text-gray-700 mb-2">
                            User ID <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-600">
                                <i data-feather="user" class="w-5 h-5"></i>
                            </span>
                            <input type="text" id="userID" name="username" required autocomplete="username"
                                class="w-full pl-10 glass-input pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="Enter your User ID" aria-describedby="userID-error">
                        </div>
                        <div id="userID-error" class="error-message hidden" role="alert"></div>
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i data-feather="lock" class="w-5 h-5"></i>
                            </span>
                            <input type="password" id="password" name="password" required
                                autocomplete="current-password"
                                class="w-full glass-input pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="Enter your password" aria-describedby="password-error">
                            <button type="button"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors duration-200"
                                onclick="togglePassword()" aria-label="Toggle password visibility">
                                <i data-feather="eye" id="togglePasswordIcon" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <div id="password-error" class="error-message hidden" role="alert"></div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>

                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" id="submitBtn"
                        class="gradient-btn w-full text-white font-medium py-3 rounded-lg flex items-center justify-center duration-200">
                        <span id="submitText">Sign In</span>
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-xs text-gray-500 mb-2">Need help?</p>
                    <a href="#" class="text-xs text-blue-600 hover:text-blue-800 hover:underline">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        feather.replace();

        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const icon = document.getElementById("togglePasswordIcon");
            const isHidden = passwordInput.type === "password";
            passwordInput.type = isHidden ? "text" : "password";
            icon.setAttribute("data-feather", isHidden ? "eye-off" : "eye");
            feather.replace();
        }

        document.querySelector("#loginForm").addEventListener("submit", async function (e) {
            e.preventDefault();

            const response = await fetch("api/login", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(Object.fromEntries((new FormData(e.target)).entries()))
            });

            if (!response.ok) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Login Error!',
                    text: 'Incorrect username or password',
                    showConfirmButton: false,
                    timer: 1500
                });
            }

            location.reload();
        });        
    </script>
</body>

</html>
