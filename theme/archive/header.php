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
:root{
  --background-image:linear-gradient(-45deg,#fff 25%,#fafafa 25%,#fafafa 50%,#fafafa 50%,#fff 75%,#fafafa 75%);
  --hover-image:linear-gradient(45deg,#fff 25%,#efe4f5 25%,#efe4f5 50%,#fff 50%,#fff 75%,#efe4f5 75%);
  --link:#0059b2;
  --active:#89639e;
  --text:#444;
  --gray:#ccc;
  --alpha:rgb(137 99 158 / 20%);
}
@media (prefers-color-scheme: dark) {
  :root{
    --background-image:linear-gradient(45deg,#000 25%,#111 25%,#111 50%,#000 50%,#000 75%,#111 75%);
    --hover-image:linear-gradient(45deg,#111 25%,#222 25%,#222 50%,#111 50%,#000 75%,#222 75%);
    --link:#89639e;
    --active:#89639e;
    --text:#625769;
    --gray:#3b3540;
    --alpha:rgb(137 99 158 / 50%);
  }
  header .logo{
    color:var(--alpha) !important;
  }
}
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
}
body {
  min-height: 100vh;
  font-family: -apple-system, BlinkMacSystemFont, "Helvetica Neue", "Hiragino Sans GB", "PingFang SC", "Helvetica, Arial", "Segoe UI", "Microsoft YaHei UI", "Microsoft YaHei", "Source Han Sans CN", STHeitiSC-Light, "WenQuanYi Zen Hei", "WenQuanYi Micro Hei", sans-serif;
  color: var(--text);
  background-image: var(--background-image);
  background-size:4px 4px ;
}
header{
  position: relative;
  /* background: linear-gradient(45deg,#000 25%,#111 25%,#111 50%,#000 50%,#000 75%,#111 75%); */
  background-size:10px 10px;
  padding:60px 20px 0px 20px;
  font-size:1rem;
  text-align:center;
  color:var(--text);
  z-index:9;
}

header .logo{
  margin:auto;
  font-family:SourceHanSerifCN-Bold,'Microsoft JhengHei',STFangsong;
  font-size:3rem;
  line-height:1;
  color:var(--active);
}
header section{
  padding:1rem;
}
.shape{
  position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    overflow: hidden;
    line-height: 0;
}
.shape svg {
  position: relative;
    display: block;
    width: calc(100% + 1.3px);
    height: 41px;
    transform: rotateY(180deg);
}

.shape .shape-fill {
    fill: var(--active);
}
</style>
</head>
<body>
<div class="shape">
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
        <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" class="shape-fill"></path>
        <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" class="shape-fill"></path>
        <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" class="shape-fill"></path>
    </svg>
</div>
<header>
  <a href="<?=DOMAIN?>" class="logo"><?=$config["title"] ?></a>
  <section><?=$config["about-content"]?></section>
</header>

