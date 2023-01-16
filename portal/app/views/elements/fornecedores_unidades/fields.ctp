<div class="row-fluid inline">
    <div class='well'>
        <p><strong>Código Matriz: </strong><?=$dados_fornecedor_matriz['Fornecedor']['codigo']; ?></p>
        <p><strong>Razão Social: </strong><?=$dados_fornecedor_matriz['Fornecedor']['razao_social']; ?></p>
        <p><strong>Nome Fantasia: </strong><?=$dados_fornecedor_matriz['Fornecedor']['nome']; ?></p>
    </div>  
  
  <div class='row-fluid inline parent'>
      <?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor_unidade', 'Código', true, 'FornecedorUnidade', '');?>
  </div>
    <?php echo $this->BForm->hidden('codigo', array('value' => empty($codigo)? '': $codigo )); ?>
    <?php echo $this->BForm->hidden('codigo_fornecedor_matriz', array('value' => $codigo_fornecedor_matriz)); ?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?php echo $html->link('Voltar', array('controller' => 'fornecedores_unidades', 'action' => 'index', $codigo_fornecedor_matriz), array('class' => 'btn')); ?>
</div>  

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        $("input[id=FornecedorUnidadeCodigoFornecedorUnidade]").each(function(){
            carregar($(this));
        });
        $("input[id=FornecedorUnidadeCodigoFornecedorUnidade]").blur(function(){
            carregar($(this));
        });
    });

  function carregar(obj){

    var razao_social_fornecedor_unidade = $("#FornecedorUnidadeCodigoFornecedorUnidadeCodigo");


    if(obj.val()){
          $.ajax({
            url: baseUrl + "fornecedores/buscar/" + obj.val() + "/"+  Math.random(),
            dataType: "json",
            success: function(data){

              if(data == false){
                $(obj).parent().find(".error-message").remove();
                $(obj).parent().addClass("error");
                $(obj).parent().append("<div class=\'help-block error-message\'>Unidade inválida</div>");
              }
              else{
                $(razao_social_fornecedor_unidade).val(data.Fornecedor.nome);
              }
            }
          });
    } 
  }
  ');