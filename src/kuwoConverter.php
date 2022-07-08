<?php

class AumKuwoConverter {
    private $lrcList;
    public function __construct($lrcList) {
        $this->lrcList = $lrcList;
    }

    private function getValidLrcTag($time) {
        $time = (float)$time;
        $min = floor($time / 60);
        $sec = floor($time - $min * 60);
        $mil = round($time - floor($time), 3) * 1000;
        return "[". str_pad($min, 2, "0", STR_PAD_LEFT) . ":" . str_pad($sec, 2, "0", STR_PAD_LEFT) . "." . str_pad($mil, 3, "0", STR_PAD_LEFT) . "]";
    }

    private function getValidLrcText($str) {
        $str = trim($str);
        if ($str === '//') {
            return '';
        }
        return $str;
    }

    public function getLrc() {
        if (empty($this->lrcList)) {
            return "";
        }
        $lrc = "";
        foreach($this->lrcList as $lrcItem) {
            $tag = $this->getValidLrcTag($lrcItem['time']);
            $line = $this->getValidLrcText($lrcItem['lineLyric']);
            $lrc .= $tag . $line . "\n";
        }
        return $lrc;
    }
}
