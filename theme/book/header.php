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
<link rel="icon" type="image/svg+xml" href="<?=DOMAIN ?>/theme/book/static/logo.svg" />
<link rel="alternate" type="application/rss+xml" title="<?=$config["title"] ?>" href="<?=DOMAIN ?>/rss.php">
<title><?=@$title ?><?=$config["title"] ?></title>
<style>
:root{
  --background:#fff;
  --shadow:#efefef;
  --link:#0059b2;
  --active:#418CE8;
  --text:#444;
  --gray:#ccc;
  --alpha:rgb(65 140 232 / 20%);
  --split:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAADCAYAAABMFFUxAAAAOElEQVQImWN8/vL9/7dvPzIIC/MzSIgJMJACmN68+cjw998/BpABpAImERF+BmYmJrDNJAEGBgYA2l8QAuWFPyUAAAAASUVORK5CYII=) repeat-x bottom/8px auto;
}
@media (prefers-color-scheme: dark) {
  :root{
    --background: #0f1318;
    --shadow: #0b0c0c;
    --link:rgb(254, 202, 107);
    --active:#418CE8;
    --text:rgb(255 255 255 / 50%);
    --gray:rgb(255 255 255 / 20%);
    --alpha:rgb(65 140 232 / 80%);
    --split:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAADCAYAAABMFFUxAAAANElEQVQImZXFwQkAMAgEwb2AFhdItQGL04chJTif0dm7KxNz50aIgVWVdDf/qWXmSOI/AjzaORAGPZrdFAAAAABJRU5ErkJggg==) repeat-x bottom/8px auto;
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
/* cyrillic-ext */
@font-face {
  font-family: 'Roboto';
  font-style: normal;
  font-weight: 400;
  src: url(https://fonts.gstatic.font.im/s/roboto/v27/KFOmCnqEu92Fr1Mu72xKOzY.woff2) format('woff2');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
}
/* cyrillic */
@font-face {
  font-family: 'Roboto';
  font-style: normal;
  font-weight: 400;
  src: url(https://fonts.gstatic.font.im/s/roboto/v27/KFOmCnqEu92Fr1Mu5mxKOzY.woff2) format('woff2');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
}
/* greek-ext */
@font-face {
  font-family: 'Roboto';
  font-style: normal;
  font-weight: 400;
  src: url(https://fonts.gstatic.font.im/s/roboto/v27/KFOmCnqEu92Fr1Mu7mxKOzY.woff2) format('woff2');
  unicode-range: U+1F00-1FFF;
}
/* greek */
@font-face {
  font-family: 'Roboto';
  font-style: normal;
  font-weight: 400;
  src: url(https://fonts.gstatic.font.im/s/roboto/v27/KFOmCnqEu92Fr1Mu4WxKOzY.woff2) format('woff2');
  unicode-range: U+0370-03FF;
}
/* vietnamese */
@font-face {
  font-family: 'Roboto';
  font-style: normal;
  font-weight: 400;
  src: url(https://fonts.gstatic.font.im/s/roboto/v27/KFOmCnqEu92Fr1Mu7WxKOzY.woff2) format('woff2');
  unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;
}
/* latin-ext */
@font-face {
  font-family: 'Roboto';
  font-style: normal;
  font-weight: 400;
  src: url(https://fonts.gstatic.font.im/s/roboto/v27/KFOmCnqEu92Fr1Mu7GxKOzY.woff2) format('woff2');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
/* latin */
@font-face {
  font-family: 'Roboto';
  font-style: normal;
  font-weight: 400;
  src: url(https://fonts.gstatic.font.im/s/roboto/v27/KFOmCnqEu92Fr1Mu4mxK.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
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
  font-family: Roboto,-apple-system, BlinkMacSystemFont, "Helvetica Neue", "Hiragino Sans GB", "PingFang SC", "Helvetica, Arial", "Segoe UI", "Microsoft YaHei UI", "Microsoft YaHei", "Source Han Sans CN", STHeitiSC-Light, "WenQuanYi Zen Hei", "WenQuanYi Micro Hei", sans-serif;
  color: var(--text);
  background:var(--background);
}
header{
  position: relative;
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
header .logo{
  width:150px;
  height:36px;
}
header .logo path{
  fill:var(--active);
}
header section{
  padding:1rem;
} 
</style>
</head>
<body>
 
<header>
  <a href="<?=DOMAIN?>" class="logo" title="<?=$config["title"] ?>">
<svg  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 47.26 11.48"><path d="M9.7,2.47s1.05.77,1.69,1.3c0,.13-.18.19-.36.19H7.2l1.9.37c0,.16-.15.26-.44.28A12.72,12.72,0,0,1,6.79,6.27H7.92l.88-1.1s1,.74,1.62,1.25c0,.13-.18.19-.35.19H6.58V8.47H8.44l.93-1.14s1,.75,1.66,1.28c0,.13-.18.19-.34.19H6.58v2.32a2.28,2.28,0,0,1-1.48.36H4.75V8.8H.55L.46,8.47H4.75V6.61H1.2l-.1-.34H3.5A1.28,1.28,0,0,1,3.06,6,4.8,4.8,0,0,0,2.69,4L2.78,4c2.51.53,2.3,1.94,1.49,2.29H6.39A16.58,16.58,0,0,0,6.81,4H.1L0,3.62H4.75V2.06H1.39l-.1-.33H4.75V.06L7,.23c0,.15-.1.26-.39.31V1.73H7.66L8.56.6s1,.76,1.66,1.27c0,.13-.17.19-.35.19H6.58V3.62h2.2Z" /><path d="M16.93,3.2c-.11.12-.24.16-.54.19a9.29,9.29,0,0,1-1.24,1.84c2.41,1,1.17,2.89.18,2a4.53,4.53,0,0,0-.25-1.34v5a1.56,1.56,0,0,1-1.32.49h-.28V6.85a10.75,10.75,0,0,1-1.6,1l-.1-.1A11.59,11.59,0,0,0,14.7,3H12.2l-.1-.34h2.53l.87-.84ZM13.21,0c3.54.49,1.81,3.07.43,1.88A4.64,4.64,0,0,0,13.13.08Zm3.34,1.38-.1-.34h4.63L21.89,0s.94.73,1.49,1.22c0,.14-.17.2-.33.2Zm6.86,5a.83.83,0,0,1-.5.25V11a2.62,2.62,0,0,1-1.33.39h-.27v-.81H18.14v.4c0,.15-.67.5-1.33.5h-.23v-6l1.63.63h3l.74-.8ZM20.83,5.3V4.85H18.7V5c0,.13-.7.46-1.34.46h-.21V1.91l1.6.61h2l.72-.79L23,2.87a.82.82,0,0,1-.48.25V5a3.28,3.28,0,0,1-1.36.33ZM18.14,6.43V8.08h.92V6.43Zm0,3.78h.92V8.42h-.92Zm2.69-5.7V2.85H18.7V4.51Zm-.39,1.92V8.08h.87V6.43Zm.87,3.78V8.42h-.87v1.79Z"/><path d="M35.05,5.35c-.09.14-.2.19-.48.23a8.35,8.35,0,0,1-1.69,3.25,9.35,9.35,0,0,0,2.67,1v.13a2,2,0,0,0-1.45,1.5A6.43,6.43,0,0,1,31.83,9.8a9.48,9.48,0,0,1-3.75,1.61L28,11.27a7.77,7.77,0,0,0,3.21-2.41,9.86,9.86,0,0,1-.92-3.59h-.51c0,1.89-.36,4.4-2.4,6l-.1-.08a17.75,17.75,0,0,0,.86-6.6V3.56a.64.64,0,0,1-.34,0A15.15,15.15,0,0,1,26.64,5l.84.31c0,.12-.15.19-.37.24V11a1.9,1.9,0,0,1-1.37.52h-.31V6.2a12,12,0,0,1-1.43,1l-.1-.11a16.37,16.37,0,0,0,2.34-4.73A12.22,12.22,0,0,1,24,3.62l-.09-.12A13.58,13.58,0,0,0,26.07.05L28,1.21c-.06.12-.18.19-.46.16a13.87,13.87,0,0,1-1.29,1l1.91,1V1.63l1.91.65h.66V.18l2,.17c0,.15-.09.26-.38.31V2.28h1l.83-.79,1.36,1.32a.67.67,0,0,1-.47.14c-.55.38-1.48,1-2.06,1.37l-.1-.07c.13-.42.34-1.12.49-1.64h-1V4.93h.31l.89-.88ZM29.8,4.93h.92V2.61H29.8V4.93Zm.69.34A5.39,5.39,0,0,0,31.8,8a9,9,0,0,0,1-2.71Z"/><path d="M39,6.38a4.92,4.92,0,0,1-2.91,5L36,11.3a7.86,7.86,0,0,0,1.21-4.92V3.56l2,.67H45l.5-.68a4.69,4.69,0,0,1-.77.07h-.29V3.09H39.17l-.51.74L37,3a2.57,2.57,0,0,1,.45-.42v-2l2.15.2c0,.18-.12.3-.44.35V2.76h1.8V.06l2.15.16c0,.16-.1.28-.43.34v2.2h1.81V.56l2.19.18c0,.16-.11.29-.45.34V3.31s0,0-.08.07c.31.27.8.67,1.14,1,0,.13-.17.19-.34.19H39Zm6.87.56s.9.75,1.43,1.25c0,.13-.17.19-.33.19h-3.4v2.69c0,.08-.54.37-1.46.37h-.34V8.38H39.05L39,8.05h2.78v-2H39.54l-.1-.33h5l.76-1.07s.89.72,1.41,1.21c0,.13-.17.19-.34.19H43.53v2H45Z"/></svg>
</a>
  <section><?=$config["about-content"]?></section>
</header>

