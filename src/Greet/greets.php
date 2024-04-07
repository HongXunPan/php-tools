<?php

date_default_timezone_set('Asia/Shanghai');
$hour=date("H");
if($hour>0 && $hour<6) echo "天还没亮，夜猫子，要注意身体哦！";
else if($hour>6 && $hour<12) echo "上午好！";
else if($hour>12 && $hour<14) echo "中午好！午休时间哦，朋友一定是不习惯午睡的吧？！";
else if($hour>14 && $hour<16) echo "下午茶的时间到了，休息一下吧！";
else if($hour>16 && $hour<18) echo "经常对着电脑不好，快去锻炼吧！";
else if($hour>18 && $hour<23) echo "吃完饭多陪陪家人吧！";
else echo "很晚了哦，注意休息呀！";

$strMidNightArr = '```EOT
1、夜深了，睡觉吧，送你几声呼噜吧：一声好运气，二声有福气，三声喜气罩，四声运气到。收到祝福开始呼噜吧，哈哈，晚安！
　　2、晚风轻轻拂面，星星深情眨眼，我在佛前虔诚许愿，愿你醒时就笑，入梦就甜，事事如我心愿，我的心愿就是你早些入梦，早展笑颜，道一声：晚安！
　　3、晚上是烦恼的结束，早晨是快乐的开始，晴天照出你灿烂的心情，雨天冲去你所有的忧愁，无论是早晚，不管是晴雨，我都愿陪在你身边，愿你快乐每一天！
　　4、这样的深夜，你在做什么。这样的深夜，我在思念你。你看那天上的星星，颗颗代表我的祝福。睡吧，睡吧，明天再告诉我你到底收到了多少祝福。晚安。
　　5、放松是铺垫，困倦是经验，微笑是情节，美丽是了解，黑夜是一页，最华丽的诗页，读出你的生活朝气蓬勃，读出你的健康活力无限，祝朋友每天快乐。晚安！
　　6、今晚眨一下眼，代表我在惦记你；眨两下眼，代表我在想念你；眨三下眼，代表我十分想念你；不停眨眼，嘿，代表你用眼过度，早点休息吧！晚安！
　　7、今天下雨，就像降落来临，问候不只是在在夜里，祝福永恒藏在在心底，雨停而不止，夜深却牵挂依然，晚安！
　　8、窗外知了声声阵阵；想起了你情情真真；风风雨雨都归凡尘；生生世世惜你缘分；枕边发出心中短信；祝福虽短情意很深；朋友千万要记在心；道声晚安送去安稳！晚安！
　　9、该做的事做了，该说话的话说了，该看的风景看了，该玩的游戏玩了，该写的总结写了，该做的梦也该做了吧？早点睡吧，晚安！
　　10、窗外月亮挂起；想起了你情情真真；风风雨雨都归凡尘；生生世世惜你缘分；枕边发出心中短信；祝福虽短情意很深；朋友千万要记在心；道声晚安送去安稳！`';
