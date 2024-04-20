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

const greetings = '[{
    "greeting": "晚安😴",
        "start_time": 0,
        "end_time": 5
      },
      {
          "greeting": "早上好鸭👋, 祝你一天好心情！",
        "start_time": 6,
        "end_time": 9
      },
      {
          "greeting": "上午好👋, 状态很好，鼓励一下～",
        "start_time": 10,
        "end_time": 10
      },
      {
          "greeting": "11点多啦, 在坚持一下就吃饭啦～",
        "start_time": 11,
        "end_time": 11
      },
      {
          "greeting": "午安👋, 宝贝",
        "start_time": 12,
        "end_time": 14
      },
      {
          "greeting": "🌈充实的一天辛苦啦！",
        "start_time": 14,
        "end_time": 18
      },
      {
          "greeting": "19点喽, 奖励一顿丰盛的大餐吧🍔。",
        "start_time": 19,
        "end_time": 19
      },
      {
          "greeting": "晚上好👋, 在属于自己的时间好好放松😌~",
        "start_time": 20,
        "end_time": 24
      }
    ]';

if ($nge_Hour == 0)
$nge_warmprompt = "现在已经过凌晨了，身体是无价的资本喔，小伙伴早点休息吧！      --编程笔记";
if ($nge_Hour == 1)
    $nge_warmprompt = "凌晨1点多了，工作是永远都做不完的，小伙伴别熬坏身子！     --编程笔记";
if ($nge_Hour == 2)
    $nge_warmprompt = "亲爱的小伙伴该休息了，身体可是革命的本钱啊！      --编程笔记";
if ($nge_Hour == 3)
    $nge_warmprompt = "夜深了，熬夜很容易导致身体内分泌失调，长痘痘的！      --编程笔记";
if ($nge_Hour == 4)
    $nge_warmprompt = "四点过了额(⊙o⊙)…，你明天不学习工作吗？？？      --编程笔记";
if ($nge_Hour == 5)
    $nge_warmprompt = "你知道吗，此时是国内网络速度最快的时候！      --编程笔记";
if ($nge_Hour == 6)
    $nge_warmprompt = "清晨好，这么早就来网站啦，谢谢小伙伴的关注哦，昨晚做的梦好吗？      --编程笔记 ";
if ($nge_Hour == 7)
    $nge_warmprompt = "新的一天又开始了，祝你过得快乐!      --编程笔记";
if ($nge_Hour == 8)
    $nge_warmprompt = "小伙伴早上好哦，一天之际在于晨，又是美好的一天！      --编程笔记";
if (($nge_Hour == 9) || ($nge_Hour ==10))
    $nge_warmprompt = "上午好！今天你看上去好精神哦！      --编程笔记";
if (( $nge_Hour == 11) || ($nge_Hour == 12))
    $nge_warmprompt = "小伙伴啊！该吃午饭啦！有什么好吃的？您有中午休息的好习惯吗？      --编程笔记";
if (( $nge_Hour >= 13) && ($nge_Hour <= 17))
    $nge_warmprompt = "下午好！外面的天气好吗？记得朵朵白云曾捎来朋友殷殷的祝福。      --编程笔记";
if (( $nge_Hour >= 17) && ($nge_Hour <= 18))
    $nge_warmprompt = "太阳落山了！快看看夕阳吧！如果外面下雨，就不必了 ^_^      --编程笔记";
if (( $nge_Hour >= 18) && ($nge_Hour <= 19))
    $nge_warmprompt = "晚上好，小伙伴今天的心情怎么样？去留言板诉说一下吧！      --编程笔记";
if (( $nge_Hour >= 19) && ($nge_Hour <= 21))
    $nge_warmprompt = "忙碌了一天，累了吧？去看看最新的新闻资讯醒醒脑吧！      --编程笔记";
if (( $nge_Hour >= 22) && ($nge_Hour <= 23))
    $nge_warmprompt = "这么晚了，小伙伴还在上网？早点洗洗睡吧，睡前记得洗洗脸喔！明天一天都会萌萌哒！      --编程笔记";


switch ($hour) {
    case $hour < 6:
        $text="又是一个不眠夜!";
        break;
    case $hour < 9:
        $text="新的一天开始了!";
        break;
    case $hour < 12:
        $text="上午工作顺利吗？";
        break;
    case $hour < 14:
        $text="中午好！吃饭了吗？";
        break;
    case $hour < 17:
        $text="下午好！别打盹呼哦！";
        break;
    case $hour < 19:
        $text="傍晚好！还在加班吗？";
        break;
    case $hour < 22:
        $text="晚上好！夜色好美啊！";
        break;
    default:
        $text="我欲修仙,法力无边。";
        break;
}