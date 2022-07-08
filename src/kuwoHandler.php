<?php
require('kuwoConverter.php');

class AumKuwoHandler {
    public static $siteSearch = 'https://search.kuwo.cn/r.s?ft=music&rformat=json&encoding=utf8&rn=8&pn=0&rn=30&all=';
    public static $siteDownload = 'https://m.kuwo.cn/newh5/singles/songinfoandlrc?httpsStatus=1&musicId=';
    public static $siteSHeader = array('Host: search.kuwo.cn');
    public static $siteDHeader = array('Host: m.kuwo.cn');
    public static $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.5005.63 Safari/537.36';

    public static function getContent($url, $siteHeader, $defaultValue) {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate,br');
        curl_setopt($curl, CURLOPT_USERAGENT, AumKuwoHandler::$userAgent);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $siteHeader);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);
        curl_close($curl);

        if ($result === false) {
            return $defaultValue;
        } else {
            return $result;
        }
    }

    public static function search($title, $artist) {
        $results = array();
        $url = AumKuwoHandler::$siteSearch . urlencode($title . " " . $artist);
        $jsonContent = AumKuwoHandler::getContent($url, AumKuwoHandler::$siteSHeader, '{"abslist":[]}');
        $jsonContent = str_replace('\'', '"', $jsonContent);
        $json = json_decode($jsonContent, true);

        $songArray = $json['abslist'];
        foreach($songArray as $songItem) {
            if (empty($songItem['MUSICRID'])) {
                continue;
            }

            $song = AumKuwoHandler::decodeHtmlEntity($songItem['SONGNAME']);
            $id = str_replace("MUSIC_", "", $songItem['MUSICRID']);
            $singers = explode("&", $songItem['ARTIST']);
            foreach($singers as $key => $singer) {
                $singers[$key] = AumKuwoHandler::decodeHtmlEntity($singer);
            }
            $des = AumKuwoHandler::decodeHtmlEntity($songItem['ALBUM']);
            if ($des === '' || $des === null) {
                $des = AumKuwoHandler::decodeHtmlEntity($songItem['NAME']);
            }

            array_push($results, array('song' => $song, 'id' => $id, 'singers' => $singers, 'des' => $des));
        }
        return $results;
    }

    public static function downloadLyric($songId) {
        $url = AumKuwoHandler::$siteDownload . $songId;
        $jsonContent = AumKuwoHandler::getContent($url, AumKuwoHandler::$siteDHeader, '{"data":{"lrclist":null}}');
        $json = json_decode($jsonContent, true);
        $lrcList = $json['data']['lrclist'];
        $lyric = new AumKuwoConverter($lrcList);
        return $lyric->getLrc();
    }

    public static function decodeHtmlEntity($str) {
        return html_entity_decode($str, ENT_QUOTES | ENT_HTML5);
    }
}
