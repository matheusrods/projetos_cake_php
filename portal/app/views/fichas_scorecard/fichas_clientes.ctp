<div class="form-procurar">
    <div class="well">
      <?php echo $this->BForm->create('FichaScorecard', array('autocomplete' => 'off')) ?>
        <div class="row-fluid inline">
          <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'FichaScorecard') ?>
          <?php echo $this->BForm->input("FichaScorecard.codigo_produto", array('options'=>$produtos, 'empty'=>'Produto', 'label' => false, 'class' => 'input-large')) ?>
          <?php echo $this->BForm->input('codigo_documento',array('label' => false,'type' => 'text','class' => 'input-medium', 'placeholder' => 'CPF')) ?>
        </div>        
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
      <?php echo $this->BForm->end() ?>
    </div>
</div>
<div class="well">
    <?php if($encontrou): ?>
        <legend>Resultados</legend>
        <strong>Status: </strong><?php echo $status; ?> <br/>
        <strong>Observação: </strong><?php echo $observacao; ?> <br/>
    <?php elseif(isset($this->data)): ?>
        <span>Nenhuma ficha encontrada.</span>
    <?php else: ?>
        <span>Forneça os parâmetros para consulta.</span>
    <?php endif; ?>
</div>

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
	    $("#FichaScorecardCodigoCliente").blur(function(){
		    $("#FichaScorecardCodigoProduto").load(baseUrl + "clientes_produtos/lista_produtos_tlcs/" + $(this).val() + "/" + Math.random());
	    });
	});', false);
?>