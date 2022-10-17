<?php

$fileurls = file_get_contents('https://inithy.build.superagency.io/sitemap.xml');
$xml = simplexml_load_string($fileurls);

foreach ($xml->url as $urlElement) {
    // get properties
    $url = $urlElement->loc;
    $pagename = explode('/', $url);
    if (isset($pagename[3])) {
        $page_name =  $pagename[3] . '.html';
    } else {
        $page_name =  'index.html';
    }

    $ch = curl_init($url);

    $header = array();
    $header[] = 'Content-length: 0';
    $header[] = 'Referer: https://inithy.build.superagency.io';

    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $data = curl_exec($ch);

    $myfile = fopen("website/" . $page_name, "w") or die("Unable to open file!");

    fwrite($myfile, $data);
    fclose($myfile);
}
