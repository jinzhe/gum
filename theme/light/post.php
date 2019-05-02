<?php
if ($id == "") {
    gum::not_found();
}

$post = $db->row("SELECT * FROM post WHERE status=1 AND id=?",["params"=>[$id]]);
if (!$post) {
    gum::not_found();
}
$title       = $post["title"] . " - ";
$keywords    = htmlspecialchars($post["keywords"]);
$description = $post["description"];
if (empty($description)) {
    $description = gum::short_text($post["content"]);
}
// 上一篇
$prev = $db->row("SELECT * FROM post WHERE status=1 AND id>? ORDER BY id ASC",["params"=>[$id]]);
// 下一篇
$next = $db->row("SELECT * FROM post WHERE status=1 AND id<? ORDER BY id DESC",["params"=>[$id]]);

$active = $post["tag_id"];
$db->update("post", "view=view+1", "id='" . $id . "'");
?><?php include 'header.php';?>

<div class="layout">
<article>
    <div class="title"><?=$post["title"]?></div>
    <div class="meta">
        <time>发表于 <?=date("d F, Y", $post["time"])?></time>
        <span>阅读 <?=number_format($post["view"])?></span>
        <?php if (!empty($post["author"])): ?>
            <span>作者 <?=$post["author"]?></span>
		<?php endif;?>

    </div>
	<section><?=$post["content"]?></section>

	<?php if (!empty($post["update_time"])): ?>
		<div class="update-time">[编辑于 <?=date("Y/m/d H:i", $post["update_time"])?>]</div>
	<?php endif;?>
	<div class="next">
		<?php if (!empty($prev)): ?>
		<a href="<?=DOMAIN?>/post/<?=$prev["id"]?>.html" title="<?=$prev["title"]?>">PREV</a>
		<?php endif;?>

		<?php if (!empty($next)): ?>
		<a href="<?=DOMAIN?>/post/<?=$next["id"]?>.html" title="<?=$next["title"]?>">NEXT</a>
		<?php endif;?>
	</div>

</article>
</div>
<?php if (strpos($post["content"], "language-") != false): //只有内容包含代码块才引用?>
				<link rel="stylesheet" href="<?=DOMAIN?>/theme/light/static/prism.css">
				<script src="<?=DOMAIN?>/theme/light/static/prism.js"></script>
				<?php endif;?>

<?php if (strpos($post["content"], "<img") != false): //只有内容包含img才引用?>
				<link href="<?=DOMAIN?>/theme/light/static/photoviewer.css" rel="stylesheet">
				<script src="<?=DOMAIN?>/theme/light/static/photoviewer.js"></script>
				<script>
				var items = [];
				document.querySelectorAll("article img").forEach(function(v,k){
				items.push({
					src:v.getAttribute("src"),
					title:"预览",
				})
				v.setAttribute("data-index",k);
				v.addEventListener("click",function (e) {
					e.preventDefault();
					if(document.querySelector(".photoviewer-modal")){
						return;
					}
					new PhotoViewer(items, {
						title: false,
						index: e.target.getAttribute("data-index")
					});
				},false);
				});
				</script>
				<?php endif;?>
<?php include 'footer.php';?>