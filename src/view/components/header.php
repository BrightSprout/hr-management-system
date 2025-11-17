<?php
if (!isset($user)) {
    $user = [
        'name' => 'GMAC',
        'role' => 'SuperPower',
        'avatar' => 'TI'
    ];
}
?>
<!-- Futuristic Header -->
<header class="relative custom-gradient-bg shadow-2xl">
    <!-- Animated Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-full h-full pattern-bg"></div>
    </div>

    <div class="relative z-10 px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Left Section - Logo & Brand -->
            <div class="flex items-center space-x-6">
                <button id="mobile-menu-btn"
                    class="lg:hidden text-white hover:bg-white/10 p-2 rounded-lg transition-colors">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>

                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 shadow-xl">
                            <i data-lucide="shield" class="w-8 h-8 text-white"></i>
                        </div>
                        <div
                            class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center">
                            <i data-lucide="zap" class="w-2.5 h-2.5 text-yellow-900"></i>
                        </div>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-2xl font-bold text-white tracking-tight">GMAC NEXUS</h1>
                        <p class="text-white/80 text-sm font-medium">SuperPower Command Interface</p>
                    </div>
                </div>
            </div>

            <!-- Center Section - Advanced Search -->
            <div class="hidden md:flex flex-1 max-w-2xl mx-8">
                <div class="relative w-full group">
                    <div
                        class="absolute inset-0 bg-white/10 rounded-2xl blur-sm group-focus-within:blur-none transition-all duration-300">
                    </div>
                </div>
            </div>

            <!-- Right Section - Controls -->
            <div class="flex items-center space-x-4">
                <div
                    class="flex items-center space-x-3 bg-white/10 backdrop-blur-md rounded-2xl px-4 py-2 border border-white/20">
                    <div
                        class="h-10 w-10 bg-white brand-red font-bold rounded-full flex items-center justify-center ring-2 ring-white/30">
                        <?php echo $user['avatar']; ?>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-bold text-white"><?php echo $userData->username; ?></p>
                        <p class="text-xs text-white/70 flex items-center">
                            <i data-lucide="star" class="w-3 h-3 mr-1 text-yellow-400"></i>
                            <?php echo $userData->role; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
