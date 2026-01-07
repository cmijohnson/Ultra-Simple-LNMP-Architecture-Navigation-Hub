-- CMItool 2.0 Database Schema

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. System Settings
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(50) UNIQUE NOT NULL,
    `value` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Tools / Apps
CREATE TABLE IF NOT EXISTS `tools` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(50) UNIQUE NOT NULL,
    `name_en` VARCHAR(100) NOT NULL,
    `name_zh` VARCHAR(100) NOT NULL,
    `desc_en` TEXT,
    `desc_zh` TEXT,
    `icon` VARCHAR(50) DEFAULT 'terminal',
    `category` ENUM('Recommend', 'International', 'SmartUJS', 'Tool', 'Info', 'External') DEFAULT 'Tool',
    `sub_category` VARCHAR(50) DEFAULT NULL,
    `size` ENUM('small', 'medium', 'large') DEFAULT 'medium',
    `url` VARCHAR(255) DEFAULT NULL,
    `component` VARCHAR(50) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0
);

-- 3. Changelogs
CREATE TABLE IF NOT EXISTS `changelogs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `version` VARCHAR(20) NOT NULL,
    `date` DATE NOT NULL,
    `title_en` VARCHAR(255),
    `title_zh` VARCHAR(255),
    `type` ENUM('Major', 'Update', 'Minor', 'History') DEFAULT 'Update',
    `items_json` JSON, -- Store items as a JSON array
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Users & Admins
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user') DEFAULT 'user',
    `invite_code` VARCHAR(20),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Invitation Codes
CREATE TABLE IF NOT EXISTS `invitations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) UNIQUE NOT NULL,
    `is_used` TINYINT(1) DEFAULT 0,
    `created_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Message Board
CREATE TABLE IF NOT EXISTS `messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL,
    `content` TEXT NOT NULL,
    `is_admin` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --- SEED DATA ---

-- Initial Settings
INSERT INTO `settings` (`key`, `value`) VALUES 
('site_status', 'online'),
('team_intro_zh', 'CMI团队 自2018年8月7日成立，为大学生学习创新合作发展的团队。本团队目前由 江苏大学土力学院团委、盐城市亭湖区雅和社区 等进行工作指导。'),
('team_intro_en', 'CMI Team was established on August 7, 2018, as a team for college students to learn, innovate and cooperate. The team is currently guided by the Youth League Committee of the School of Civil Engineering and Mechanics of Jiangsu University and the Yahe Community of Tinghu District, Yancheng City.');

-- Initial Admin (Password: admin123 - change this after login)
-- Using PHP password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `users` (`username`, `password`, `role`) VALUES 
('admin', '$2y$10$89W1hGIsfGfXFh/xGZ7PoeZ6e6.P5wWpJyG/Yn7x1M.Wv0M1i3J6e', 'admin');

-- Seed Tools
-- Seed Tools (Full Dataset)
INSERT INTO `tools` (`slug`, `name_en`, `name_zh`, `desc_en`, `desc_zh`, `icon`, `category`, `sub_category`, `size`, `url`, `component`) VALUES
-- Recommend
('mytan-ai', 'MyTan AI', 'MyTan 人工智能助手', 'Global leading AI assistant powered by Gemini.', '全能人工智能助手，全球遥遥领先，集合Gemini等牛掰模型的内测服务。', 'bot', 'Recommend', NULL, 'large', 'https://mytan.maiseed.com.cn/chat', NULL),
('blog-center', 'Blog Center', '博客中心', 'CMI Team blog portal.', 'CMI团队博客门户，获取最新动态、技术文章与社区互动。', 'home', 'Recommend', NULL, 'medium', 'https://www.cmiteam.top/wordpress', NULL),
('isas-platform', 'ISAS Platform', 'ISAS起零平台', 'High-quality API resources.', '面向企业与个人提供免费、全面且高质量的 API 数据接口资源。', 'zap', 'Recommend', NULL, 'medium', 'https://api.istero.com', NULL),
('ujs-speed-test', 'UJS Speed Test', '细米测速助手', 'Network speed and IP detection.', '您的专属测速网站，提供基础测速和 IP 检测，助您高效解决问题。', 'gauge', 'Recommend', NULL, 'medium', 'https://speed.cmiteam.cn/', NULL),
('grad-2024', '2024 Memory', '2024 毕业回忆', 'Precious moments of Class 11.', '重温高中三年珍贵瞬间，记录我们的青春微电影。', 'film', 'Recommend', NULL, 'small', 'https://www.cmiteam.top/index/memory/', NULL),

