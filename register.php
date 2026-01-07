<?php
session_start();
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $invite_code = $_POST['invite_code'] ?? '';

    if (empty($username) || empty($password) || empty($invite_code)) {
        $error = 'All fields are required.';
    } else {
        $pdo = getDB();
        
        // Check Invite Code
        $stmt = $pdo->prepare("SELECT id FROM invitations WHERE code = ? AND is_used = 0");
        $stmt->execute([$invite_code]);
        $invitation = $stmt->fetch();

        if (!$invitation) {
            $error = 'Invalid or used invitation code.';
        } else {
            // Check if username exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username already exists.';
            } else {
                // Create User
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role, invite_code) VALUES (?, ?, 'user', ?)");
                $stmt->execute([$username, $hashed_password, $invite_code]);

                // Mark invite as used
                $stmt = $pdo->prepare("UPDATE invitations SET is_used = 1 WHERE id = ?");
                $stmt->execute([$invitation['id']]);

                $success = 'Registration successful! You can now login.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CMItool</title>
    <script src="assets/vendor/tailwind.min.js"></script>
    <script src="assets/vendor/lucide.min.js"></script>
    <link rel="stylesheet" href="assets/vendor/fonts.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .lang-active { color: #a855f7; font-weight: 900; }
    </style>
</head>
<body class="bg-[#050505] text-white flex items-center justify-center min-h-screen relative">
    
    <div class="fixed top-8 right-8 z-50 flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-zinc-500">
        <button id="lang-en" onclick="setLang('en')" class="hover:text-white transition-colors">EN</button>
        <span class="text-zinc-700">/</span>
        <button id="lang-zh" onclick="setLang('zh')" class="hover:text-white transition-colors">中文</button>
    </div>

    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-purple-600 rounded-full blur-[150px] opacity-10"></div>
    </div>

    <div class="w-full max-w-md p-8">
        <div class="glass rounded-[2.5rem] p-10 space-y-8 shadow-2xl">
            <div class="text-center space-y-2">
                <div class="w-16 h-16 rounded-2xl bg-purple-600 flex items-center justify-center mx-auto mb-6 shadow-xl shadow-purple-500/20">
                    <i data-lucide="user-plus" size="32"></i>
                </div>
                <h1 class="text-3xl font-black tracking-tighter uppercase italic" data-zh="加入 CMItool" data-en="Join CMItool">Join <span class="text-purple-500">CMItool</span></h1>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest" data-zh="仅限邀请注册" data-en="Invitation Only Registration">Invitation Only Registration</p>
            </div>

            <?php if ($error): ?>
                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-xs font-bold text-center">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-500 text-xs font-bold text-center">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4" data-zh="邀请码" data-en="Invite Code">Invite Code</label>
                    <input type="text" name="invite_code" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-purple-500/50 transition-all font-mono font-bold text-purple-400">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4" data-zh="用户名" data-en="Username">Username</label>
                    <input type="text" name="username" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-blue-500/50 transition-all font-bold">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4" data-zh="密码" data-en="Password">Password</label>
                    <input type="password" name="password" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-blue-500/50 transition-all font-bold">
                </div>
                <button type="submit" class="w-full py-5 rounded-2xl bg-purple-600 text-white font-black uppercase tracking-widest text-xs hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-purple-500/20">
                    <span data-zh="注册账户" data-en="Register Account">Register Account</span>
                </button>
            </form>

            <div class="text-center">
                <a href="login.php" class="text-[10px] font-black text-zinc-500 hover:text-white transition-colors uppercase tracking-widest" data-zh="返回登录" data-en="Back to Login">Back to Login</a>
            </div>
        </div>
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
