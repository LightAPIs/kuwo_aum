<?php
require('debug.php');
require('src/kuwoSource.php');

$downloader = (new ReflectionClass('AumKuwoSource'))->newInstance();
$testArray = array(
    array('title' => 'Hello', 'artist' => 'SHINee')
);

foreach ($testArray as $key => $item) {
    echo "\n++++++++++++++++++++++++++++++\n";
    echo "测试 $key 开始...\n";
    if ($key > 0) {
        echo "等待 5 秒...\n";
        sleep(5);
    }
    echo "{title = " . $item['title'] . "; artist = " . $item['artist'] . " }.\n";
    $testObj = new AudioStationResult();
    $count = $downloader->getLyricsList($item['artist'], $item['title'], $testObj);
    if ($count > 0) {
        $item = $testObj->getFirstItem();
        $downloader->getLyrics($item['id'], $testObj);
    } else {
        echo "没有查找到任何歌词！\n";
    }
    echo "测试 $key 结束。\n";
}
