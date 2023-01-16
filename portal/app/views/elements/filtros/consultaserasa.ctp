<style type="text/css">
/*<![CDATA[*/
#one
   {
    position:absolute;
    left:0%;
    top:0%;
   margin:122px 0 0 85px;
   }
object
   {
    width: 800px; 
    height:800px;  
    border:solid 0px #000000;
   }
 /*//]]>*/
</style>

<script type="text/javascript">
//<![CDATA[
// written by: Coothead
function updateObjectIframe(which){
    document.getElementById('one').innerHTML = '<'+'object id="foo" name="foo" type="text/html" data="'+which.href+'"><\/object>';
}

//]]>
</script>

</head>
<body>

<div id="one">
<object id="foo" name="foo" type="text/html" data="http://informacoes.buonny.com.br/bcb/autenticacao/login"></object>
</div>
<div>
<a href="http://informacoes.buonny.com.br/bcb/autenticacao/login" onclick="updateObjectIframe(this); return false;"></a>
</div>

</body>
</html>
