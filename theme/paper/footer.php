<footer class="footer">
<a href="https://github.com/jinzhe/gum" target="_blank" title="Powered by GUM">&copy;</a> <?=date("Y")?>
&nbsp;
<?=$config["copyright"]?>
&nbsp;
<a href="http://www.miitbeian.gov.cn" target="_blank"><?=$config["icp"]?></a>
</footer>
<div class="progress"></div>
</body>
</html>

<script>
var body=document.querySelector("body");
var nav=document.querySelector(".nav-toggle");
var search=document.querySelector(".search-toggle");
var searchBoxClose=document.querySelector(".search-box span");
var searchBoxInput=document.querySelector(".search-box input");
var tap='ontouchstart' in document?'touchstart':'click';

nav.addEventListener(tap,function(){
    body.classList.toggle('blur-nav');
},false);
search.addEventListener(tap,function(){
    body.classList.add('blur-search');
},false);
searchBoxClose.addEventListener(tap,function(){
    setTimeout(function(){
        body.classList.remove('blur-search');
    },100);
},false);
searchBoxInput.addEventListener("keyup",function(e){
    if(e.keyCode==13 && this.value.trim()!=""){
        location.href="<?=DOMAIN?>/search/"+encodeURIComponent(this.value)+".html";
    }
},false);
</script>
