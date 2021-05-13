<?php
if ($id == "") {
    gum::not_found();
}

$post = $db->row("SELECT * FROM post WHERE status=1 AND id=?", ["params" => [$id]]);
if (!$post) {
    gum::not_found();
}
$title       = $post["title"] . " - ";
$keywords    = htmlspecialchars($post["keywords"]);
$description = $post["description"] ?? gum::short_text($post["content"]);
// 上一篇
$prev = $db->row("SELECT * FROM post WHERE status=1 AND id>? ORDER BY id ASC", ["params" => [$id]]);
// 下一篇
$next = $db->row("SELECT * FROM post WHERE status=1 AND id<? ORDER BY id DESC", ["params" => [$id]]);

$active = $post["tag_id"];
$db->update("post", "view=view+1", "id='" . $id . "'");
?><?php include 'header.php'; ?>
<style>
article{
  max-width:800px;
  min-height:500px;
  margin:0 auto;
  padding:1rem;
  color:#000;
}
article > aside{
	margin-bottom:0.5rem;
	font-family:DIN;
	text-transform:uppercase;
	font-size: 0.8rem;
	color:rgba(0,0,0,.3);
}
article > h1{
	display:inline-block;
	margin-bottom:1rem;
	box-shadow: inset 0 -10px 0 #ceebe7;
	text-transform:uppercase;
	font-family:DIN;
	line-height:1.2;
	color:#000;
}
article > section{
	text-align: justify;
	text-justify: inter-ideograph;
	word-break: break-all;
	word-wrap: break-word;
	font-size: 1rem;
	letter-spacing: 0.06rem;
	line-height: 1.6;
	color: #000;
}
 
 
article > section del {
  text-decoration: none;
  background: linear-gradient(to right, #292734 100%, transparent 0) 0 50% / 1px 1px repeat-x;
}
article > section p,
article > section ul,
article > section ol,
article > section dl {
  margin-bottom: 24px;
}
article > section pre,
article > section table {
  margin-bottom: 32px;
}
article > section ul,
article > section ol {
  padding-left: 30px;
}
article > section li {
  margin-top: 5px;
}
article > section li p {
  margin-bottom: 0;
}
article > section dl {
  display: flex;
  flex-wrap: wrap;
  margin: 0;
}
article > section dt {
  width: 25%;
  font-weight: 700;
}
article > section dd {
  width: 75%;
  margin-left: 0;
  padding-left: 10px;
}
article > section dt ~ dt,
article > section dd ~ dd {
  margin-top: 10px;
}
article > section table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
}
article > section th,
article > section td {
  padding: 10px;
  border-bottom: 1px solid #eee;
}
article > section th {
  font-size: 14px;
  text-align: left;
}
article > section blockquote {
  margin: 0 0 0 calc(24px * -1);
  padding: 0 0 0 21px;
  font-style: italic;
  border-left: 3px solid #292734;
}
article > section hr {
  height: 0;
  margin-top: 72px;
  margin-bottom: 72px;
  text-align: center;
  border: 0;
  overflow: visible;
}
article > section hr::before {
  content: "...";
  display: inline-block;
  color: #555;
  font-size: 32px;
  letter-spacing: 20px;
  transform: translate(10px, -40px);
}

article > section video {
  width: 100%;
  height: auto;
}
@media (min-width: 992px) {
  article > section video {
    display: block;
    max-width: 50%;
    margin: auto;
    margin-bottom: 1rem;
  }
}
article > section img {
  max-width: 100%;
  vertical-align: middle;
}
 
article > section blockquote {
  margin: 0 0 3rem 0;
  padding: 15px 20px;
  background-color: #ecf8ff;
  border-left: 3px solid #292734;
  font-size: 1rem;
  border-radius: 4px;
  color: #5e6d82;
}
article > section blockquote p:last-child {
  margin-bottom: 0;
}
article > section a[href] {
	color: #67cdcc;
	text-decoration:underline;
}
 
 

</style>

<article>
	<aside>
		<?=date("M d, Y", $post["time"]) ?>
	</aside>
    <h1><?=$post["title"] ?></h1>
	<section><?=str_replace('<pre class="language-', '<pre class="line-numbers language-', str_replace('<img', '<img loading="lazy"', $post["content"])) ?></section>
</article>

	<div class="pagination">
		<?php if (!empty($prev)): ?>
		<a class="prev" href="<?=DOMAIN ?>/post/<?=$prev["id"] ?>.html" title="<?=$prev["title"] ?>"></a>
		<?php else: ?>
		<a class="prev disabled"></a>
		<?php endif; ?>

		<?php if (!empty($next)): ?>
		<a class="next" href="<?=DOMAIN ?>/post/<?=$next["id"] ?>.html" title="<?=$next["title"] ?>"></a>
		<?php else: ?>
		<a class="next disabled"></a>
		<?php endif; ?>
	</div>
 
<?php if (strpos($post["content"], "language-") != false): //只有内容包含代码块才引用 ?>
	<link rel="stylesheet" href="<?=DOMAIN ?>/theme/archive/static/prism.css">
	<script src="<?=DOMAIN ?>/theme/archive/static/prism.js"></script>
<?php endif; ?>

<?php if (strpos($post["content"], "<img") != false): //只有内容包含img才引用 ?>
	<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="pswp__bg"></div>
	<div class="pswp__scroll-wrap">
	<div class="pswp__container">
	<div class="pswp__item"></div>
	<div class="pswp__item"></div>
	<div class="pswp__item"></div>
	</div>
	<div class="pswp__ui pswp__ui--hidden">
	<div class="pswp__top-bar">
	<div class="pswp__counter"></div>
	<button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
	<button class="pswp__button pswp__button--share" title="Share"></button>
	<button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
	<button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
	<div class="pswp__preloader">
	<div class="pswp__preloader__icn">
	<div class="pswp__preloader__cut">
	<div class="pswp__preloader__donut"></div>
	</div>
	</div>
	</div>
	</div>
	<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
	<div class="pswp__share-tooltip"></div>
	</div>
	<button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
	</button>
	<button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
	</button>
	<div class="pswp__caption">
	<div class="pswp__caption__center"></div>
	</div>
	</div>
	</div>
	</div>
	<link href="<?=DOMAIN ?>/theme/archive/static/photoswipe.css" rel="stylesheet">
	<script src="<?=DOMAIN ?>/theme/archive/static/photoswipe.js"></script>
	<?php endif; ?>
<script src="<?=DOMAIN ?>/api.php?class=post&method=update_view&id=<?=$id?>"></script>
<?php include 'footer.php'; ?>
