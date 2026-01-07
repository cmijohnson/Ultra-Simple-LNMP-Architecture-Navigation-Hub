<?php
require_once 'includes/db.php';
$pdo = getDB();
$stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = 'site_status' LIMIT 1");
$stmt->execute();
$status = $stmt->fetchColumn();
if ($status === 'offline') {
    include 'maintenance.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMItool 2.0 - Similan Pavilion Hub</title>
    <meta name="description" content="CMItool 2.0 is a premium open-source utility workstation. Built with modern performance in mind, it operates entirely client-side.">
    <!-- Tailwind CSS (Localized) -->
    <script src="assets/vendor/tailwind.min.js"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'system-ui', 'sans-serif'],
              mono: ['JetBrains Mono', 'monospace'],
            }
          }
        }
      }
    </script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Local Fonts -->
    <link rel="stylesheet" href="assets/vendor/fonts.css">
    <!-- Local Lucide Icons -->
    <script src="assets/vendor/lucide.min.js"></script>
    <!-- Local Mermaid -->
    <script src="assets/vendor/mermaid.min.js"></script>
    
    <style>
      /* Blob Animations */
      @keyframes blob1 { 0% { transform: translate(0,0) scale(1); } 33% { transform: translate(50px, -80px) scale(1.2); } 66% { transform: translate(-30px, 40px) scale(0.9); } 100% { transform: translate(0,0) scale(1); } }
      @keyframes blob2 { 0% { transform: translate(0,0) scale(1); } 33% { transform: translate(-40px, 90px) scale(0.8); } 66% { transform: translate(60px, -50px) scale(1.1); } 100% { transform: translate(0,0) scale(1); } }
      @keyframes blob3 { 0% { transform: translate(0,0); } 50% { transform: translate(100px, 50px); } 100% { transform: translate(0,0); } }
      
      .blob-1 { animation: blob1 20s infinite linear; }
      .blob-2 { animation: blob2 25s infinite linear; }
      .blob-3 { animation: blob3 30s infinite linear; }
    </style>
