<div class="well">
	<?php echo $this->Bajax->form('FichaScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaScorecard', 'element_name' => 'ficha_scorecard_consulta_fichas'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'FichaScorecard'); ?>
		<?php echo $this->BForm->input('codigo_ficha', array('class' => 'input-small', 'type' => 'text', 'label' => 'Código Ficha' )); ?>
		<?php echo $this->Buonny->input_periodo($this, 'FichaScorecard', 'data_inicial', 'data_final', TRUE); ?>
		<?php echo $this->BForm->input('atendente', array('class' => 'input-small', 'label' => 'Atendente' )); ?>
		<?php echo $this->BForm->input('status', array('class' => 'input-medium', 'options' => $status, 'label' => 'Status', 'empty' => 'Selecione' )); ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('cpf_profissional', array('class' => 'input-small formata-cpf', 'type' => 'text', 'label' => 'CPF do Profissional' )); ?>
		<?php echo $this->BForm->input('nome_profissional', array('class' => 'input-medium', 'type' => 'text', 'label' => 'Nome do Profissional' )); ?>
		<?php echo $this->BForm->input('tipo_profissional', array('class' => 'input-medium', 'options' => $tipos_profissionais, 'label' => 'Tipo Profissional', 'empty' => 'Selecione' )); ?>
		<?php echo $this->BForm->input('cpf_proprietario', array('class' => 'input-small formata-cpf', 'type' => 'text', 'label' => 'CPF do Proprietario' )); ?>
		<?php echo $this->BForm->input('nome_proprietario', array('class' => 'input-medium', 'type' => 'text', 'label' => 'Nome do Proprietario' )); ?>
		<?php echo $this->BForm->input('placa', array('class' => 'input-mini placa-veiculo','label' => 'Placa' )); ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id'=>'filtrar')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){     	
    	setup_mascaras();
   		atualizaListaConsultaFichasScorecard();
        $("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaScorecard/element_name:ficha_scorecard_consulta_fichas/" + Math.random())
        });
    });', false);

?>