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
  max-width:800px;
  margin:0 auto;
  padding:1rem;
}
main dl{
  margin-bottom:1rem;
  margin-top:0px;
  font-family:DIN;
}
 
main dl dt{
  margin:0 0.5rem 0.5rem 0.5rem;
  font-weight:500;
  font-size:2.4rem;
  color:var(--text);
}
main dl dd{
  clear:left;
  display:flex;
  justify-content:space-between;
  padding:0.5rem 1rem;
  border-radius:8px;
  transition:.3s;
}
main dl dd:last-child{
  border-bottom-color:transparent;
}
main dl dd::before{
  content:"";
  margin-top:10px;
  width:3px;
  height:3px;
  border-radius:50%;
  box-shadow:0 0 0 2px var(--text);
  margin-right:10px;
}
@media all and (min-width: 400px) {
  main dl dd:hover{
    background-image: var(--hover-image);
    background-size:4px 4px;
  }
  main dl dd:hover::before{
    box-shadow:0 0 0 2px var(--active);
  }
  main dl dd:hover a{
      color:var(--active);
  }
  main dl dd:hover .date{
    color:var(--active);
  }
}


main dl dd span.date{
  white-space: nowrap;
  padding-top:0.2rem;
  text-transform:uppercase;
  font-size:0.8rem;
  color:var(--gray);;
}
main dl dd a{
  flex:1;
  font-family: tenon, -apple-system, BlinkMacSystemFont, Segoe UI, Helvetica, Arial, sans-serif;
  font-weight:500;
  letter-spacing: 0.06rem;
  text-transform:uppercase;
  color:var(--text);
}

@media (max-width: 768px) {
  main dl{
    display:block;
  }
}
</style>
<main>
<?php foreach ($archives as $year=>$posts): ?>
<dl>
<dt><?=$year?></dt>
<?php foreach ($posts as $post): ?>
<dd><a href="<?=DOMAIN ?>/post/<?=$post["id"] ?>.html"><?=$post["title"] ?></a><span class="date"><?=date("m/d",$post['time'])?></span></dd>
<?php endforeach; ?>
</dl>
<?php endforeach; ?>
</main>
<?php include 'footer.php'; ?>