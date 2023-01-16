<div class="well">
  <div id='filtros'>  
    <?php echo $bajax->form('FichaScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaScorecard', 'element_name' => 'fichas_scorecard_excluir_vinculo'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">      
      <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente','FichaScorecard') ?>
      <?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true)) ?> 
      <?php echo $this->BForm->hidden('pagina', array('value'=>1)); ?>
    </div>        
    <div class="row-fluid inline">
      <?php echo $this->BForm->input('codigo_documento',array('label' => 'Código Documento','type' => 'text','class' => 'input-medium cpf', 'placeholder' => 'CPF')) ?>
      <?php echo $this->BForm->input('nome',array('label' => 'Nome','type' => 'text','class' => 'input-medium', 'placeholder' => 'Nome')) ?>
      <?php echo $this->BForm->input('data_inicial', array('label' => 'Data Inicio', 'placeholder' => 'Data Inicial', 'type' => 'text', 'class' => 'data input-small')); ?>
      <?php echo $this->BForm->input('data_final', array('label' => 'Data Fim', 'placeholder' => 'Data Final', 'type' => 'text', 'class' => 'data input-small')); ?>
    </div>    
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>  
</div>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
        $(".btn").click(function(){
	    	var div = jQuery("div.lista");
	    	bloquearDiv(div);
	    	div.load(baseUrl + "fichas_scorecard/listagem_excluir_vinculo/" + Math.random());
		});
		setup_datepicker();
    setup_mascaras();
    setup_codigo_cliente();
    
    $("#limpar-filtro").click(function(){
      bloquearDiv(jQuery(".form-procurar"));
      jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaScorecard/element_name:fichas_scorecard_excluir_vinculo/" + Math.random())
    });

	});', false);?>