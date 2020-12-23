
<?php include 'header.php';?>

<div class="about">
    <?php if(empty($config["about-avatar"])):?>
        <img src="<?=DOMAIN?>/theme/paper/static/avatar.jpg" class="avatar">
    <?php else:?>
        <img src="<?=$config["about-avatar"]?>" class="avatar">
    <?php endif;?>

    <div class="info"><?=$config["about-content"]?></div>
</div>
<?php include 'footer.php';?>