<footer class="footer">

</footer>
 
<div class="up"></div>
</body>
</html>

<script>
var d=document;
var body=d.querySelector("body");
var up=d.querySelector(".up");
// var search=d.querySelector(".search-toggle");
// var searchBoxClose=d.querySelector(".search-box span");
// var searchBoxInput=d.querySelector(".search-box input");
var tap='ontouchstart' in d?'touchstart':'click';

window.addEventListener("scroll",function(e){
    var t=d.documentElement.scrollTop||d.body.scrollTop;

    if(t>window.innerHeight/2){
        up.classList.add('fade');
    }else{
        up.classList.remove('fade');
    }
});

up.addEventListener(tap,function(){
    !(function go(){
        var t=d.documentElement.scrollTop||d.body.scrollTop;
        if(t>0){
            window.requestAnimationFrame(go);
            window.scrollTo(0,t-t/2);
        }
    })();
},false);

// search.addEventListener(tap,function(){
//     body.classList.add('blur-search');
// },false);

// searchBoxClose.addEventListener(tap,function(){
//     setTimeout(function(){
//         body.classList.remove('blur-search');
//     },100);
// },false);

// searchBoxInput.addEventListener("keyup",function(e){
//     if(e.keyCode==13 && this.value.trim()!=""){
//         location.href="<?=DOMAIN ?>/search/"+encodeURIComponent(this.value)+".html";
//     }
// },false);
</script>
<?php 
$result = ob_get_contents();
ob_end_clean();
if($theme_file_name=='post'){
    file::create(ROOT."post/".$id.".html",$result);
}elseif($theme_file_name=='tag'){
    if($page_current>1){
        file::create(ROOT."tag/".$id."-".$page.".html",$result);
    }else{
        file::create(ROOT."tag/".$id.".html",$result);
        file::create(ROOT."tag/".$id."-1.html",$result);
    }
}elseif($theme_file_name=='index'){
    file::create(ROOT."/index.html",$result);
}
echo $result;
?>