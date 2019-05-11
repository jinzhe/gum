<?php
$keywords    = KEYWORDS;
$description    = DESCRIPTION;
?>
<?php include 'header.php';?>

<div class="layout home">
<?php $tags = $db->rows("SELECT * FROM tag  WHERE status=1 ORDER BY sort ASC,id ASC");?>

<?php foreach ($tags as $tag): ?>
    <?php $posts = $db->rows("SELECT * FROM post WHERE status=1 AND tag_id=" . $tag["id"] . " ORDER BY id DESC LIMIT 10");?>

    <?php if (count($posts) > 0): ?>
        <h3 class="title"><?=$tag["name"]?></h3>
        <?php if ($tag["tag"]=="photo"): ?>
            <div class="photos">
                <?php foreach ($posts as $post): ?>
                <div class="photo">
                    <a href="<?=DOMAIN?>/post/<?=$post["id"]?>.html" class="cover"<?php if (!empty($post["cover"])): ?> style="background-image:url(<?=$post["cover"]?>)"<?php endif;?>></a>
                    <div class="info">
                        <div class="title"><?=$post["title"]?></div>
                        <div class="description"><?=$post["description"]?></div>
                    </div>
                    <?php if ($post["best"]==1): ?>
                        <div class="best">推荐</div>
                    <?php endif;?> 
                </div>
                <?php endforeach;?>
            </div>
        <?php else:?>
        <ul class="list">
            <?php foreach ($posts as $post): ?>
            <li>
               
                <a href="<?=DOMAIN?>/post/<?=$post["id"]?>.html"><?=$post["title"]?> <?php if ($post["best"]==1): ?>
                <span class="best">推荐</span>
                <?php endif;?></a>
               
     
                <span class="date" title="<?=date("d F, Y", $post["time"])?>"><?=date("m/d", $post["time"])?></span>
            </li>
            <?php endforeach;?>
        </ul>
        <?php endif;?>
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