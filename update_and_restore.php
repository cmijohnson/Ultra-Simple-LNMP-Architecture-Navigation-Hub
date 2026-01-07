<?php
require_once 'includes/db.php';
$pdo = getDB();

$tools = [
     ['id' => 'ujs-video', 'name_zh' => '课程回看', 'name_en' => 'Course Playback', 'desc_zh' => '错过的课程？来这里回看，温故知新。', 'desc_en' => 'Lecture recordings.', 'icon' => 'video', 'category' => 'SmartUJS', 'url' => 'https://xsxx.ujs.edu.cn/', 'subCategory' => '学习与工具'],
     ['id' => 'ujs-cx', 'name_zh' => '超星学习通', 'name_en' => 'ChaoXing', 'desc_zh' => '校内主要的在线教学平台，用于网课学习与作业。', 'desc_en' => 'Online learning.', 'icon' => 'book-open', 'category' => 'SmartUJS', 'url' => 'http://fanya.chaoxing.com/', 'subCategory' => '学习与工具'],
     ['id' => 'ujs-chsi', 'name_zh' => '学信网', 'name_en' => 'CHSI', 'desc_zh' => '学历学位查询、在线验证报告、四六级成绩。', 'desc_en' => 'Degree verification.', 'icon' => 'award', 'category' => 'SmartUJS', 'url' => 'https://www.chsi.com.cn/', 'subCategory' => '学习与工具'],
     ['id' => 'ujs-mooc', 'name_zh' => '中国大学MOOC', 'name_en' => 'China MOOC', 'desc_zh' => '汇集国内顶尖高校的在线开放课程资源。', 'desc_en' => 'Top courses.', 'icon' => 'graduation-cap', 'category' => 'SmartUJS', 'url' => 'https://www.icourse163.org/', 'subCategory' => '学习与工具'],
     ['id' => 'ujs-youth', 'name_zh' => '智慧团建', 'name_en' => 'Youth League', 'desc_zh' => '团组织关系转接、团费缴纳、青年大学习。', 'desc_en' => 'League management.', 'icon' => 'users', 'category' => 'SmartUJS', 'url' => 'https://zhtj.youth.cn/zhtj/signin', 'subCategory' => '学习与工具'],
     ['id' => 'ujs-file', 'name_zh' => '微信文件助手', 'name_en' => 'File Helper', 'desc_zh' => '电脑与手机快速互传文件、文字和图片。', 'desc_en' => 'Cross-platform transfer.', 'icon' => 'send', 'category' => 'SmartUJS', 'url' => 'https://filehelper.weixin.qq.com/', 'subCategory' => '学习与工具'],
     ['id' => 'ujs-fox', 'name_zh' => '狐友', 'name_en' => 'Fox Community', 'desc_zh' => '江苏大学最懂学生的大型社区平台，大家都在用！', 'desc_en' => 'Student social hub.', 'icon' => 'message-square', 'category' => 'SmartUJS', 'url' => 'https://sohu.cn/jZ2', 'subCategory' => '学习与工具'],
     ['id' => 'ujs-lib', 'name_zh' => '图书馆', 'name_en' => 'Library', 'desc_zh' => '海量图书、期刊、数据库，学习科研的好帮手。', 'desc_en' => 'Resources & Search.', 'icon' => 'library', 'category' => 'SmartUJS', 'url' => 'https://lib.ujs.edu.cn/', 'subCategory' => '学习与工具'],
     ['id' => 'ujs-oj', 'name_zh' => '计算机专业课平台', 'name_en' => 'CS Platform', 'desc_zh' => '计算机类专业课程学习、实验、在线编程训练平台。', 'desc_en' => 'Programming OJ.', 'icon' => 'code', 'category' => 'SmartUJS', 'url' => 'https://csoj.ujs.edu.cn/', 'subCategory' => '学习与工具'],
     ['id' => 'ujs-xnxx', 'name_zh' => '校内信息网', 'name_en' => 'Internal Info', 'desc_zh' => '获取学校最新通知、公告及各类信息发布。', 'desc_en' => 'Official notices.', 'icon' => 'newspaper', 'category' => 'SmartUJS', 'url' => 'https://xnxx.ujs.edu.cn/', 'subCategory' => '官网与机构'],
     ['id' => 'ujs-jgsz', 'name_zh' => '学校机构导航', 'name_en' => 'Institutions', 'desc_zh' => '快速查找学校各部门、学院的详细信息。', 'desc_en' => 'Org navigation.', 'icon' => 'landmark', 'category' => 'SmartUJS', 'url' => 'https://www.ujs.edu.cn/jgsz.htm', 'subCategory' => '官网与机构'],
     ['id' => 'ujs-main', 'name_zh' => '江苏大学官网', 'name_en' => 'UJS Official', 'desc_zh' => '学校官方主页，新闻、通知、校园概况。', 'desc_en' => 'Home page.', 'icon' => 'home', 'category' => 'SmartUJS', 'url' => 'http://www.ujs.edu.cn/', 'subCategory' => '官网与机构'],
     ['id' => 'ujs-ehall-link', 'name_zh' => '一站式服务大厅', 'name_en' => 'E-Hall', 'desc_zh' => '集成校内各项业务办理与服务的综合门户。', 'desc_en' => 'Integrated services.', 'icon' => 'coffee', 'category' => 'SmartUJS', 'url' => 'http://ehall.ujs.edu.cn/', 'subCategory' => '官网与机构'],
     ['id' => 'ujs-xyh', 'name_zh' => '校友会/基金会', 'name_en' => 'Alumni', 'desc_zh' => '联络全球校友，支持学校发展的官方平台。', 'desc_en' => 'Global alumni.', 'icon' => 'users-2', 'category' => 'SmartUJS', 'url' => 'https://xyh.ujs.edu.cn/', 'subCategory' => '官网与机构'],
     ['id' => 'ujs-zb', 'name_zh' => '本科生招生', 'name_en' => 'UG Admission', 'desc_zh' => '官方本科招生信息网，发布招生简章与政策。', 'desc_en' => 'UG enrollment.', 'icon' => 'user-plus', 'category' => 'SmartUJS', 'url' => 'https://zb.ujs.edu.cn/', 'subCategory' => '招生与就业'],
     ['id' => 'ujs-yz', 'name_zh' => '研究生招生', 'name_en' => 'PG Admission', 'desc_zh' => '研究生招生信息网，包含考研、推免等信息。', 'desc_en' => 'PG enrollment.', 'icon' => 'graduation-cap', 'category' => 'SmartUJS', 'url' => 'https://yz.ujs.edu.cn/', 'subCategory' => '招生与就业'],
     ['id' => 'ujs-oec-1', 'name_zh' => '留学生教育', 'name_en' => 'International', 'desc_zh' => '海外教育学院，面向国际学生的教学与服务。', 'desc_en' => 'OEC education.', 'icon' => 'globe', 'category' => 'SmartUJS', 'url' => 'https://oec.ujs.edu.cn/', 'subCategory' => '招生与就业'],
     ['id' => 'ujs-91job', 'name_zh' => '就业信息网', 'name_en' => '91Job', 'desc_zh' => '发布招聘信息、校园招聘会、就业指导服务。', 'desc_en' => 'Career guidance.', 'icon' => 'briefcase', 'category' => 'SmartUJS', 'url' => 'https://ujs.91job.org.cn/sub-station/home/10299', 'subCategory' => '招生与就业'],
     ['id' => 'ujs-jwc-5', 'name_zh' => '本科生教育', 'name_en' => 'UG Education', 'desc_zh' => '由教务处负责，管理课程、成绩与教学安排。', 'desc_en' => 'Admin by JWC.', 'icon' => 'school', 'category' => 'SmartUJS', 'url' => 'https://jwc.ujs.edu.cn/', 'subCategory' => '教育与教学'],
     ['id' => 'ujs-yjsy-5', 'name_zh' => '研究生教育', 'name_en' => 'PG Education', 'desc_zh' => '研究生院官网，负责学科建设、培养与学位。', 'desc_en' => 'Graduate school.', 'icon' => 'graduation-cap', 'category' => 'SmartUJS', 'url' => 'http://yjsy.ujs.edu.cn/', 'subCategory' => '教育与教学'],
     ['id' => 'ujs-oec-5', 'name_zh' => '留学生教育', 'name_en' => 'International', 'desc_zh' => '海外教育学院，面向国际学生的教学与服务。', 'desc_en' => 'OEC services.', 'icon' => 'globe', 'category' => 'SmartUJS', 'url' => 'https://oec.ujs.edu.cn/', 'subCategory' => '教育与教学'],
     ['id' => 'ujs-de-5', 'name_zh' => '继续教育', 'name_en' => 'Continuing', 'desc_zh' => '提供各类成人高等教育与非学历培训项目。', 'desc_en' => 'Adult training.', 'icon' => 'book-marked', 'category' => 'SmartUJS', 'url' => 'http://www.ujsde.com/', 'subCategory' => '教育与教学'],
     ['id' => 'ujs-rsc-5', 'name_zh' => '师资队伍', 'name_en' => 'Faculty', 'desc_zh' => '人事处官网，展示学校师资力量与相关政策。', 'desc_en' => 'Human resources.', 'icon' => 'users', 'category' => 'SmartUJS', 'url' => 'http://rsc.ujs.edu.cn/', 'subCategory' => '教育与教学'],
     ['id' => 'ujs-rczp-5', 'name_zh' => '人才招聘', 'name_en' => 'Recruitment', 'desc_zh' => '面向海内外高层次人才的官方招聘门户。', 'desc_en' => 'Official portal.', 'icon' => 'user-plus', 'category' => 'SmartUJS', 'url' => 'https://www.ujs.edu.cn/jyjx/rczp.htm', 'subCategory' => '教育与教学'],
     ['id' => 'ujs-kjc', 'name_zh' => '科技动态', 'name_en' => 'Science Dynamics', 'desc_zh' => '科技处官网，发布自然科学类科研项目与成果。', 'desc_en' => 'Sci-Tech admin.', 'icon' => 'microscope', 'category' => 'SmartUJS', 'url' => 'http://kjc.ujs.edu.cn/', 'subCategory' => '科研与学术'],
     ['id' => 'ujs-skc', 'name_zh' => '社科动态', 'name_en' => 'Social Science', 'desc_zh' => '社会科学处官网，负责人文社科类科研管理。', 'desc_en' => 'Social sci admin.', 'icon' => 'book-open', 'category' => 'SmartUJS', 'url' => 'http://skc.ujs.edu.cn/', 'subCategory' => '科研与学术'],
     ['id' => 'ujs-cxy', 'name_zh' => '产学研合作', 'name_en' => 'Cooperation', 'desc_zh' => '发布行业、企业与学校的合作项目与动态。', 'desc_en' => 'Industry link.', 'icon' => 'award', 'category' => 'SmartUJS', 'url' => 'https://cxyc.ujs.edu.cn/', 'subCategory' => '科研与学术'],
     ['id' => 'ujs-zscq', 'name_zh' => '知识产权', 'name_en' => 'IP Center', 'desc_zh' => '知识产权学院，处理专利、商标等相关事务。', 'desc_en' => 'Patents.', 'icon' => 'shield-check', 'category' => 'SmartUJS', 'url' => 'http://zscq.ujs.edu.cn/', 'subCategory' => '科研与学术'],
     ['id' => 'ujs-zzs', 'name_zh' => '学术期刊', 'name_en' => 'Journals', 'desc_zh' => '江苏大学杂志社，主办各类高水平学术期刊。', 'desc_en' => 'UJS Press.', 'icon' => 'archive', 'category' => 'SmartUJS', 'url' => 'http://zzs.ujs.edu.cn/', 'subCategory' => '科研与学术'],
     ['id' => 'ujs-dag', 'name_zh' => '档案馆', 'name_en' => 'Archives', 'desc_zh' => '负责学校档案的收集、整理、保管与利用。', 'desc_en' => 'History records.', 'icon' => 'archive', 'category' => 'SmartUJS', 'url' => 'http://dag.ujs.edu.cn/', 'subCategory' => '校园服务与文化'],
     ['id' => 'ujs-xsg', 'name_zh' => '校史馆', 'name_en' => 'History Museum', 'desc_zh' => '展示学校百年历史、文化传承与辉煌成就。', 'desc_en' => 'Uni history.', 'icon' => 'landmark', 'category' => 'SmartUJS', 'url' => 'http://xsg.ujs.edu.cn/', 'subCategory' => '校园服务与文化'],
     ['id' => 'ujs-njwh', 'name_zh' => '农机文化展示馆', 'name_en' => 'Tractor Museum', 'desc_zh' => '展现中国农业机械发展的历史与文化。', 'desc_en' => 'Agri tech culture.', 'icon' => 'tractor', 'category' => 'SmartUJS', 'url' => 'http://zgnjwhzsg.ujs.edu.cn/', 'subCategory' => '校园服务与文化'],
     ['id' => 'ujs-cgb', 'name_zh' => '采购与招标', 'name_en' => 'Bidding', 'desc_zh' => '发布学校各类物资、服务采购与招标公告。', 'desc_en' => 'Purchasing ads.', 'icon' => 'gavel', 'category' => 'SmartUJS', 'url' => 'https://cgb.ujs.edu.cn/', 'subCategory' => '校园服务与文化'],
     ['id' => 'ujs-xl-7', 'name_zh' => '校历', 'name_en' => 'Calendar', 'desc_zh' => '查询学年校历与全校统一的作息时间表。', 'desc_en' => 'Schedules.', 'icon' => 'calendar', 'category' => 'SmartUJS', 'url' => 'https://jwc.ujs.edu.cn/index/xl_zuo_xi_shi_jian.htm', 'subCategory' => '校园服务与文化'],
     ['id' => 'ujs-warm', 'name_zh' => '暖心驿站', 'name_en' => 'Warm Station', 'desc_zh' => '校纪委（监察专员办）设立的师生服务平台。', 'desc_en' => 'Supervision service.', 'icon' => 'heart', 'category' => 'SmartUJS', 'url' => 'https://jw.ujs.edu.cn/', 'subCategory' => '校园服务与文化']
];

$stmt = $pdo->prepare("INSERT INTO tools (slug, name_zh, name_en, desc_zh, desc_en, url, icon, category, sub_category, size) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'small')");

$count = 0;
foreach ($tools as $t) {
    // Check if exists
    $check = $pdo->prepare("SELECT COUNT(*) FROM tools WHERE slug = ?");
    $check->execute([$t['id']]);
    if ($check->fetchColumn() == 0) {
        $stmt->execute([
            $t['id'],
            $t['name_zh'],
            $t['name_en'],
            $t['desc_zh'],
            $t['desc_en'],
            $t['url'],
            $t['icon'],
            $t['category'],
            $t['subCategory']
        ]);
        $count++;
    }
}

echo "Restored $count tools to the database.";
?>
