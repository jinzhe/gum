<?php
$keywords    = $config["keywords"];
$description = $config["description"];

$posts        = [];
$page_size    = 20;
$page_current = $page ?? 1;
$sql          = "SELECT * FROM post WHERE status=1";
$count        = $db->count($sql); //先查数量
if ($count > 0) {
    $page_count   = ceil($count / $page_size);
    $page_current = max(min($page_current, $page_count), 1);
    $prev_page    = $page_current - 1 < 1 ? 1 : $page_current - 1;
    $next_page    = $page_current + 1 > $page_count ? $page_count : $page_current + 1;
    $rows         = $db->rows($sql . " ORDER BY id DESC LIMIT " . (($page_current - 1) * $page_size) . "," . $page_size); //获取ID(索引)
    $ids          = implode(",",array_column($rows, 'id')); //取出id集合字符串
    $posts        = $db->rows("SELECT * FROM post WHERE id IN (" . $ids . ") ORDER BY id DESC");
}
?>
<?php include 'header.php'; ?>

<div class="layout">
<?php if (count($posts) > 0): ?>

 
<?php foreach ($posts as $post): ?>
<article>
    <a class="title" href="<?=DOMAIN ?>/post/<?=$post["id"] ?>.html"><?=$post["title"] ?></a>
    <section><?=empty($post["description"]) ? gum::short_text($post["content"]) : $post["description"] ?></section>
    <?php if (!empty($post["cover"])): ?>
        <img src="<?=$post["cover"] ?>" class="cover">
    <?php endif; ?>
</article>
<?php endforeach; ?>
 

    <?php if ($page_count > 1): ?>
<div class="pagination">
<?php if ($page_current > 1): ?>
    <a class="prev" href="<?=DOMAIN ?>/page/<?=$prev_page ?>.html"></a>
<?php else: ?>
    <a class="prev disabled"></a>
<?php endif; ?>

<?php if ($page_current < $page_count): ?>
    <a class="next" href="<?=DOMAIN ?>/page/<?=$next_page ?>.html"></a>
<?php else: ?>
    <a class="next disabled"></a>
<?php endif; ?>
</div>
<?php endif; ?>

<?php else: ?>
    <div class="nodata">找不到数据</div>
<?php endif; ?>

<div class="links">
<?php $links = $db->rows("SELECT * FROM link  WHERE status=1 ORDER BY sort ASC,id ASC"); ?>
<?php foreach ($links as $link): ?>
<a href="<?=$link["url"] ?>" target="_blank"><?=$link["title"] ?></a>
<?php endforeach; ?>
</div>
</div>
 
<?php include 'footer.php'; ?>