-- International
('gemini', 'Gemini', 'Google Gemini', 'Google\'s most capable AI.', '谷歌最强多模态 AI，支持多语言对话、编码与创作。', 'sparkles', 'International', NULL, 'medium', 'https://gemini.google.com', NULL),
('claude', 'Claude', 'Claude AI', 'Anthropic\'s intelligent assistant.', 'Anthropic 开发的高智能、安全 AI，擅长长文本处理。', 'brain', 'International', NULL, 'medium', 'https://claude.ai', NULL),
('chatgpt', 'ChatGPT', 'ChatGPT', 'OpenAI\'s conversational AI.', '全球知名的 AI 对话系统，基于 GPT-4o 架构。', 'message-square', 'International', NULL, 'medium', 'https://chatgpt.com', NULL),
('deepl', 'DeepL', 'DeepL 翻译', 'World\'s most accurate translator.', '公认翻译最自然、最准确的 AI 翻译神器。', 'languages', 'International', NULL, 'small', 'https://www.deepl.com/translator', NULL),
('github-intl', 'GitHub', 'GitHub', 'Developer software platform.', '全球最大的开源代码托管平台。', 'github', 'International', NULL, 'small', 'https://github.com', NULL),
('stackoverflow-intl', 'Stack Overflow', 'Stack Overflow', 'Q&A for programmers.', '程序员必备的问答社区。', 'code', 'International', NULL, 'small', 'https://stackoverflow.com', NULL),

-- SmartUJS - Core
('ujs-jwxt', 'Education System', '教务系统', 'Grades and courses.', '选课、成绩查询、学籍信息管理的核心入口。', 'school', 'SmartUJS', '核心服务', 'medium', 'http://jwc.ujs.edu.cn/', NULL),
('ujs-portal', 'Unified Portal', '综合门户', 'Campus services hub.', '集成校内各项业务办理与服务的综合门户。', 'layout-grid', 'SmartUJS', '核心服务', 'medium', 'http://ehall.ujs.edu.cn/', NULL),
('ujs-webvpn', 'WebVPN', 'WebVPN 校外访问', 'Off-campus access.', '在校外访问校内网站和服务，使用一卡通号登录。', 'lock', 'SmartUJS', '核心服务', 'medium', 'https://webvpn.ujs.edu.cn/', NULL),
('ujs-repair', 'Maintenance', '网上报修', 'Dorm repair system.', '宿舍、教室等区域的设施损坏在线报修系统。', 'wrench', 'SmartUJS', '核心服务', 'small', 'http://hqbx.ujs.edu.cn/index.php', NULL),
('ujs-xl-1', 'Calendar', '校历', 'Academic schedule.', '查询学年校历与全校统一的作息时间表。', 'calendar', 'SmartUJS', '核心服务', 'small', 'https://jwc.ujs.edu.cn/index/xl_zuo_xi_shi_jian.htm', NULL),

