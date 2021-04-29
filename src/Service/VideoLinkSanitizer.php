<?php


namespace App\Service;


class VideoLinkSanitizer
{
    public function clean($url){

        $domainName = parse_url($url, PHP_URL_HOST);
        if (str_contains($domainName, 'youtu') ){
            $youtubeId = parse_url($url, PHP_URL_PATH);
            return "https://www.youtube.com/embed" . $youtubeId;

        }
        if(str_contains($domainName, 'dailymotion') || str_contains($domainName, 'dai.ly')){
            $dailyMotionId = parse_url($url, PHP_URL_PATH);
            return "https://www.dailymotion.com/embed/video" . $dailyMotionId;
        }
    }

}