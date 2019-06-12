<?php
$keywords    = KEYWORDS;
$description = DESCRIPTION;
?>
<?php include 'header.php';?>

<div class="layout home">
<?php $first = $db->row("SELECT * FROM post WHERE status=1 AND best=1 ORDER BY id DESC LIMIT 1");?>
<?php if (count($first) > 0): ?>
<article class="first-entry">
  <header class="entry-header">
    <h2><?=$first["title"]?></h2>
  </header>
  <section class="entry-content">
   <p><?=empty($first["description"])?gum::short_text($first["content"]):$first["description"]?></p>
  </section>
  <footer class="entry-footer">
  <time><?=date("d F, Y", $first["time"])?></time>
  </footer>
  <a class="entry-link" href="<?=DOMAIN?>/post/<?=$first["id"]?>.html"></a>
</article>
<?php endif;?>


<?php $tags = $db->rows("SELECT * FROM tag  WHERE status=1 ORDER BY sort ASC,id ASC");?>

<?php foreach ($tags as $tag): ?>
    <?php $posts = $db->rows("SELECT * FROM post WHERE status=1 AND tag_id=" . $tag["id"] . " ORDER BY id DESC LIMIT 10");?>

    <?php if (count($posts) > 0): ?>
        <h3 class="title"><?=$tag["name"]?></h3>
        
            <?php foreach ($posts as $post): ?>
            <article class="post-entry">
                <header class="entry-header">
                    <h2><?=$post["title"]?><?php if ($post["best"] == 1): ?>
                <span class="best">推荐</span>
                <?php endif;?></a></h2>
                </header>
                <section class="entry-content">
                <p><?=empty($post["description"])?gum::short_text($post["content"]):$post["description"]?></p>
                </section>
                <footer class="entry-footer">
                    <time><?=date("d F, Y", $post["time"])?></time>
                </footer>
                <a class="entry-link" href="<?=DOMAIN?>/post/<?=$post["id"]?>.html"></a>
            </article>
            <?php endforeach;?>
 
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