-- SmartUJS - Study
('ujs-video', 'Course Playback', '课程回看', 'Lecture recordings.', '错过的课程？来这里回看，温故知新。', 'video', 'SmartUJS', '学习与工具', 'small', 'https://xsxx.ujs.edu.cn/', NULL),
('ujs-cx', 'ChaoXing', '超星学习通', 'Online learning.', '校内主要的在线教学平台，用于网课学习与作业。', 'book-open', 'SmartUJS', '学习与工具', 'small', 'http://fanya.chaoxing.com/', NULL),
('ujs-chsi', 'CHSI', '学信网', 'Degree verification.', '学历学位查询、在线验证报告、四六级成绩。', 'award', 'SmartUJS', '学习与工具', 'small', 'https://www.chsi.com.cn/', NULL),
('ujs-mooc', 'China MOOC', '中国大学MOOC', 'Top courses.', '汇集国内顶尖高校的在线开放课程资源。', 'graduation-cap', 'SmartUJS', '学习与工具', 'small', 'https://www.icourse163.org/', NULL),
('ujs-youth', 'Youth League', '智慧团建', 'League management.', '团组织关系转接、团费缴纳、青年大学习。', 'users', 'SmartUJS', '学习与工具', 'small', 'https://zhtj.youth.cn/zhtj/signin', NULL),
('ujs-file', 'File Helper', '微信文件助手', 'Cross-platform transfer.', '电脑与手机快速互传文件、文字和图片。', 'send', 'SmartUJS', '学习与工具', 'small', 'https://filehelper.weixin.qq.com/', NULL),
('ujs-fox', 'Fox Community', '狐友', 'Student social hub.', '江苏大学最懂学生的大型社区平台，大家都在用！', 'message-square', 'SmartUJS', '学习与工具', 'medium', 'https://sohu.cn/jZ2', NULL),
('ujs-lib', 'Library', '图书馆', 'Resources & Search.', '海量图书、期刊、数据库，学习科研的好帮手。', 'library', 'SmartUJS', '学习与工具', 'small', 'https://lib.ujs.edu.cn/', NULL),
('ujs-oj', 'CS Platform', '计算机专业课平台', 'Programming OJ.', '计算机类专业课程学习、实验、在线编程训练平台。', 'code', 'SmartUJS', '学习与工具', 'small', 'https://csoj.ujs.edu.cn/', NULL),

-- SmartUJS - Org
('ujs-xnxx', 'Internal Info', '校内信息网', 'Official notices.', '获取学校最新通知、公告及各类信息发布。', 'newspaper', 'SmartUJS', '官网与机构', 'small', 'https://xnxx.ujs.edu.cn/', NULL),
('ujs-jgsz', 'Institutions', '学校机构导航', 'Org navigation.', '快速查找学校各部门、学院的详细信息。', 'landmark', 'SmartUJS', '官网与机构', 'small', 'https://www.ujs.edu.cn/jgsz.htm', NULL),
('ujs-main', 'UJS Official', '江苏大学官网', 'Home page.', '学校官方主页，新闻、通知、校园概况。', 'home', 'SmartUJS', '官网与机构', 'small', 'http://www.ujs.edu.cn/', NULL),
('ujs-ehall-link', 'E-Hall', '一站式服务大厅', 'Integrated services.', '集成校内各项业务办理与服务的综合门户。', 'coffee', 'SmartUJS', '官网与机构', 'small', 'http://ehall.ujs.edu.cn/', NULL),
('ujs-xyh', 'Alumni', '校友会/基金会', 'Global alumni.', '联络全球校友，支持学校发展的官方平台。', 'users-2', 'SmartUJS', '官网与机构', 'small', 'https://xyh.ujs.edu.cn/', NULL),

-- SmartUJS - Admission
('ujs-zb', 'UG Admission', '本科生招生', 'UG enrollment.', '官方本科招生信息网，发布招生简章与政策。', 'user-plus', 'SmartUJS', '招生与就业', 'small', 'https://zb.ujs.edu.cn/', NULL),
('ujs-yz', 'PG Admission', '研究生招生', 'PG enrollment.', '研究生招生信息网，包含考研、推免等信息。', 'graduation-cap', 'SmartUJS', '招生与就业', 'small', 'https://yz.ujs.edu.cn/', NULL),
('ujs-oec-1', 'International', '留学生教育', 'OEC education.', '海外教育学院，面向国际学生的教学与服务。', 'globe', 'SmartUJS', '招生与就业', 'small', 'https://oec.ujs.edu.cn/', NULL),
('ujs-91job', '91Job', '就业信息网', 'Career guidance.', '发布招聘信息、校园招聘会、就业指导服务。', 'briefcase', 'SmartUJS', '招生与就业', 'small', 'https://ujs.91job.org.cn/sub-station/home/10299', NULL),

