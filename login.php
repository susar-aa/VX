<?php
// login.php - Secure Login System for VX
session_start();
require_once 'db.php';

// If already logged in, redirect to index.php
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $conn->prepare("SELECT * FROM `users` WHERE `username` = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];

                // If remember me is checked, set a cookie that lasts 30 days
                if ($remember) {
                    ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
                    session_regenerate_id(true);
                }

                header("Location: index.php");
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-black">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VX ERP + POS - Login</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        dark: {
                            950: '#030303',
                            900: '#0a0a0a',
                            800: '#121212',
                            700: '#1c1c1e',
                            600: '#2c2c2e',
                        },
                        lime: {
                            DEFAULT: '#ccff00',
                            glow: '#a3e635',
                            dark: '#84cc16',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom smooth focus ring & text styling */
        .neon-border {
            box-shadow: 0 0 10px rgba(204, 255, 0, 0.1), 0 0 1px rgba(204, 255, 0, 0.4);
        }
        .neon-border:focus-within {
            box-shadow: 0 0 15px rgba(204, 255, 0, 0.25), 0 0 2px rgba(204, 255, 0, 0.8);
            border-color: #ccff00;
        }
        .neon-btn {
            background-color: #ccff00;
            color: #000;
            box-shadow: 0 4px 14px 0 rgba(204, 255, 0, 0.3);
            transition: all 0.3s ease;
        }
        .neon-btn:active {
            transform: scale(0.97);
            box-shadow: 0 2px 6px 0 rgba(204, 255, 0, 0.2);
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-dark-950 font-sans text-gray-200 selection:bg-lime selection:text-black">

    <div class="w-full max-w-md">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <h1 class="text-7xl font-extrabold tracking-tighter text-white inline-flex items-center gap-1">
                V<span class="text-lime">X</span>
            </h1>
            <p class="text-xs tracking-[0.25em] text-gray-500 uppercase mt-2">Partnership ERP + POS</p>
        </div>

        <!-- Glassmorphism Login Card -->
        <div class="bg-dark-900/60 backdrop-blur-xl border border-white/10 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            <!-- Decorative light glow top-right -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-lime/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <h2 class="text-xl font-bold text-white mb-6">Access Account</h2>

            <?php if (!empty($error)): ?>
                <div class="mb-5 p-3 rounded-xl bg-red-950/40 border border-red-500/30 text-red-400 text-xs flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 7.5h.008v.008H12v-.008Z" />
                    </svg>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" autocomplete="off" class="space-y-5">
                
                <!-- Username Input -->
                <div class="space-y-2">
                    <label for="username" class="text-xs font-semibold text-gray-400 tracking-wide">Username</label>
                    <div class="flex items-center bg-dark-800/80 border border-white/10 rounded-2xl p-3.5 neon-border transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-500 mr-3 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            required 
                            placeholder="e.g. susar.aa" 
                            class="bg-transparent border-0 w-full text-white placeholder-gray-600 focus:outline-none focus:ring-0 text-sm font-medium"
                            value="<?= isset($username) ? htmlspecialchars($username) : '' ?>"
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <label for="password" class="text-xs font-semibold text-gray-400 tracking-wide">Password</label>
                    <div class="flex items-center bg-dark-800/80 border border-white/10 rounded-2xl p-3.5 neon-border transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-500 mr-3 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            required 
                            placeholder="••••••••" 
                            class="bg-transparent border-0 w-full text-white placeholder-gray-600 focus:outline-none focus:ring-0 text-sm font-medium"
                        >
                        <button type="button" onclick="togglePassword()" class="text-gray-500 hover:text-white shrink-0 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5" id="eyeIcon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center cursor-pointer select-none">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            class="sr-only peer"
                            <?= isset($remember) && $remember ? 'checked' : '' ?>
                        >
                        <div class="w-5 h-5 bg-dark-800 rounded-md border border-white/10 peer-checked:border-lime peer-checked:bg-lime/10 flex items-center justify-center transition-all shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3.5 h-3.5 text-lime hidden peer-checked:block">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 ml-2.5">Keep me logged in</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full neon-btn py-4 rounded-2xl font-bold text-sm tracking-wide mt-3 flex items-center justify-center gap-2">
                    <span>Enter VX System</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </button>

            </form>
        </div>

        <div class="text-center mt-6">
            <p class="text-xs text-gray-600 font-medium">Authorized Personnel Only • Secure Session Tracking Active</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M21 12c-1.292-4.338-5.311-7.5-10-7.5-1.293 0-2.527.234-3.669.66M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm-9.75 3h.008v.008H2.25V12Zm19.5 0h.008v.008h-.008V12Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                `;
            }
        }
    </script>
</body>
</html>
