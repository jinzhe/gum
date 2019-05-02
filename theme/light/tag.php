<?php
if ($id == "") {
    gum::not_found();
}
// 获取tag信息
$tag = $db->row("SELECT name FROM tag WHERE id=?",["params"=>[$id]]);
if (!$tag) {
    gum::not_found();
}
$title        = $tag["name"] . " - ";
$active       = $id;
$posts        = [];
$page_size    = 10;
$page_current = $page;
$sql          = "SELECT * FROM post WHERE status=1 AND tag_id='" . $id . "'";
$count        = $db->count($sql); //先查数量
if ($count > 0) {
    $page_count   = ceil($count / $page_size);
    $page_current = max(min($page_current, $page_count), 1);
    $prev_page    = $page_current - 1 < 1 ? 1 : $page_current - 1;
    $next_page    = $page_current + 1 > $page_count ? $page_count : $page_current + 1;
    $rows         = $db->rows($sql . " ORDER BY id DESC LIMIT " . (($page_current - 1) * $page_size) . "," . $page_size); //获取ID(索引)
    $ids          = implode(array_column($rows, 'id'), ","); //取出id集合字符串
    $posts        = $db->rows("SELECT * FROM post WHERE id IN (" . $ids . ") ORDER BY id DESC");
}
?>

<?php include 'header.php';?>

<div class="layout">
<?php if (!empty($posts)): ?>

    <ul class="list">
    <?php foreach ($posts as $post): ?>

    <li>
        <span class="link">
            <a href="<?=DOMAIN?>/post/<?=$post["id"]?>.html"><?=$post["title"]?></a>
            <?php if ($post["best"]==1): ?>
            <span class="best">推荐</span>
            <?php endif;?>
        </span>
        <span class="date"><?=date("Y-m-d", $post["time"])?></span>
    </li>

    <?php endforeach;?>
    </ul>


    <?php if ($page_count > 1): ?>
        <div class="pagination">
            <?php if ($page_current > 1): ?>
                <a href="<?=DOMAIN?>/tag/<?=$id?>-<?=$prev_page?>.html">PREV</a>
            <?php else: ?>
                <span>PREV</span>
            <?php endif;?>

            <?php if ($page_current < $page_count): ?>
                    <a href="<?=DOMAIN?>/tag/<?=$id?>-<?=$next_page?>.html">NEXT</a>
            <?php else: ?>
                <span>NEXT</span>
            <?php endif;?>
        </div>
    <?php endif;?>

<?php else: ?>
    <div class="nodata">找不到数据</div>
<?php endif;?>
</div>
<?php include 'footer.php';?>