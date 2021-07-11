<style>
footer{
  box-sizing:border-box;
  margin:auto;
  max-width:800px;
  display:flex;
  flex-direction: column;
  align-items:center;
  justify-content:space-between;
  padding:40px 10px;
  text-align:center;
  font-size:0.8rem;
  border-radius:50px;
  color:var(--text);
}
footer a{
  color:var(--text);
}
footer a:hover{
  color:var(--active);
}
footer .link{
  margin:10px;
}
footer .link a{
  font-weight:bold;
  font-size:1rem;
}
@media (max-width: 768px) {
  footer{
    font-size:0.8rem;
  }
}
</style>
<footer>
<div class="link">
<?php $links = $db->rows("SELECT * FROM link  WHERE status=1 ORDER BY sort ASC,id ASC"); ?>
<?php foreach ($links as $link): ?>
<a href="<?=$link["url"] ?>" target="_blank"><?=$link["title"] ?></a>
<?php endforeach; ?>
</div>
<div>
  <a href="https://github.com/jinzhe/gum" target="_blank" title="Powered by GUM">&copy;</a> <?=date("Y") ?>
  <?=$config["copyright"] ?>
  <a href="http://www.miitbeian.gov.cn" target="_blank"><?=$config["icp"] ?></a>
</div>
</footer>
</body>
</html>
<?php 
// $result = ob_get_contents();
// ob_end_clean();
// if($theme_file_name=='post'){
//     file::create(ROOT."post/".$id.".html",$result);
// }elseif($theme_file_name=='index'){
//     file::create(ROOT."/index.html",$result);
// }
// echo $result;
?>