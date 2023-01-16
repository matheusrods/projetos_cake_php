<div class="well">
	<?php echo $this->Bajax->form('FichaScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaScorecard', 'element_name' => 'ficha_scorecard_log_consulta'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('nro_consulta', array('class' => 'input-small', 'type' => 'text', 'label' => 'N° Consulta' )); ?>
		<?php echo $this->BForm->input('codigo_documento', array('class' => 'input-small formata-cpf', 'type' => 'text', 'label' => 'CPF' )); ?>
		<?php echo $this->BForm->input('placa', array('class' => 'input-small', 'type' => 'text', 'label' => 'Placa' )); ?>
		<?php echo $this->BForm->input('usuario', array('class' => 'input-small', 'type' => 'text', 'label' => 'Usuário' )); ?>		
	</div>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'FichaScorecard'); ?>
		<?php echo $this->Buonny->input_periodo($this, 'FichaScorecard', 'data_inicial', 'data_final', TRUE); ?>
		<?php echo $this->BForm->input('codigo_tipo_profissional',array('label' => 'Categoria', 'empty' => 'Categoria','options' => $tipos_profissional,'class'=>'input-medium' ));?>
		<?php echo $this->BForm->input('tipo_faturamento',array('label' => 'Tipo Faturamento', 'empty' => 'Tipo Faturamento','options' => $tipos_faturamento,'class'=>'input-medium' ));?>
	</div>
	<div class="row-fluid inline">
	      <?php echo $this->BForm->input('codigo_tipo_operacao',array('label' => false,'label'=>'Tipo de Operação', 'empty' => 'Selecione um Tipo de Operação','options' => $tipos_operacoes,'class'=>'input-xxlarge' ));?>   
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id'=>'filtrar')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end();?>
</div>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){     	
    	setup_codigo_cliente();
    	setup_mascaras();
		var div = jQuery("div.lista");
		bloquearDiv(div);
		div.load(baseUrl + "fichas_scorecard/listagem_log_consultas/" + Math.random());
        $("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaScorecard/element_name:ficha_scorecard_log_consulta/" + Math.random())
        });
    });', false);

?>