<?php
$keywords    = $config["keywords"];
$description = $config["description"];
$posts        = $db->rows("SELECT id,title,time,tag_id,tag_name FROM post WHERE status=1 ORDER BY id DESC");
$archives=[];
$year="";
foreach ($posts as $post){
    $post_year=date("Y",$post['time']);
    if($year!=$post_year){
        $year=$post_year;
    } 
    $archives[$year][]=$post;
}
 
?>
<?php include 'header.php'; ?>
<style>
main{
  max-width:620px;
  margin:0 auto;
  padding:1rem;
}
main dl{
    margin-bottom:1rem;
    font-family:DIN;
}
main dl dt{
  margin:0 0.5rem 0.5rem 0.5rem;
  font-weight:500;
  font-size:2.6rem;
  color:#000;
}
main dl dd{
   display:flex;
   justify-content:space-between;
   padding:0.5rem;
   border-bottom:1px dotted #ccc;
}
main dl dd:hover{
    background-color:#fbfbfb;
}
main dl dd span.date{
    white-space: nowrap;
    padding-top:0.3rem;
  text-transform:uppercase;
  font-family:DIN;
   font-size:0.5rem;
   color:rgba(0,0,0,.4);
}
main dl dd a{
  font-weight:500;
  letter-spacing: 0.06rem;
  text-transform:uppercase;
 
 
  color:#000;
}
main dl dd a:hover{
 
    color:#000;
}
</style>
<main>
<?php foreach ($archives as $year=>$posts): ?>
<dl>
<dt><?=$year?></dt>
<?php foreach ($posts as $post): ?>
<dd>
    <a href="<?=DOMAIN ?>/post/<?=$post["id"] ?>.html"><?=$post["title"] ?></a>
    <span class="space"></span>
    <span class="date"><?=date("M d",$post['time'])?></span>
</dd>
<?php endforeach; ?>
</dl>
<?php endforeach; ?>
</main>
<?php include 'footer.php'; ?>