-- SmartUJS - Education
('ujs-jwc-5', 'UG Education', '本科生教育', 'Admin by JWC.', '由教务处负责，管理课程、成绩与教学安排。', 'school', 'SmartUJS', '教育与教学', 'small', 'https://jwc.ujs.edu.cn/', NULL),
('ujs-yjsy-5', 'PG Education', '研究生教育', 'Graduate school.', '研究生院官网，负责学科建设、培养与学位。', 'graduation-cap', 'SmartUJS', '教育与教学', 'small', 'http://yjsy.ujs.edu.cn/', NULL),
('ujs-oec-5', 'International', '留学生教育', 'OEC services.', '海外教育学院，面向国际学生的教学与服务。', 'globe', 'SmartUJS', '教育与教学', 'small', 'https://oec.ujs.edu.cn/', NULL),
('ujs-de-5', 'Continuing', '继续教育', 'Adult training.', '提供各类成人高等教育与非学历培训项目。', 'book-marked', 'SmartUJS', '教育与教学', 'small', 'http://www.ujsde.com/', NULL),
('ujs-rsc-5', 'Faculty', '师资队伍', 'Human resources.', '人事处官网，展示学校师资力量与相关政策。', 'users', 'SmartUJS', '教育与教学', 'small', 'http://rsc.ujs.edu.cn/', NULL),
('ujs-rczp-5', 'Recruitment', '人才招聘', 'Official portal.', '面向海内外高层次人才的官方招聘门户。', 'user-plus', 'SmartUJS', '教育与教学', 'small', 'https://www.ujs.edu.cn/jyjx/rczp.htm', NULL),

-- SmartUJS - Research
('ujs-kjc', 'Science Dynamics', '科技动态', 'Sci-Tech admin.', '科技处官网，发布自然科学类科研项目与成果。', 'microscope', 'SmartUJS', '科研与学术', 'small', 'http://kjc.ujs.edu.cn/', NULL),
('ujs-skc', 'Social Science', '社科动态', 'Social sci admin.', '社会科学处官网，负责人文社科类科研管理。', 'book-open', 'SmartUJS', '科研与学术', 'small', 'http://skc.ujs.edu.cn/', NULL),
('ujs-cxy', 'Cooperation', '产学研合作', 'Industry link.', '发布行业、企业与学校的合作项目与动态。', 'award', 'SmartUJS', '科研与学术', 'small', 'https://cxyc.ujs.edu.cn/', NULL),
('ujs-zscq', 'IP Center', '知识产权', 'Patents.', '知识产权学院，处理专利、商标等相关事务。', 'shield-check', 'SmartUJS', '科研与学术', 'small', 'http://zscq.ujs.edu.cn/', NULL),
('ujs-zzs', 'Journals', '学术期刊', 'UJS Press.', '江苏大学杂志社，主办各类高水平学术期刊。', 'archive', 'SmartUJS', '科研与学术', 'small', 'http://zzs.ujs.edu.cn/', NULL),

-- SmartUJS - Campus
('ujs-dag', 'Archives', '档案馆', 'History records.', '负责学校档案的收集、整理、保管与利用。', 'archive', 'SmartUJS', '校园服务与文化', 'small', 'http://dag.ujs.edu.cn/', NULL),
('ujs-xsg', 'History Museum', '校史馆', 'Uni history.', '展示学校百年历史、文化传承与辉煌成就。', 'landmark', 'SmartUJS', '校园服务与文化', 'small', 'http://xsg.ujs.edu.cn/', NULL),
('ujs-njwh', 'Tractor Museum', '农机文化展示馆', 'Agri tech culture.', '展现中国农业机械发展的历史与文化。', 'tractor', 'SmartUJS', '校园服务与文化', 'small', 'http://zgnjwhzsg.ujs.edu.cn/', NULL),
('ujs-cgb', 'Bidding', '采购与招标', 'Purchasing ads.', '发布学校各类物资、服务采购与招标公告。', 'gavel', 'SmartUJS', '校园服务与文化', 'small', 'https://cgb.ujs.edu.cn/', NULL),
('ujs-xl-7', 'Calendar', '校历', 'Schedules.', '查询学年校历与全校统一的作息时间表。', 'calendar', 'SmartUJS', '校园服务与文化', 'small', 'https://jwc.ujs.edu.cn/index/xl_zuo_xi_shi_jian.htm', NULL),
('ujs-warm', 'Warm Station', '暖心驿站', 'Supervision service.', '校纪委（监察专员办）设立的师生服务平台。', 'heart', 'SmartUJS', '校园服务与文化', 'small', 'https://jw.ujs.edu.cn/', NULL),

