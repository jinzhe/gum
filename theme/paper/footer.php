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
window.addEventListener("load",function(){
    document.querySelector(".nav-toggle").addEventListener("click",function(){
        document.querySelector("body").classList.toggle('blur');
    },false);
});
</script>
