
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>.: Pesquisa :.</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>

<style type="text/css">
  
  #content-dtr{
    width: 779px;
    margin: 0 auto;   
    background: #FFF;    
    margin-top: 25px;   
    border: 1px solid black;    
  }
  .content{ font-family: Arial, Verdana; font-size: 14px;  }  
  .forte{ font-weight:bold; }    
  #descricao{ margin-bottom: 30px; }
  input[type="text"]{ background: #FFFFFF; border: 1px solid #AAA; padding: 5px; width: 300px;}
  input[type="text"]:hover{ background: #EFEFFF; }
  input[type="submit"]{ background: #FFFFFF; border: 1px solid #AAA; padding: 5px; cursor: pointer; }
  input[type="submit"]:hover{ background: #EFEFFF; }
  .ita{ font-style: italic; color: #000; margin-top: 30px; margin-bottom: 30px; }  
  #mensagem { display: none; }
  #mensagem h1{ font-size: 16px; color: #0101C1; width: 740px; margin: 0 auto; }
  #frmQuestionario, #descricao{ font-size:14px; width: 740px; margin: 0 auto; }
  #imagens{ width: 480px; height: 360px; margin: 0 auto; text-align: center; }
  #imagem-1{ margin-right: 60px; float: left; }
  #imagem-1 a, span{ font-size: 11px; }
  #imagem-2 a, span{ font-size: 11px; }
</style>

</head>

<body>

<div id="container"><div id="content-dtr">


<div class="content">

<div id="topo">
  <img src="http://www.buonny.com.br/images/topo_renovacao2004.jpg" border="0" />  
<div>

<div id="descricao">
  <p>
    RENOVAÇÃO 2004 PERGUNTA é uma forma de conhecer a opinião de seus clientes e potenciais clientes.    
  </p>

  <p>
    Você pode contribuir com nossa pesquisa e concorrer a 02 (dois) livros do palestrante e administrador de empresas MARCELO BUONAVOGLIA;
  </p>
</div>
<div id="mensagem">
  <h1>Questionário preenchido com sucesso! Obrigado.<h1>
</div>
<div id="questionario">
<form method="POST" action="" name="frmQuestionario" id="frmQuestionario">
  <table width="100%" border="0" cellspacing="0" cellpadding="4">

<div id="imagens">
  <div id="imagem-1">
    <img src="http://www.buonny.com.br/images/lideranca_nas_organizacoes.jpg" border="0" />
    <h5>LIDERANÇA NAS ORGANIZAÇÕES</h5>
    <span>clique abaixo para maiores detalhes</span><br />
    <a href="http://www.lifeeditora.com.br/crbst_82.html" target="_blank">www.lifeeditora.com.br</a>
  </div>
  <div id="imagem-2">
    <img src="http://www.buonny.com.br/images/qualidade_no_atendimento.jpg" border="0" />  
    <h5>QUALIDADE NO ATENDIMENTO</h5>
    <span>clique abaixo para maiores detalhes</span><br />
    <a href="http://www.lifeeditora.com.br/crbst_101.html" target="_blank">www.lifeeditora.com.br</a>
  </div>  
</div>

<p style="clear:both; font-weight:none;">
Participe e boa sorte!!!
<br />
Basta responder (SIM ou NÂO) à pergunta abaixo, clicando na opção que escolher:
</p>

  <p class="forte">1. TREINAMENTOS CONTRIBUEM COM O SUCESSO PROFISSIONAL?</p>
  <p>
    <label>
      <input type="radio" name="pergunta_1" id="pergunta_1" value="SIM" />
    </label>
    SIM<br />
    <label>
      <input type="radio" name="pergunta_1" id="pergunta_1" value="NAO" />
      </label>
    NÃO<br />
  </p> 

  <p style="font-weight:none;">
    No total serão sorteados 30 (trinta) livros entre os participantes;
    <br />
    O sorteio será em 15/02/2013, sendo que em 21/02/2013 receberá o resultado em seu email.
  </p>

  <p class="forte">
   <p class="ita">
    <span>Nome completo (opcional)</span>
    <br />
    <label>
      <input type="text" name="nome" id="nome" value="" />
    </label>    
  </p>

   <p class="forte" style="margin-top:10px;">
   <p class="ita">
    <span>Email (opcional)</span>
    <br />
    <label>
      <input type="text" name="email" id="email" value="" />
    </label>    
  </p>

  <p class="forte">
   <p>        
    <label>
      <input type="submit" name="btnEnviar" id="btnEnviar" value="ENVIAR" />
    </label>    
  </p>

</form>
</div>


</div>
  
</div>
</div>

</div>

</body>
</html>

<script type="text/javascript">
  $(document).ready(function(){

  $("input[name='pergunta_1']").click(function(){
    if( $(this).val() == 'SIM'){
      $("input[name='pergunta_1b']").attr('disabled',true);
      $("input[name='pergunta_1b']").removeAttr("checked");
      $("input[name='pergunta_1a']").attr('disabled',false);
    } else {
      $("input[name='pergunta_1a']").attr('disabled',true);     
      $("input[name='pergunta_1a']").removeAttr("checked");
      $("input[name='pergunta_1b']").attr('disabled',false);
    }
  })

  $('#btnEnviar').click(function(event){

    event.preventDefault();
    
    
    var v1   = $("input[name='pergunta_1']:checked").val();      
    var nome = $('#nome').val();
    var email = $('#email').val();

    if( v1 == undefined ){
      alert('Pergunta 1 em branco!');
      return false;
    }     

    $.ajax({

        type: 'POST',        
        url: '/portal/api/questionario_renovacao2004_incluir',        
        dataType : 'TEXT',
        data:{
          'pergunta'  : v1,          
          'nome'       : nome,
          'email'      : email 
        },                
        success : function(data) {   
        if( data == '1' ){
          $('#questionario').slideToggle('slow');
          $('#mensagem').fadeIn("slow");
          setInterval(
            function(){
              window.location = 'http://www.renovacao2004.com.br';
            },
            5000
          )
        } else {
          alert('Oooops! Ocorreu algum problema, tente novamente.');
        }
      },
      error : function(data){
        alert('Oooops! Ocorreu algum problema, tente novamente.');
      }
    })

  })
  
})
</script>