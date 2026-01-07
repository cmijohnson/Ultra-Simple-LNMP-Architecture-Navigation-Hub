<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin_user']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = getDB();

// Handle Actions
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $status = $_POST['site_status'];
        $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE `key` = 'site_status'");
        $stmt->execute([$status]);
        
        // Handle Board Status
        $boardStatus = $_POST['board_status'] ?? 'enabled';
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES ('board_status', ?) ON DUPLICATE KEY UPDATE `value` = ?");
        $stmt->execute([$boardStatus, $boardStatus]);
        
        $msg = "系统设置更新成功。";
    }
    
    if ($_POST['action'] === 'update_team_intro') {
        $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE `key` = 'team_intro_zh'");
        $stmt->execute([$_POST['team_intro_zh']]);
        $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE `key` = 'team_intro_en'");
        $stmt->execute([$_POST['team_intro_en']]);
        $msg = "团队资料已同步。";
    }

    if ($_POST['action'] === 'add_tool') {
        $slug = $_POST['slug'] ?: 'app-' . time();
        $stmt = $pdo->prepare("INSERT INTO tools (slug, name_zh, name_en, desc_zh, desc_en, url, icon, category, size) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$slug, $_POST['name_zh'], $_POST['name_en'], $_POST['desc_zh'], $_POST['desc_en'], $_POST['url'], $_POST['icon'], $_POST['category'], $_POST['size']]);
        $msg = "导航模块已添加。";
    }

    if ($_POST['action'] === 'delete_tool') {
        $stmt = $pdo->prepare("DELETE FROM tools WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $msg = "模块已下线。";
    }

    if ($_POST['action'] === 'gen_invite') {
        $code = strtoupper(substr(md5(uniqid()), 0, 8));
        $stmt = $pdo->prepare("INSERT INTO invitations (code, created_by) VALUES (?, ?)");
        $stmt->execute([$code, 1]);
        $msg = "新生成的邀请码: $code";
    }

    if ($_POST['action'] === 'add_log') {
        $items = json_encode(array_filter(array_map('trim', explode("\n", $_POST['items']))));
        $stmt = $pdo->prepare("INSERT INTO changelogs (version, date, title_zh, title_en, type, items_json) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['version'], $_POST['date'], $_POST['title_zh'], $_POST['title_en'], $_POST['type'], $items]);
        $msg = "更新日志已提交。";
    }
}

// Stats
$toolCount = $pdo->query("SELECT COUNT(*) FROM tools")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$msgCount = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

// Fetch Data
$tools = $pdo->query("SELECT * FROM tools ORDER BY sort_order ASC, id DESC")->fetchAll();
$changelogs = $pdo->query("SELECT * FROM changelogs ORDER BY date DESC, version DESC")->fetchAll();
$messages = $pdo->query("SELECT * FROM messages ORDER BY id DESC LIMIT 15")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMI 管理控制台</title>
    <script src="assets/vendor/tailwind.min.js"></script>
    <script src="assets/vendor/lucide.min.js"></script>
    <link rel="stylesheet" href="assets/vendor/fonts.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06); backdrop-filter: blur(10px); }
        .sidebar-link.active { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-right: 3px solid #3b82f6; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    </style>
</head>
<body class="bg-[#050505] text-white min-h-screen flex selection:bg-blue-500/30">
    
    <!-- Sidebar -->
    <aside class="w-72 border-r border-white/5 bg-black flex flex-col p-8 space-y-12 shrink-0">
        <div class="flex items-center gap-3">
             <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20"><i data-lucide="terminal" size="20"></i></div>
             <span class="text-2xl font-black tracking-tighter italic uppercase">Admin<span class="text-blue-500">Hub</span></span>
        </div>

        <nav class="flex-1 space-y-1">
            <a href="#" class="sidebar-link active flex items-center gap-4 py-4 px-6 rounded-xl transition-all font-bold text-[10px] uppercase tracking-[0.2em]"><i data-lucide="layout-grid" size="18"></i> 控制台</a>
            <a href="#tools" class="sidebar-link flex items-center gap-4 py-4 px-6 rounded-xl transition-all font-bold text-[10px] uppercase tracking-[0.2em] text-zinc-500 hover:text-white"><i data-lucide="box" size="18"></i> 导航管理</a>
            <a href="#logs" class="sidebar-link flex items-center gap-4 py-4 px-6 rounded-xl transition-all font-bold text-[10px] uppercase tracking-[0.2em] text-zinc-500 hover:text-white"><i data-lucide="history" size="18"></i> 更新日志</a>
            <a href="#users" class="sidebar-link flex items-center gap-4 py-4 px-6 rounded-xl transition-all font-bold text-[10px] uppercase tracking-[0.2em] text-zinc-500 hover:text-white"><i data-lucide="users" size="18"></i> 会员管理</a>
        </nav>

        <div class="pt-8 border-t border-white/5">
             <div class="flex items-center gap-3 mb-6">
                 <div class="w-10 h-10 rounded-full bg-blue-600/10 flex items-center justify-center font-black text-blue-500 border border-blue-500/20"><?php echo strtoupper(substr($_SESSION['admin_user'], 0, 1)); ?></div>
                 <div><p class="text-[9px] font-black uppercase text-zinc-500 tracking-widest">当前操作员</p><p class="text-xs font-bold"><?php echo $_SESSION['admin_user']; ?></p></div>
             </div>
             <a href="logout.php" class="flex items-center gap-3 text-red-500 font-black text-[10px] uppercase tracking-widest hover:pl-2 transition-all"><i data-lucide="power" size="14"></i> 退出登录</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-12 overflow-auto relative space-y-16">
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-blue-600/5 blur-[180px] pointer-events-none -z-10"></div>

        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-5xl font-black tracking-tighter italic uppercase">控制台</h1>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.3em] mt-2">中央管理与系统分析</p>
            </div>
            
            <form method="POST" class="flex items-center gap-4 bg-white/5 border border-white/5 p-2 rounded-[1.5rem] backdrop-blur-xl">
                <input type="hidden" name="action" value="update_status">
                <?php
                   $stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = 'site_status'");
                   $stmt->execute();
                   $st = $stmt->fetchColumn();
                   
                   $stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = 'board_status'");
                   $stmt->execute();
                   $bst = $stmt->fetchColumn() ?: 'enabled';
                ?>
                <div class="flex items-center gap-2 px-4 border-r border-white/5">
                    <div class="w-2 h-2 rounded-full <?php echo $st === 'online' ? 'bg-green-500 animate-pulse' : 'bg-red-500'; ?>"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">系统</span>
                </div>
                <select name="site_status" class="bg-transparent border-none outline-none px-2 py-2 text-xs font-bold appearance-none cursor-pointer">
                    <option value="online" class="bg-black" <?php if ($st === 'online') echo 'selected'; ?>>运行中</option>
                    <option value="offline" class="bg-black" <?php if ($st === 'offline') echo 'selected'; ?>>维护中</option>
                </select>
                
                <div class="w-px h-6 bg-white/10 mx-2"></div>

                <div class="flex items-center gap-2 px-2">
                     <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">留言板</span>
                </div>
                <select name="board_status" class="bg-transparent border-none outline-none px-2 py-2 text-xs font-bold appearance-none cursor-pointer">
                    <option value="enabled" class="bg-black" <?php if ($bst === 'enabled') echo 'selected'; ?>>开启</option>
                    <option value="disabled" class="bg-black" <?php if ($bst === 'disabled') echo 'selected'; ?>>关闭</option>
                </select>

                <button type="submit" class="ml-4 bg-white text-black px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:scale-105 active:scale-95 transition-all">应用更改</button>
            </form>
        </header>

        <?php if ($msg): ?>
            <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-500 text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-3 animate-fade-in">
                <i data-lucide="check-circle" size="16"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="glass p-10 rounded-[2.5rem] space-y-2">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">活跃应用数</p>
                <p class="text-6xl font-black italic tracking-tighter"><?php echo $toolCount; ?></p>
            </div>
            <div class="glass p-10 rounded-[2.5rem] space-y-2">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">注册会员数</p>
                <p class="text-6xl font-black italic tracking-tighter"><?php echo $userCount; ?></p>
            </div>
            <div class="glass p-10 rounded-[2.5rem] border-blue-500/20 space-y-2">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">留言反馈数</p>
                <p class="text-6xl font-black italic tracking-tighter"><?php echo $msgCount; ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-12">
            
            <div class="xl:col-span-2 space-y-16">
                <!-- Navigation Management -->
                <section id="tools" class="space-y-8">
                    <div class="flex items-center justify-between">
                        <h2 class="text-3xl font-black uppercase italic tracking-tighter">导航中枢</h2>
                        <button onclick="document.getElementById('tool-modal').classList.toggle('hidden')" class="px-6 py-3 rounded-[1.2rem] bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-all flex items-center gap-2"><i data-lucide="plus" size=14></i> 新建应用</button>
                    </div>

                    <div id="tool-modal" class="hidden glass p-10 rounded-[3rem] space-y-8 animate-fade-in-up">
                         <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                             <input type="hidden" name="action" value="add_tool">
                             <div class="space-y-3"><label class="text-[10px] font-black uppercase text-zinc-500 ml-4">中文标题</label><input type="text" name="name_zh" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-blue-500 text-sm font-bold"></div>
                             <div class="space-y-3"><label class="text-[10px] font-black uppercase text-zinc-500 ml-4">英文标题</label><input type="text" name="name_en" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-blue-500 text-sm font-bold"></div>
                             <div class="md:col-span-2 space-y-3"><label class="text-[10px] font-black uppercase text-zinc-500 ml-4">链接 (外部链接或组件留空)</label><input type="text" name="url" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-blue-500 font-mono text-xs"></div>
                             <div class="space-y-3"><label class="text-[10px] font-black uppercase text-zinc-500 ml-4">图标 (Lucide Icon)</label><select name="icon" class="w-full bg-black border border-white/10 rounded-2xl px-6 py-4 outline-none"><option value="zap">Zap</option><option value="bot">Bot</option><option value="code">Code</option><option value="globe">Globe</option></select></div>
                             <div class="space-y-3"><label class="text-[10px] font-black uppercase text-zinc-500 ml-4">分类</label><select name="category" class="w-full bg-black border border-white/10 rounded-2xl px-6 py-4 outline-none"><option value="Recommend">Featured (推荐)</option><option value="Tool">Utility (工具)</option></select></div>
                             <button type="submit" class="md:col-span-2 py-5 rounded-2xl bg-blue-600 font-black uppercase tracking-widest text-xs">添加模块</button>
                         </form>
                    </div>

                    <div class="glass rounded-[2.5rem] overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-white/5 text-[9px] font-black uppercase tracking-[0.3em] text-zinc-500 border-b border-white/5">
                                <tr><th class="p-8">应用名称</th><th class="p-8">分类</th><th class="p-8">操作</th></tr>
                            </thead>
                            <tbody class="text-xs font-bold divide-y divide-white/5">
                                <?php foreach($tools as $t): ?>
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="p-8"><div class="flex items-center gap-4"><i data-lucide="<?php echo $t['icon']; ?>" size=16 class="text-blue-500"></i> <?php echo $t['name_zh']; ?></div></td>
                                        <td class="p-8 text-zinc-500 uppercase tracking-widest text-[9px]"><?php echo $t['category']; ?></td>
                                        <td class="p-8">
                                             <form method="POST" onsubmit="return confirm('确认删除?');">
                                                 <input type="hidden" name="action" value="delete_tool"><input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                                 <button type="submit" class="text-zinc-600 hover:text-red-500 transition-colors"><i data-lucide="trash-2" size=16></i></button>
                                             </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Changelog Management -->
                <section id="logs" class="space-y-8">
                    <div class="flex items-center justify-between">
                        <h2 class="text-3xl font-black uppercase italic tracking-tighter">系统历史</h2>
                        <button onclick="document.getElementById('log-modal').classList.toggle('hidden')" class="px-6 py-3 rounded-[1.2rem] bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-all">添加记录</button>
                    </div>
                    
                    <div id="log-modal" class="hidden glass p-10 rounded-[3rem] animate-fade-in-up">
                         <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                             <input type="hidden" name="action" value="add_log">
                             <div class="space-y-3"><label class="text-[10px] font-black uppercase text-zinc-500 ml-4">版本号 (Version)</label><input type="text" name="version" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-indigo-500 font-bold"></div>
                             <div class="space-y-3"><label class="text-[10px] font-black uppercase text-zinc-500 ml-4">日期 (Date)</label><input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-indigo-500 font-bold"></div>
                             <div class="md:col-span-2 space-y-3"><label class="text-[10px] font-black uppercase text-zinc-500 ml-4">标题 (ZH)</label><input type="text" name="title_zh" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-indigo-500 font-bold"></div>
                             <div class="md:col-span-2 space-y-3"><label class="text-[10px] font-black uppercase text-zinc-500 ml-4">更新条目 (JSON format or lines)</label><textarea name="items" required class="w-full h-32 bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-indigo-500 text-xs font-medium resize-none"></textarea></div>
                             <button type="submit" class="md:col-span-2 py-5 rounded-2xl bg-indigo-600 font-black uppercase tracking-widest text-xs">提交记录</button>
                         </form>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <?php foreach($changelogs as $l): ?>
                            <div class="glass p-8 rounded-[2rem] border-l-4 border-zinc-800 hover:border-indigo-500 transition-all group">
                                <div class="flex items-center justify-between mb-4">
                                     <span class="font-black text-2xl group-hover:text-indigo-400">v<?php echo $l['version']; ?></span>
                                     <span class="text-[9px] font-black uppercase text-zinc-600"><?php echo $l['date']; ?></span>
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500"><?php echo $l['title_zh']; ?></p>
                            </div>
                         <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <!-- Profile & Access -->
            <div class="space-y-16">
                 <section class="space-y-8">
                     <h2 class="text-2xl font-black uppercase italic tracking-tighter">团队资料</h2>
                     <form method="POST" class="glass p-10 rounded-[2.5rem] space-y-8">
                         <input type="hidden" name="action" value="update_team_intro">
                         <?php
                            $intro_zh = $pdo->query("SELECT value FROM settings WHERE `key` = 'team_intro_zh'")->fetchColumn();
                            $intro_en = $pdo->query("SELECT value FROM settings WHERE `key` = 'team_intro_en'")->fetchColumn();
                         ?>
                         <div class="space-y-3">
                              <label class="text-[10px] font-black uppercase text-zinc-500 ml-4">内容 (ZH)</label>
                              <textarea name="team_intro_zh" class="w-full h-32 bg-black/40 border border-white/10 rounded-[1.5rem] px-6 py-4 outline-none focus:border-blue-500 text-xs font-medium leading-relaxed resize-none"><?php echo $intro_zh; ?></textarea>
                         </div>
                         <div class="space-y-3">
                              <label class="text-[10px] font-black uppercase text-zinc-500 ml-4">内容 (EN)</label>
                              <textarea name="team_intro_en" class="w-full h-32 bg-black/40 border border-white/10 rounded-[1.5rem] px-6 py-4 outline-none focus:border-blue-500 text-xs font-medium leading-relaxed resize-none"><?php echo $intro_en; ?></textarea>
                         </div>
                         <button type="submit" class="w-full py-5 rounded-2xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all">同步元数据</button>
                     </form>
                 </section>

                 <section id="users" class="space-y-8">
                     <div class="flex items-center justify-between">
                         <h2 class="text-2xl font-black uppercase italic tracking-tighter">注册邀请邀请码</h2>
                         <form method="POST"><input type="hidden" name="action" value="gen_invite"><button type="submit" class="w-10 h-10 rounded-xl bg-purple-600 flex items-center justify-center text-white shadow-lg shadow-purple-500/20"><i data-lucide="plus" size=18></i></button></form>
                     </div>
                     <div class="space-y-3">
                         <?php
                           $invites = $pdo->query("SELECT * FROM invitations WHERE is_used = 0 ORDER BY id DESC LIMIT 8")->fetchAll();
                           foreach($invites as $inv):
                         ?>
                           <div class="glass px-6 py-4 rounded-2xl flex items-center justify-between border-purple-500/10 hover:border-purple-500/30 transition-all">
                               <span class="font-mono text-sm font-black text-purple-400 tracking-wider"><?php echo $inv['code']; ?></span>
                               <span class="text-[8px] font-black uppercase text-zinc-600">待使用</span>
                           </div>
                         <?php endforeach; if(empty($invites)) echo '<p class="text-center text-zinc-700 py-10 italic text-[10px] uppercase tracking-widest font-bold">暂无可用邀请码</p>'; ?>
                     </div>
                 </section>

                 <section id="messages" class="space-y-8">
                     <h2 class="text-2xl font-black uppercase italic tracking-tighter">动态订阅</h2>
                     <div class="space-y-4">
                          <?php foreach($messages as $m): ?>
                            <div class="glass p-6 rounded-2xl border-l-4 <?php echo $m['is_admin'] ? 'border-blue-500' : 'border-zinc-800'; ?> space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-black text-[10px] uppercase tracking-widest"><?php echo htmlspecialchars($m['username']); ?></span>
                                    <span class="text-[8px] font-bold text-zinc-600 uppercase"><?php echo date('M d, H:i', strtotime($m['created_at'])); ?></span>
                                </div>
                                <p class="text-xs text-zinc-400 leading-relaxed"><?php echo htmlspecialchars($m['content']); ?></p>
                            </div>
                          <?php endforeach; ?>
                     </div>
                 </section>
            </div>

        </div>

    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
