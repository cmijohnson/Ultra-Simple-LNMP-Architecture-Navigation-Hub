<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance - CMItool</title>
    <script src="assets/vendor/tailwind.min.js"></script>
    <script src="assets/vendor/lucide.min.js"></script>
    <link rel="stylesheet" href="assets/vendor/fonts.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes orbit { from { transform: rotate(0deg) translateX(100px) rotate(0deg); } to { transform: rotate(360deg) translateX(100px) rotate(-360deg); } }
        .orbit-item { animation: orbit 10s linear infinite; }
    </style>
</head>
<body class="bg-zinc-50 dark:bg-black text-zinc-900 dark:text-white flex items-center justify-center min-h-screen overflow-hidden">
    
    <!-- Background Blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-600 rounded-full blur-[150px] opacity-20 animate-pulse"></div>
    </div>

    <div class="max-w-xl w-full p-8 text-center space-y-12 relative">
        <div class="relative inline-block">
             <div class="w-32 h-32 rounded-[2.5rem] bg-gradient-to-br from-blue-500 to-indigo-700 flex items-center justify-center text-white shadow-2xl shadow-blue-500/40 relative z-10">
                 <i data-lucide="wrench" size="48" class="animate-bounce"></i>
             </div>
             <!-- Orbiting elements -->
             <div class="absolute inset-0 orbit-item flex items-center justify-center text-blue-500/20"><i data-lucide="cpu" size="24"></i></div>
             <div class="absolute inset-0 orbit-item flex items-center justify-center text-purple-500/20" style="animation-delay: -3s"><i data-lucide="shield" size="24"></i></div>
             <div class="absolute inset-0 orbit-item flex items-center justify-center text-indigo-500/20" style="animation-delay: -7s"><i data-lucide="zap" size="24"></i></div>
        </div>

        <div class="space-y-4">
            <h1 class="text-5xl font-black tracking-tighter uppercase italic">System <span class="text-blue-600">Upgrade</span></h1>
            <p class="text-zinc-500 dark:text-zinc-400 font-medium text-lg leading-relaxed">
                我们在进行系统升级以接入 LNMP 高性能后端。 预计很快回来，请耐心等待。
            </p>
        </div>

        <div class="p-6 rounded-3xl bg-white/50 dark:bg-white/5 border border-zinc-200 dark:border-white/10 backdrop-blur-xl">
             <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-zinc-400">
                 <span>Status</span>
                 <span class="text-blue-500">Optimizing Performance...</span>
             </div>
             <div class="w-full h-1 bg-zinc-100 dark:bg-white/5 rounded-full mt-4 overflow-hidden">
                 <div class="h-full bg-blue-600 animate-[marquee_2s_linear_infinite]" style="width: 40%"></div>
             </div>
        </div>

        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em]">© 2026 CMItool Labs</p>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
