<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['admin_user'])) {
    if ($_SESSION['admin_role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_user'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];
        
        if ($user['role'] === 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CMItool</title>
    <script src="assets/vendor/tailwind.min.js"></script>
    <script src="assets/vendor/lucide.min.js"></script>
    <link rel="stylesheet" href="assets/vendor/fonts.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .lang-active { color: #3b82f6; font-weight: 900; }
    </style>
</head>
<body class="bg-[#050505] text-white flex items-center justify-center min-h-screen relative">
    
    <div class="fixed top-8 right-8 z-50 flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-zinc-500">
        <button id="lang-en" onclick="setLang('en')" class="hover:text-white transition-colors">EN</button>
        <span class="text-zinc-700">/</span>
        <button id="lang-zh" onclick="setLang('zh')" class="hover:text-white transition-colors">中文</button>
    </div>

    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-blue-600 rounded-full blur-[150px] opacity-10"></div>
    </div>

    <div class="w-full max-w-md p-8">
        <div class="glass rounded-[2.5rem] p-10 space-y-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 blur-3xl"></div>
            
            <div class="text-center space-y-2">
                <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center mx-auto mb-6 shadow-xl shadow-blue-500/20">
                    <i data-lucide="shield-lock" size="32"></i>
                </div>
                <h1 class="text-3xl font-black tracking-tighter uppercase italic" data-zh="用户登录" data-en="User Login">User Login</h1>
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest" data-zh="CMI Tool 认证中心" data-en="Authentication Center">Authentication Center</p>
            </div>

            <?php if ($error): ?>
                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-xs font-bold text-center animate-shake">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4" data-zh="用户名" data-en="Username">Username</label>
                    <input type="text" name="username" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-blue-500/50 transition-all font-bold">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4" data-zh="密码" data-en="Password">Password</label>
                    <input type="password" name="password" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-blue-500/50 transition-all font-bold">
                </div>
                <button type="submit" class="w-full py-5 rounded-2xl bg-blue-600 text-white font-black uppercase tracking-widest text-xs hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-blue-500/20 flex items-center justify-center gap-3">
                    <span data-zh="登录" data-en="Sign In">Sign In</span> <i data-lucide="arrow-right" size="16"></i>
                </button>
            </form>

            <div class="text-center">
                <a href="register.php" class="text-[10px] font-black text-zinc-500 hover:text-blue-500 transition-colors uppercase tracking-widest" data-zh="没有账号？使用邀请码注册" data-en="Need access? Join with invite code">Need access? Join with invite code</a>
            </div>
        </div>
        <p class="text-center text-[10px] font-bold text-zinc-700 uppercase tracking-[0.4em] mt-8">© 2026 CMItool Labs</p>
    </div>

    <script>
        lucide.createIcons();

        function setLang(lang) {
            document.querySelectorAll('[data-' + lang + ']').forEach(el => {
                el.innerText = el.getAttribute('data-' + lang);
            });
            document.getElementById('lang-en').classList.toggle('lang-active', lang === 'en');
            document.getElementById('lang-zh').classList.toggle('lang-active', lang === 'zh');
            localStorage.setItem('cmi_admin_lang', lang);
        }

        // Init Lang
        const saved = localStorage.getItem('cmi_admin_lang') || 'zh';
        setLang(saved);
    </script>
</body>
</html>
