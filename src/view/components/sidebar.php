<!-- Creative Sidebar -->
<aside id="sidebar" class="sidebar-closed lg:sidebar-open  flex flex-col">
    <div class="flex flex-col h-full">
        <!-- Sidebar Header -->
        <div class="p-6 bg-gradient-to-br from-gray-50 to-white border-b border-gray-200/50">
            <div class="flex items-center justify-between lg:hidden mb-4">
                <span class="text-lg font-bold text-gray-900">Navigation</span>
                <button id="close-sidebar" class="p-2 hover:bg-gray-100 rounded-lg">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <!-- Power Status Card -->
            <div class="custom-gradient-bg rounded-2xl p-4 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-10 translate-x-10">
                </div>
                <div class="relative z-10">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                            <i data-lucide="zap" class="w-5 h-5 text-yellow-300"></i>
                        </div>
                        <span class="font-bold">SUPERPOWER MODE</span>
                    </div>
                    <p class="text-white/80 text-sm">All systems operational</p>
                    <div class="flex items-center mt-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2 pulse-animation"></div>
                        <span class="text-xs text-white/70">Status: ACTIVE</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scrollable Content Area -->
        <div class="flex-1 min-h-0 overflow-hidden">
            <!-- Navigation Items -->
            <nav id="navigator"
                class="h-full p-4 space-y-2 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent">
                <?php foreach ($navigationItems as $index => $item): ?>
                    <button data-navfor="?page=<?php echo $item['id']; ?>"
                        class="nav-item w-full group relative overflow-hidden rounded-2xl transition-all duration-300 transform hover:scale-[1.02] <?php echo $activeItem === $item['id'] ? 'active' : ''; ?> <?php echo $item['color']; ?>"
                        style="animation-delay: <?php echo $index * 50; ?>ms" data-color="<?php echo $item['color']; ?>">
                        <div
                            class="nav-gradient absolute inset-0 bg-gradient-to-r <?php echo $item['color']; ?> opacity-90 transition-opacity duration-300">
                        </div>
                        <div class="relative z-10 flex items-center justify-between p-4">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="nav-icon w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-300">
                                    <i data-lucide="<?php echo $item['icon']; ?>"
                                        class="h-6 w-6 transition-colors duration-300"></i>
                                </div>
                                <div class="text-left">
                                    <p class="nav-label font-bold transition-colors duration-300">
                                        <?php echo $item['label']; ?>
                                    </p>
                                    <p class="nav-status text-sm transition-colors duration-300">Ready</p>
                                </div>
                            </div>
                            <?php if (isset($item['badge'])): ?>
                            <span class="<?php echo $item["id"] . "-total";?> nav-badge px-2 py-1 rounded text-xs font-bold transition-all duration-300">
                              <?php echo $item["badge"]; ?>     
                            </span>
                            <?php endif; ?>
                        </div>
                    </button>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-gray-200/50 bg-gradient-to-t from-gray-50 to-transparent">
            <div class="space-y-2">
                <form action="api/logout">
                    <button
                        class="w-full flex items-center justify-start px-3 py-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                        <i data-lucide="log-out" class="h-5 w-5 mr-3"></i>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
