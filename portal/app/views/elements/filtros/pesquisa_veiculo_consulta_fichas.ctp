<div class="well">
	<?php echo $this->Bajax->form('PesquisaVeiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PesquisaVeiculo', 'element_name' => 'pesquisa_veiculo_consulta_fichas'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'PesquisaVeiculo'); ?>	
		<?php echo $this->BForm->input('placa', array('class' => 'input-mini placa-veiculo','label' => 'Placa' )); ?>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_embarcador', 'Embarcador', 'Cliente', 'PesquisaVeiculo') ?>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_transportador', 'Transportador', 'Cliente', 'PesquisaVeiculo') ?>
		<?php echo $this->BForm->input('codigo_status', array('class' => 'input-medium', 'options' => $status, 'label' => 'Status', 'empty' => 'Selecione' )); ?>				
		<?php echo $this->Buonny->input_periodo($this, 'PesquisaVeiculo','data_inicial', 'data_final', true) ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id'=>'filtrar')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end();?>
</div>
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php $this->addScript($this->Buonny->link_js('fichas_scorecard.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){     	
    	setup_mascaras();
    	setup_codigo_cliente();
   		atualizaListaConsultaPesquisaVeiculo();   		
        $("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PesquisaVeiculo/element_name:pesquisa_veiculo_consulta_fichas/" + Math.random())
        });
    });', false);

?>