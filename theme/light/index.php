<?php include 'header.php';?>

<div class="layout home">
<?php $tags = $db->rows("SELECT * FROM tag  WHERE status=1 ORDER BY sort ASC,id ASC");?>

<?php foreach ($tags as $tag): ?>
    <?php $posts = $db->rows("SELECT * FROM post WHERE status=1 AND tag_id=" . $tag["id"] . " ORDER BY id DESC LIMIT 10");?>

    <?php if (count($posts) > 0): ?>
        <h3 class="title"><?=$tag["name"]?></h3>
        <ul class="list">
            <?php foreach ($posts as $post): ?>
            <li>
                <span class="link">
                    <a href="<?=DOMAIN?>/post/<?=$post["id"]?>.html"><?=$post["title"]?></a>
                    <?php if ($post["best"]==1): ?>
                    <span class="best">推荐</span>
                    <?php endif;?>
                </span>
                <span class="date" title="<?=date("d F, Y", $post["time"])?>"><?=date("m/d", $post["time"])?></span>
            </li>
            <?php endforeach;?>
        </ul>
    <?php endif;?>
<?php endforeach;?>

<h3 class="title">邻居</h3>
<div class="links">
<?php $links = $db->rows("SELECT * FROM link  WHERE status=1 ORDER BY sort ASC,id ASC");?>
<?php foreach ($links as $link): ?>
<a href="<?=$link["url"]?>" target="_blank"><?=$link["title"]?></a>
<?php endforeach;?>
</div>
</div>


<?php include 'footer.php';?>