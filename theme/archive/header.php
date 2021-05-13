<?php ob_start();?>
<!DOCTYPE html>
<html lang="zh-Hans">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, viewport-fit=cover">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="<?=$config["title"] ?>">
<meta name="format-detection" content="telephone=no">
<meta name="format-detection" content="email=no">
<meta name="theme-color" content="#ffffff">
<?php if (isset($post)): ?>
<meta property="og:site_name" content="<?=$config["title"] ?>">
<meta property="og:type" content="article">
<meta property="og:title" content="<?=htmlspecialchars($post["title"]) ?>">
<meta property="og:url" content="<?=DOMAIN ?>/post/<?=$post["id"] ?>.html">
<?php if (!empty($post["cover"])): ?>
<meta property="og:image" content="<?=htmlspecialchars($post["cover"]) ?>">
<?php endif; ?>
<?php if (!empty($description)): ?>
<meta property="og:description" content="<?=$description ?>">
<?php endif; ?>
<?php endif; ?>
<?php if (!empty($keywords)): ?>
<meta name="keywords" content="<?=$keywords ?>">
<?php endif; ?>
<?php if (!empty($description)): ?>
<meta name="description" content="<?=$description ?>">
<?php endif; ?>
<?php if (!empty($post["author"])): ?>
<meta name="author" content="<?=htmlspecialchars($post["author"]) ?>">
<?php endif; ?>
<link rel="icon" type="image/svg+xml" href="<?=DOMAIN ?>/theme/archive/static/logo.svg" />
<link rel="alternate" type="application/rss+xml" title="<?=$config["title"] ?>" href="<?=DOMAIN ?>/rss.php">
<title><?=@$title ?><?=$config["title"] ?></title>
<style>
@font-face {
  font-family: "DIN";
  src: url("<?=DOMAIN ?>/theme/archive/fonts/din.eot");
  src: url("<?=DOMAIN ?>/theme/archive/fonts/din.woff") format("woff"), url("../fonts/din.ttf") format("truetype");
}
* {
  margin: 0;
  padding: 0;
  outline: 0;
  border: 0;
  -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
  -webkit-touch-callout: none;
  -webkit-font-smoothing: antialiased;
}
a {
  display: inline-block;
  text-decoration: none;
  color:#555;
}
body {
  min-height: 100vh;
  font-family: -apple-system, BlinkMacSystemFont, "Helvetica Neue", "Hiragino Sans GB", "PingFang SC", "Helvetica, Arial", "Segoe UI", "Microsoft YaHei UI", "Microsoft YaHei", "Source Han Sans CN", STHeitiSC-Light, "WenQuanYi Zen Hei", "WenQuanYi Micro Hei", sans-serif;
  color: #000;
  background: linear-gradient(-45deg,#fff 25%,#fafafa 25%,#fafafa 50%,#fafafa 50%,#fff 75%,#fafafa 75%);
  background-size:4px 4px ;
}
header{
  position: relative;
  background-color:#000;
  background: linear-gradient(45deg,#000 25%,#111 25%,#111 50%,#000 50%,#000 75%,#111 75%);
  background-size:10px 10px;
  padding:50px 20px;
  font-size:1rem;
  text-align:center;
  color:rgba(255,255,255,.8);
}

header .logo{
  margin:auto;
  font-family:SourceHanSerifCN-Bold,'Microsoft JhengHei',STFangsong;
  font-size:3rem;
  line-height:1;
  color:#fff;
}
header section{
  padding:1rem;
}
</style>
</head>
<body>
<header>
  <a href="<?=DOMAIN?>" class="logo"><?=$config["title"] ?></a>
  <section><?=$config["about-content"]?></section>
</header>

