<?php


namespace App\Service;


class VideoLinkSanitizer


{
    public function clean($input)
    {

        // extaire une url dans la chaine de caractere envoyée

        preg_match('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $input, $matches);

        dump($matches);
        if (empty($matches)) {
            return $input;
        } else {
            $url = $matches[0];
        }

        $domainName = parse_url($url, PHP_URL_HOST);
        if (str_contains($domainName, 'youtu.be')) {
            $youtubeId = parse_url($url, PHP_URL_PATH);
            return "https://www.youtube.com/embed" . $youtubeId;
        }
        if (str_contains($url, 'youtube.com/embed')) {
            return $url;
        }

        if (str_contains($domainName, 'dai.ly')) {
            $dailyMotionId = parse_url($url, PHP_URL_PATH);
            return "https://www.dailymotion.com/embed/video" . $dailyMotionId . "?queue-autoplay-next=0";
        }

        if (str_contains($url, 'dailymotion.com/embed') || str_contains($domainName, 'dai.ly')) {
            $dailyMotionId = parse_url($url, PHP_URL_PATH);
            return "https://www.dailymotion.com" . $dailyMotionId . "?queue-autoplay-next=0";
        }

        return $input;
    }


}