</head>
<body class="bg-zinc-50 dark:bg-black text-zinc-900 dark:text-white transition-colors duration-500 overflow-x-hidden">

    <div id="app-root" class="min-h-screen flex flex-col relative">
        
        <!-- Entry Loader -->
        <div id="entry-loader" class="fixed inset-0 z-[1000] flex items-center justify-center bg-zinc-50 dark:bg-[#050505] transition-opacity duration-700">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 animate-pulse shadow-2xl shadow-blue-500/50">
                <i data-lucide="terminal" class="text-white w-6 h-6"></i>
            </div>
        </div>

        <!-- Background Blobs -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
            <div class="absolute -top-20 -left-20 w-[600px] h-[600px] bg-blue-600 rounded-full blur-[120px] opacity-20 dark:opacity-10 blob-1"></div>
            <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-purple-600 rounded-full blur-[100px] opacity-20 dark:opacity-10 blob-2"></div>
            <div class="absolute top-1/2 left-1/3 w-[400px] h-[400px] bg-indigo-600 rounded-full blur-[150px] opacity-20 dark:opacity-10 blob-3"></div>
        </div>

        <!-- Command Palette Overlay -->
        <div id="command-palette" class="hidden fixed inset-0 z-[200] bg-black/50 backdrop-blur-sm flex items-start justify-center pt-[20vh] px-4">
             <div class="w-full max-w-2xl bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden border border-zinc-200 dark:border-white/10 animate-fade-in-up">
                 <div class="p-4 border-b border-zinc-200 dark:border-white/10 flex items-center gap-3">
                     <i data-lucide="search" class="text-zinc-400"></i>
                     <input type="text" id="cmd-input" placeholder="Type a command or search..." class="flex-1 bg-transparent outline-none text-lg">
                     <button onclick="toggleCommandPalette(false)" class="p-1 text-zinc-400 hover:text-zinc-600"><i data-lucide="x"></i></button>
                 </div>
                 <div class="p-2 max-h-[300px] overflow-auto text-sm text-zinc-500">
                    <p class="p-4 text-center">Press Enter to select. Esc to close.</p>
                 </div>
             </div>
        </div>

        <!-- Header -->
        <header id="main-header" class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-6 md:px-12 py-4 md:py-5 backdrop-blur-3xl border-b border-zinc-200 dark:border-white/5 bg-white/70 dark:bg-black/70 transition-all duration-500">
            <div class="flex items-center gap-3 cursor-pointer" onclick="closeTool()">
                 <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl flex items-center justify-center bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 shadow-lg shadow-blue-500/20">
                     <i data-lucide="terminal" class="text-white w-5 h-5"></i>
                 </div>
                 <div class="flex flex-col">
                     <span class="text-lg md:text-xl font-black tracking-tighter text-zinc-900 dark:text-white leading-none">CMI<span class="text-blue-500">tool</span></span>
                     <span class="hidden md:inline text-[9px] font-black uppercase tracking-[0.3em] text-zinc-400 dark:text-zinc-600 mt-1" id="header-subtitle">细米兰阁 · 极效中枢</span>
                 </div>
            </div>
            
            <nav class="hidden lg:flex items-center gap-10">
               <a href="#" onclick="setCategory('All')" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-500 hover:text-blue-500 transition-colors">APPS</a>
               <a href="#section-news" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-500 hover:text-blue-500 transition-colors">NEWS</a>
               <a href="#section-partners" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-500 hover:text-blue-500 transition-colors">PARTNERS</a>
            </nav>

            <div class="flex items-center gap-3 md:gap-4">
                <div class="hidden md:flex items-center p-1 rounded-xl bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10">
                   <button id="header-lang-en" onclick="setLanguage('en')" class="px-3 py-1.5 rounded-lg text-[10px] font-black transition-all">EN</button>
                   <button id="header-lang-zh" onclick="setLanguage('zh')" class="px-3 py-1.5 rounded-lg text-[10px] font-black transition-all">中</button>
                </div>
                <div class="flex items-center p-1 rounded-xl bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10">
                   <button onclick="setTheme('light')" class="p-2 rounded-lg text-zinc-400 hover:text-blue-500"><i data-lucide="sun" size="16"></i></button>
                   <button onclick="setTheme('dark')" class="p-2 rounded-lg text-zinc-400 hover:text-blue-400"><i data-lucide="moon" size="16"></i></button>
                   <button onclick="setTheme('system')" class="hidden md:inline-block p-2 rounded-lg text-zinc-400 hover:text-blue-500"><i data-lucide="monitor" size="16"></i></button>
                </div>
                <button onclick="toggleCommandPalette(true)" class="md:hidden p-2.5 rounded-xl bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 text-zinc-500">
                   <i data-lucide="search" size="18"></i>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main id="main-content" class="flex-1 transition-all duration-500 pt-32 pb-24 px-6 md:px-12 max-w-7xl mx-auto w-full">
             <!-- Injected by app.js -->
        </main>

        <!-- Footer -->
        <footer class="py-20 px-10 border-t border-zinc-200 dark:border-white/5 bg-zinc-50 dark:bg-black mt-auto">
             <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 items-start">
                  <div class="flex flex-col gap-4">
                       <div class="flex items-center gap-3 text-zinc-900 dark:text-white font-black text-2xl tracking-tighter">
                           <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 flex items-center justify-center text-white"><i data-lucide="terminal" size="16"></i></div>
                           CMItool 2.0
                       </div>
                       <p class="text-[10px] text-zinc-400 dark:text-zinc-600 font-bold uppercase tracking-[0.3em] leading-relaxed">
                           极致生产力，纯前端驱动。<br>
                           © 2025 细米兰阁 (CMI). ALL RIGHTS RESERVED.
                       </p>
                  </div>
                  <div class="flex flex-col items-center gap-2">
                      <div class="flex gap-8 text-[10px] font-black uppercase tracking-widest text-zinc-400">
                          <button id="footer-btn-msg" onclick="openTool('message-board')" class="hover:text-blue-500 transition-colors uppercase">留言</button>
                          <button id="footer-btn-privacy" onclick="openTool('privacy-policy')" class="hover:text-blue-500 transition-colors uppercase">隐私政策</button>
                          <button id="footer-btn-team" onclick="openTool('team-introduction')" class="hover:text-blue-500 transition-colors uppercase">团队介绍</button>
                      </div>
                  </div>
                  <div class="flex flex-col md:items-end gap-2 text-[10px] font-mono font-bold text-zinc-400 dark:text-zinc-700">
                      <a href="https://beian.miit.gov.cn/" target="_blank" class="hover:text-blue-500 transition-colors">苏ICP备2024068081号-1</a>
                      <a href="https://www.beian.gov.cn/portal/registerSystemInfo?recordcode=32099502000473" target="_blank" class="hover:text-blue-500 transition-colors">苏公网安备32099502000473号</a>
                      <div class="flex items-center gap-2 mt-2">
                          <i data-lucide="shield-check" size="12" class="text-blue-500/50"></i>
                          <span class="opacity-40 tracking-tighter">CMItool Labs · 细米科技工作室</span>
                      </div>
                  </div>
             </div>
        </footer>

        <!-- Mobile Dock -->
        <div id="mobile-dock" class="md:hidden fixed bottom-0 left-0 right-0 z-[100] pb-[env(safe-area-inset-bottom,1.5rem)] px-6 pointer-events-none transition-transform duration-500">
            <div class="pointer-events-auto bg-white/90 dark:bg-zinc-900/90 backdrop-blur-2xl border border-zinc-200 dark:border-white/10 rounded-[2.5rem] p-2 flex items-center justify-between shadow-[0_-10px_40px_rgba(0,0,0,0.1)] mb-6">
                <button onclick="setCategory('All')" class="flex-1 flex flex-col items-center gap-1 p-3 rounded-2xl transition-all text-zinc-400 focus:text-blue-600">
                    <i data-lucide="home" size="22"></i>
                </button>
                <button onclick="setCategory('Recommend')" class="flex-1 flex flex-col items-center gap-1 p-3 rounded-2xl transition-all text-zinc-400 focus:text-blue-600">
                    <i data-lucide="layers" size="22"></i>
                </button>
                <div class="relative -top-8">
                    <button onclick="toggleCommandPalette(true)" class="w-14 h-14 rounded-full bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white flex items-center justify-center shadow-2xl shadow-blue-500/40 border-4 border-white dark:border-zinc-900 active:scale-90 transition-transform">
                        <i data-lucide="search" size="24"></i>
                    </button>
                </div>
                 <button onclick="setTheme(state.theme === 'dark' ? 'light' : 'dark')" class="flex-1 flex flex-col items-center gap-1 p-3 rounded-2xl text-zinc-400">
                    <i data-lucide="sun-moon" size="22"></i>
                </button>
                 <button onclick="setLanguage(state.language === 'en' ? 'zh' : 'en')" class="flex-1 flex flex-col items-center gap-1 p-3 rounded-2xl text-zinc-400">
                    <i data-lucide="globe" size="22"></i>
                </button>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="assets/js/data.js"></script>
    <script src="assets/js/tools.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        // Start App
        document.addEventListener('DOMContentLoaded', () => {
             window.initApp();
        });
    </script>
</body>
</html>
