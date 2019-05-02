<?php
require "./config.php";
require "./core/gum.php";
header("Content-type:text/xml");

$db   = new db();
$sql  = "SELECT id,time FROM post WHERE status=1 ORDER BY id DESC";
$rows = $db->rows($sql);
if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $protocol = 'https://';
} else {
    $protocol = 'http://';
}
$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
$xml .= "<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\" xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\r\n";

foreach ($rows as $row) {

    $xml .= "<url>\r\n";
    $xml .= "\t<loc>" . $protocol . $_SERVER['HTTP_HOST'] . "/post/" . $row['id'] . ".html</loc>\r\n";
    $xml .= "\t<lastmod>" . date('c', $row['time']) . "</lastmod>\r\n";
    $xml .= "\t<changefreq>daily</changefreq>\r\n";
    $xml .= "\t<priority>0.8</priority>\r\n";
    $xml .= "</url>\r\n";

}

$xml .= "</urlset>\r\n";
echo $xml;