-- Tool & Others
('latex-converter', 'LaTeX Tool', 'LaTeX多功能转化', 'Convert LaTeX to Word/PDF.', '采用全新风格页面设计，将LaTeX轻松转化为Word、PDF等文件格式。', 'file-code', 'Tool', NULL, 'small', 'https://www.cmiteam.top/index/latex%20test.html', NULL),
('mindmap-gen', 'Mindmap Gen', '思维导图MD生成器', 'AI mindmaps to Markdown.', '思维导图MD生成器，方便导入到WPS,XMIND等主流软件。', 'brain', 'Tool', NULL, 'small', NULL, 'mindmap-gen'),
('study-progress', 'Study Plan', '学习进度计划', 'Local data plan tracker.', '计划制定页面，本地保存数据，安全可靠，助您高效学习。', 'clipboard-list', 'Tool', NULL, 'small', 'https://www.cmiteam.top/study-progress/', NULL),
('runoob', 'Runoob', '菜鸟语言教程', 'Programming languages.', '包含C语言、Python等多种编程语言的基础教程。', 'book-open', 'External', NULL, 'small', 'https://www.runoob.com/', NULL),
('tts-tool', 'Text to Speech', '文字转语音', 'Convert text to audio.', '将文本在线转换为语音输出。', 'volume-2', 'Tool', NULL, 'small', 'https://www.cmiteam.top/read/', NULL),
('mix-info', 'Mix Info', '综合信息平台', 'News and benchmarks.', '提供国内外新闻，CPU,GPU跑分数据，实时天气服务。', 'layout-grid', 'Info', NULL, 'medium', 'https://www.cmiteam.top/index/mixinfo.html', NULL),
('url-extractor', 'URL Extractor', '网站查询', 'Extract URLs and ICP.', '提供网页URL批量提取和ICP查询服务。', 'link', 'Tool', NULL, 'small', 'https://www.cmiteam.top/index/url_extractor.html', NULL),
('download-station', 'Download Station', '下载站', 'CMI Team resources.', 'CMI团队资源下载中心。', 'download', 'External', NULL, 'small', 'https://www.cmiteam.top/simple-download/', NULL),
('sub-station', 'Sub-station', '分站导航', 'Digital link nodes.', '汇聚智慧，洞察先机，构建合作化的数字脉络。', 'share-2', 'Info', NULL, 'small', 'https://www.cmiteam.cn/', NULL),
('feedback', 'Feedback', '网页反馈', 'Give us suggestions.', '网页功能和意见反馈，欢迎反映！无需登录即可填写。', 'help-circle', 'External', NULL, 'small', 'https://f.wps.cn/ksform/h/write/Bq1bjGb1/', NULL),
('wechat-qr', 'WeChat', '关注微信公众号', 'Official WeChat news.', '扫码获取CMI团队最新资讯、技术分享与独家福利！', 'qr-code', 'External', NULL, 'small', 'https://www.cmiteam.top/index/wechat-qr.html', NULL),
('bilibili-chn', 'Bilibili', '关注哔哩哔哩', 'Creative video content.', '观看CMI团队精彩视频内容，获取技术教程与项目分享。', 'tv', 'External', NULL, 'small', 'https://www.cmiteam.top/index/tran-bilibili.html', NULL),

