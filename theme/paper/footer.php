<footer class="footer">
<a href="https://github.com/jinzhe/gum" target="_blank" title="Powered by GUM">&copy;</a> <?=date("Y")?>
&nbsp;
<?=COPYRIGHT?>
&nbsp;
<a href="http://www.miitbeian.gov.cn" target="_blank"><?=ICP?></a>
&nbsp;Theme by <a href="https://github.com/nanxiaobei/hugo-paper" target="_blank">Paper</a>
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
nav.addEventListener("click",function(){
    body.classList.toggle('blur-nav');
},false);
search.addEventListener("click",function(){
    body.classList.add('blur-search');
},false);
searchBoxClose.addEventListener("click",function(){
    body.classList.remove('blur-search');
},false);
searchBoxInput.addEventListener("keyup",function(e){
    if(e.keyCode==13 && this.value.trim()!=""){
        location.href="<?=DOMAIN?>/search/"+encodeURIComponent(this.value)+".html";
    }
},false);
</script>
