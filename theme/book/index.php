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
.grid{
  position:absolute;
  top:0;
  right:0px;
  width:200px;
  height:200px;
  overflow:hidden;
  opacity:0.6;
}
.grid .shadow{
  position:absolute;
  top:0;
  left:0;
  width:100%;
  height:100%;
  box-shadow:100px -100px 100px var(--background) inset;
}
.grid .shadow-top{
  position:absolute;
  top:0;
  right:0;
  width:100%;
  height:100%;
  box-shadow:-20px -20px 20px var(--background) inset;
}
.grid .line{
  position:absolute;
  top:0;
  right:0;
  width:200px;
  height:1px;
  transform-origin:top right;
  background:var(--active);
}
.grid .line:nth-child(1){
  top:50px;
}
          

.grid .line:nth-child(2){
  top:100px;
}
    

.grid .line:nth-child(3){
  top:150px;
}
    

.grid .line:nth-child(4){
  top:-50px;
}
    

.grid .line:nth-child(5){
  top:0px;
}

.grid .line:nth-child(6){
  top:50px;
}

.grid .line:nth-child(7){
  top:100px;
}

.grid .vertical{
  transform:rotate(-30deg);
}
    

.grid .horizontal{
  transform:rotate(30deg);
}
          
 
main{
  max-width:680px;
  margin:0 auto;
  padding:1rem;
}
main dl{
  position: relative;
  margin-bottom:4rem;
  margin-top:0px;
  padding:40px;
  border-radius:20px;
  background:var(--background);
  box-shadow: 0px 40px 50px var(--shadow);
}
 
main dl dt{
  margin:0 0.5rem 0.5rem 0;
  font-weight:500;
  font-size:2.4rem;
  color:var(--active);
}
main dl dd{
  position: relative;
  clear:left;
  display:flex;
  justify-content:space-between;
  padding:10px 0;
  background:var(--split);
}
main dl dd .no{
  width:30px;
  line-height:22px;
  font-weight:bold;
  color:var(--active);
}
main dl dd:last-child{
  background:transparent;
}
 
@media all and (min-width: 400px) {
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
<div class="grid">
  <div class="line horizontal"></div>
  <div class="line horizontal"></div>
  <div class="line horizontal"></div>
  <div class="line vertical"></div>
  <div class="line vertical"></div>
  <div class="line vertical"></div>
  <div class="line vertical"></div>
  <div class="shadow"></div>
  <div class="shadow-top"></div>
</div>
<dt><?=$year?></dt>
<?php foreach ($posts as $no=>$post): ?>
<dd>
  <span class="no"><?=str_pad($no+1,2,"0",STR_PAD_LEFT)?></span>
  <a href="<?=DOMAIN ?>/post/<?=$post["id"] ?>.html"><?=$post["title"] ?></a>
  <span class="date"><?=date("m/d",$post['time'])?></span>
</dd>
<?php endforeach; ?>
</dl>
<?php endforeach; ?>
</main>
<?php include 'footer.php'; ?>