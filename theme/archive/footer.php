 
<footer>
    <?php $links = $db->rows("SELECT * FROM link  WHERE status=1 ORDER BY sort ASC,id ASC"); ?>
    <?php foreach ($links as $link): ?>
    <a href="<?=$link["url"] ?>" target="_blank"><?=$link["title"] ?></a>
    <img src="<?=DOMAIN ?>/theme/archive/static/link.svg" width="12" align="absmiddle">
    <?php endforeach; ?>
    <br>
    <a href="https://github.com/jinzhe/gum" target="_blank" title="Powered by GUM">&copy;</a> <?=date("Y") ?>
    <?=$config["copyright"] ?>
    <a href="http://www.miitbeian.gov.cn" target="_blank"><?=$config["icp"] ?></a>
</footer>
</body>
</html>
<?php 
$result = ob_get_contents();
ob_end_clean();
if($theme_file_name=='post'){
    file::create(ROOT."post/".$id.".html",$result);
}elseif($theme_file_name=='index'){
    file::create(ROOT."/index.html",$result);
}
echo $result;
?>