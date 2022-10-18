<?php
ini_set('max_execution_time', '600');

// please do not keep ending slash in the referer url

$referer = 'https://inithy.build.superagency.io';

$sitemap = file_get_contents($referer . '/sitemap.xml');

$xml = simplexml_load_string($sitemap);


// start creating a sitemap in website folder 

$file_sitemap = fopen("website/sitemap.xml", "w") or die("Unable to open file!");

fwrite($file_sitemap, $sitemap);
fclose($file_sitemap);

// end creating a sitemap in website folder 

// start creating a vercel.json in website folder 

$file_vercel = fopen("website/vercel.json", "w") or die("Unable to open file!");
$vercel_content = '{
                        "cleanUrls": true
                   }';
fwrite($file_vercel, $vercel_content);
fclose($file_vercel);

// end creating a vercel.json in website folder 

// start creating a robots.txt in website folder 
$robot_content = file_get_contents($referer . '/robots.txt');
$file_robot = fopen("website/robots.txt", "w") or die("Unable to open file!");
fwrite($file_robot, $robot_content);
fclose($file_robot);
// end creating a robots.txt in website folder 

foreach ($xml->url as $urlElement) {
    // get properties
    $url = $urlElement->loc;
    $pagename = explode($referer, $url);

    if (isset($pagename[1])) {
        $path_structure = explode('/', $pagename[1]);
        if (count($path_structure) == 1) {
            $page_name =  'index.html';
        } elseif (count($path_structure) == 2) {
            $page_name =  $path_structure[1] . '.html';
        } else {
            if (!is_dir('website/' . $path_structure[1])) {
                mkdir('website/' . $path_structure[1]);
            }
            $page_name =  $path_structure[2] . '.html';
        }
    }

    $ch = curl_init($url);

    $header = array();
    $header[] = 'Content-length: 0';
    $header[] = 'Referer: ' . $referer;

    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $data = curl_exec($ch);

    if (isset($path_structure[2])) {
        $myfile = fopen("website/" . $path_structure[1] . "/" . $page_name, "w") or die("Unable to open file!");
    } else {
        $myfile = fopen("website/" . $page_name, "w") or die("Unable to open file!");
    }

    fwrite($myfile, $data);
    fclose($myfile);
}
