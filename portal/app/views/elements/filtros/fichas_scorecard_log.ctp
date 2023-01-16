<div class="well">
  <?php echo $bajax->form('FichaScorecardLog', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaScorecardLog', 'element_name' => 'fichas_scorecard_log'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'FichaScorecardLog') ?>
      <?php echo $this->BForm->input('data_inclusao_inicio', array('label' => false, 'placeholder' => 'Data Inicial', 'type' => 'text', 'class' => 'data input-small')); ?>
      <?php echo $this->BForm->input('data_inclusao_fim', array('label' => false, 'placeholder' => 'Data Final', 'type' => 'text', 'class' => 'data input-small')); ?>      
      <?php echo $this->BForm->input('codigo_profissional_tipo',array('label' => false, 'empty' => 'Selecione uma Categoria','options' => $tipos_profissional,'class'=>'input-large' ));?>
      <?php echo $this->BForm->input('codigo_status',array('label' => false, 'empty' => 'Selecione um Status','options' => $statuses,'class'=>'input-large' ));?>      
      <?php echo $this->BForm->input('profissional_log_codigo_documento',array('label' => false,'type' => 'text','class' => 'input-medium', 'placeholder' => 'CPF Profissional')) ?>
      <?php echo $this->BForm->input('usuario_apelido',array('label' => false, 'class'=>'input-medium', 'placeholder' => 'Responsável' ));?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id'=>'filtrar')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
      setup_codigo_cliente(); 
	    setup_datepicker();
      var div = jQuery("div.lista");
      bloquearDiv(div);
      div.load(baseUrl + "/fichas_scorecard/listar_fichas_log/" + Math.random());
      $("#limpar-filtro").click(function(){
        $(".form-procurar :input").not(":button, :submit, :reset, :hidden").val("");
        $(".form-procurar form").submit();
		  });
	});', false);
?>