<?php

global $dir;
$generator = new \core\Database\Generator();

$data = [
    'Rayuan Pengesahan Markah' => 'Kepada semua guru penasihat / guru pengiring / pelajar.

 Kutipan terakhir Borang LR untuk menuntut markah PAJSK 2023 adalah 5/1/2024 .

Pelajar-pelajar juga diingatkan untuk periksa markah PAJSK dalam SMS . Sila maklum Unit Gerko jika ada sebarang masalah / kesilapan. 

Tarikh akhir untuk RAYUAN: 9/1/2024 .

Rayuan selepas 9/1/2024 tidak akan dilayan. 

Harap maklum. 
Terima kasih.',
    'Gotong Royong Anjuran PIBG' => 'Tarikh: 8/3/2024(Jumaat)
Masa: 8.00 - 10.00am
Pakaian：T-Shirt Sekolah dan kasut sekolah/ kasut sukan
Kehadiran: murid T2, T3, T4, T5, T6
*Ketua Tingkatan Kelas perlu ambil kedatangan
*Badan yang terlibat di luar bilik darjah perlu ambil kedatangan kemudian serahkan kepada PKHEM.
Kehadiran perlu tulis: Nama badan, Nama murid , Kelas',
    '《航空科学大观》—创造飞行 分享会' => '同学们，

对航空领域有兴趣的学生们请注意，好消息来了‼️

我们很荣幸地邀请到了黎宏隽学长来为我们分享及讲解航空科学，带领我们一同探索航空科学的奥妙及无限可能！

📅【日期】：2024年2月2日 (星期五)
💡【时间】：下午2时正 至 4时正
📍【地点】：锺中图书馆
👨‍💼【讲者】：黎宏隽学长
🚀【主题】：《航空科学大观》—创造飞行

报名表格链接👇
https://forms.gle/6kqNh1JJaRzoxZU87

🔥名额有限，报名从速🔥
报名截止日期为2024年1月24日 (晚上11时59分)。

谢谢。',
    '以书会友2.0' => '师生们，

大家期待已久的以书会友活动重启啦🥳！此活动旨在为本馆的旧书找到新的归属，提供学生们以低价购入心仪之作的难得机会，甚至以一块钱就能拥有一本喜爱的书籍！

活动亮点：

1️⃣低价放送：以至少半价的价格购买心仪的书籍，别错过这个千载难逢的机会！

2️⃣赠品相伴：凡是购买任何一本书籍都将获得一本赠书。不仅如此，我们也提供买5送1的福利。

3️⃣多彩书籍种类：无论你是文学爱好者，小说迷，又或者抱着寻找参考书的目的前来此活动，我们都有琳琅满目的书供你选择。

活动详情👇
地点：锺中图书馆
日期：1月22日 至 2月7日
时间：	
7.00am-7.30am (上课前)
9.30am-10.00am (中一，二，四休息时段)
10.30am-11.00am (中三，五休息时段)

‼️📌本馆也欢迎学生们将已用不上的参考书捐赠至本馆，以兑换相应的购书券。✌️

任何疑问，敬请咨询
筹委主席 阮峰鋆 (018-380 0398)
筹委秘书 林敬桐 (011-1018 6168)',
];

$generator->generateCSVFile($data, $dir . '/resources/data/announcements.csv');