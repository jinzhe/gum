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

<div class="layout">
<article class="detail">
    <div class="title"><?=$post["title"] ?></div>
    <div class="meta">
        <time>发表于 <?=date("Y年m月d日", $post["time"]) ?></time>
        <span>阅读 <?=number_format($post["view"]) ?></span>
        <?php if (!empty($post["author"])): ?>
            <span>作者 <?=$post["author"] ?></span>
		<?php endif; ?>

    </div>
	<section><?=str_replace('<img', '<img loading="lazy"', $post["content"]) ?></section>

	<?php if (!empty($post["update_time"])): ?>
		<div class="update-time">[编辑于 <?=date("Y/m/d H:i", $post["update_time"]) ?>]</div>
	<?php endif; ?>

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


</div>
<?php if (strpos($post["content"], "language-") != false): //只有内容包含代码块才引用 ?>
				<link rel="stylesheet" href="<?=DOMAIN ?>/theme/paper/static/prism.css">
				<script src="<?=DOMAIN ?>/theme/paper/static/prism.js"></script>
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
			<link href="<?=DOMAIN ?>/theme/paper/static/photoswipe.css" rel="stylesheet">
			<script src="<?=DOMAIN ?>/theme/paper/static/photoswipe.js"></script>
			<?php endif; ?>
<?php include 'footer.php'; ?>