('json-formatter', 'JSON Formatter', 'JSON 格式化', 'Prettify or minify JSON.', '一键美化或压缩 JSON 代码，支持语法错误检测。', 'file-code', 'Tool', NULL, 'small', NULL, 'json-formatter'),
('image-base64', 'Image Lab', '图片转 Base64', 'Convert images to code.', '将图片转换为 Base64 编码，或从编码还原预览图片。', 'edit-3', 'Tool', NULL, 'small', NULL, 'image-base64'),
('ascii-query', 'ASCII Table', 'ASCii 快速查询', 'Check ASCII codes.', '快速查询 ASCII 码表，便捷的字符编码工具。', 'binary', 'Tool', NULL, 'small', NULL, 'ascii-query'),
('digital-clock', 'Digital Clock', '量子时钟', 'Precise full-screen clock.', '精确到秒的数字时钟，支持多种视觉样式。', 'clock', 'Tool', NULL, 'small', NULL, 'digital-clock'),
('stopwatch-built', 'Stopwatch', '精准秒表', 'Timing features.', '功能丰富的秒表，支持计次与分段。', 'timer', 'Tool', NULL, 'small', NULL, 'stopwatch-built'),
('random-gen-built', 'Randomizer', '随机数生成器', 'Generate numbers.', '生成指定范围内的随机数，辅助决策。', 'dice-5', 'Tool', NULL, 'small', NULL, 'random-gen-built'),
('pomodoro-built', 'Pomodoro', '专注番茄', 'Productivity timer.', '高效工作的番茄钟，自定义工作与休息时长。', 'timer', 'Tool', NULL, 'small', NULL, 'pomodoro-built'),

('password-gen', 'Password Gen', '安全密码生成', 'Secure random passwords.', '生成高强度的随机密码，保护您的账户安全。', 'shield-check', 'Tool', NULL, 'small', NULL, 'password-gen'),
('weather-tool', 'Atmosphere Lab', '气象实验室', 'Real-time weather data.', '获取全球城市的实时天气、风力与湿度信息。', 'cloud', 'Tool', NULL, 'medium', NULL, 'weather-tool'),
('text-util', 'Text Suite', '文本全能处理', 'Transform text easily.', '提供大小写转换、驼峰转换、字数统计等文本工具。', 'file-text', 'Tool', NULL, 'small', NULL, 'text-util'),
('epic-games-tool', 'Epic Freebies', 'Epic 喜加一', 'Track free games.', '追踪 Epic 商城的每周限免游戏动态。', 'gamepad-2', 'Info', NULL, 'small', NULL, 'epic-games-tool'),

('message-board', 'Message Board', '智链留言板', 'Share your thoughts.', '留下您的宝贵建议或反馈，与团队直接面对面。', 'message-square', 'Info', NULL, 'small', NULL, 'message-board'),
('update-log', 'Update Log', '网站更新日志', 'Track our growth.', '追踪细米兰阁及 CMI 团队项目的最新更新历程。', 'history', 'Info', NULL, 'small', NULL, 'update-log'),

('privacy-policy', 'Privacy', '隐私政策', 'Security first.', '详细了解我们如何保护您的数字隐私。', 'shield-check', 'Info', NULL, 'small', NULL, 'privacy-policy'),
('team-introduction', 'Team Intro', '团队介绍', 'About us.', 'CMI 团队的成立背景与指导单位。', 'users', 'Info', NULL, 'small', NULL, 'team-introduction');

-- Seed Changelogs
INSERT INTO `changelogs` (`version`, `date`, `title_en`, `title_zh`, `type`, `items_json`) VALUES
('6.0', '2026-01-05', 'Next-Gen Hub: CMItool 2.0', '次世代中枢：智链细米 2.0', 'Major', '["全新视觉语言：采用 Glassmorphism 2.0 磨砂玻璃设计", "手机端深度优化：自动切换底部导航栏", "智能气象实验室：新增城市感知功能", "隐私至上架构：全站纯前端驱动"]'),
('5.0', '2025-08-22', 'Major Refresh', '主页导航焕新', 'Major', '["修复了新主页导航的动画设计逻辑", "智链细米拓展应用中心上线", "新增新闻中心版块"]');
