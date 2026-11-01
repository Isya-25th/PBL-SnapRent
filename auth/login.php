<?php
session_start();

// Jika admin sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['admin'])) {
    header("Location: ../dashboard/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SnapRent | Rental Kamera</title>
    
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- ikon kamera & form -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Latar belakang */
        .studio-grid {
            background-color: #fafafa;
            background-size: 24px 24px;
        }

        /* Autofill styling */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px white inset !important;
            -webkit-text-fill-color: #18181b !important;
            font-family: 'Poppins', sans-serif !important;
        }
    </style>
</head>
<body class="studio-grid min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 text-neutral-900 selection:bg-black selection:text-white">

    <!-- Container utama form Login -->
    <div class="w-full max-w-[410px] relative mt-8">

        <!-- Card Login dengan Aksen Sudut Viewfinder Kamera [  ] -->
        <div class="relative bg-white rounded-3xl border border-neutral-300/80 shadow-[0_15px_40px_-10px_rgba(0,0,0,0.08)] px-8 pt-12 pb-8 transition-all">
            
            <!-- Ornamen Viewfinder Kamera di 4 Sudut Card -->
            <span class="absolute top-4 left-4 w-3.5 h-3.5 border-t-2 border-l-2 border-neutral-300 rounded-tl pointer-events-none" title="Viewfinder Focus"></span>
            <span class="absolute top-4 right-4 w-3.5 h-3.5 border-t-2 border-r-2 border-neutral-300 rounded-tr pointer-events-none"></span>
            <span class="absolute bottom-4 left-4 w-3.5 h-3.5 border-b-2 border-l-2 border-neutral-300 rounded-bl pointer-events-none"></span>
            <span class="absolute bottom-4 right-4 w-3.5 h-3.5 border-b-2 border-r-2 border-neutral-300 rounded-br pointer-events-none"></span>

            <!-- Badge Logo Kamera Melayang di Bagian Atas Card (Lensa Kamera / Lens Aperture) -->
            <div class="absolute -top-11 left-1/2 transform -translate-x-1/2 bg-white rounded-full p-1.5 border border-neutral-300/90 shadow-sm">
                <div class="bg-black text-white rounded-full h-16 w-16 flex flex-col items-center justify-center ring-2 ring-neutral-200">
                    <i class="fa-solid fa-camera text-2xl"></i>
                </div>
            </div>

            <!-- Header: Identitas Web SnapRent -->
            <div class="text-center mt-6 mb-7">
                <!-- Badge Kategori Layanan -->
                <!-- <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold uppercase tracking-wider bg-neutral-100 text-neutral-700 border border-neutral-200 mb-2">
                    <span class="rounded-full bg-black"></span>
                    Portal Admin SnapRent
                </div> -->

                <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">
                    Admin Login
                </h1>
                <p class="text-xs text-neutral-500 mt-1.5 leading-relaxed">
                    Sistem Manajemen Penyewaan Kamera
                </p>
            </div>

            <!-- Pesan Notifikasi Error Jika Login Gagal -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs flex items-start gap-2.5 shadow-sm">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-sm mt-0.5 shrink-0"></i>
                    <div class="font-medium leading-snug">
                        <?= htmlspecialchars($_SESSION['error']); ?>
                    </div>
                </div>
                <?php unset($_SESSION['error']); // Bersihkan sesi error ?>
            <?php endif; ?>

            <!-- Form Otentikasi Admin -->
            <form action="proses_login.php" method="POST" class="space-y-4">
                
                <!-- Kolom Input: Username atau Email -->
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1.5">
                        Username / Email
                    </label>
                    <div class="relative border border-neutral-300 rounded-xl flex items-center px-3.5 py-2.5 bg-neutral-50/50 hover:border-neutral-400 focus-within:border-black focus-within:bg-white focus-within:ring-2 focus-within:ring-black/10 transition-all duration-200">
                        <i class="fa-regular fa-user text-neutral-400 text-base mr-3 shrink-0"></i>
                        <input 
                            type="text" 
                            name="username" 
                            placeholder="Username" 
                            class="w-full text-sm font-normal text-neutral-900 placeholder-neutral-400 bg-transparent focus:outline-none"
                            required
                            autocomplete="username"
                        >
                    </div>
                </div>

                <!-- Kolom Input: Password -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-xs font-semibold text-neutral-700">
                            Password
                        </label>
                        <a 
                            href="javascript:void(0)" 
                            onclick="alert('Untuk mengatur ulang kata sandi admin, silakan hubungi Superadmin atau periksa database SnapRent.')" 
                            class="text-xs text-neutral-500 hover:text-black hover:underline transition-colors"
                        >
                            Lupa Password?
                        </a>
                    </div>
                    <div class="relative border border-neutral-300 rounded-xl flex items-center px-3.5 py-2.5 bg-neutral-50/50 hover:border-neutral-400 focus-within:border-black focus-within:bg-white focus-within:ring-2 focus-within:ring-black/10 transition-all duration-200">
                        <i class="fa-solid fa-lock text-neutral-400 text-base mr-3 shrink-0"></i>
                        <input 
                            type="password" 
                            id="passwordInput"
                            name="password" 
                            placeholder="Password" 
                            class="w-full text-sm font-normal text-neutral-900 placeholder-neutral-400 bg-transparent focus:outline-none"
                            required
                            autocomplete="current-password"
                        >
                        <!-- Tombol Show / Hide Password -->
                        <button 
                            type="button" 
                            id="togglePasswordBtn" 
                            class="text-neutral-400 hover:text-black focus:outline-none transition-colors ml-1 p-1" 
                            title="Tampilkan / Sembunyikan Password"
                            aria-label="Toggle password"
                        >
                            <i id="togglePasswordIcon" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Pilihan Remember Me -->
                <div class="flex items-center pt-0.5">
                    <label class="flex items-center space-x-2 text-neutral-700 font-medium text-xs cursor-pointer select-none">
                        <input 
                            type="checkbox" 
                            name="remember"
                            class="w-4 h-4 border-neutral-300 rounded accent-black cursor-pointer"
                        >
                        <span>Ingat sesi masuk saya</span>
                    </label>
                </div>

                <!-- Tombol Submit Login -->
                <button 
                    type="submit" 
                    class="w-full bg-black text-white font-medium text-sm py-3 rounded-xl mt-6 hover:bg-neutral-800 active:scale-[0.99] shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 group"
                >
                    <span>Masuk ke Dashboard</span>
                    <i class="fa-solid fa-arrow-right text-xs transition-transform duration-200 group-hover:translate-x-1"></i>
                </button>
            </form>

            <!-- Footer Card: Catatan Keamanan -->
            <div class="mt-6 pt-4 border-t border-neutral-100 flex items-center justify-center gap-2 text-[11px] text-neutral-400">
                <i class="fa-solid fa-shield-halved text-neutral-400"></i>
                <span>Portal Admin SnapRent</span>
            </div>
        </div>

        <!-- Tombol Kembali ke Katalog Customer -->
        <div class="mt-6 text-center">
            <a 
                href="../index.php" 
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-neutral-300 text-xs font-medium text-neutral-600 hover:text-black hover:border-black shadow-sm transition-all duration-200 group"
            >
                <i class="fa-solid fa-arrow-left text-xs transition-transform duration-200 group-hover:-translate-x-1"></i>
                <span>Kembali ke Katalog Rental Kamera</span>
            </a>
        </div>

    </div>

    <!-- Script Interaktif untuk Fitur Toggle Show/Hide Password -->
    <script>
        const passwordInput = document.getElementById('passwordInput');
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');

        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            
            // Ubah icon mata
            if (isPassword) {
                togglePasswordIcon.classList.remove('fa-eye');
                togglePasswordIcon.classList.add('fa-eye-slash');
            } else {
                togglePasswordIcon.classList.remove('fa-eye-slash');
                togglePasswordIcon.classList.add('fa-eye');
            }
        });
    </script>

</body>
</html>
