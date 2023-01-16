	<div class='well'>
	<?php echo $this->Bajax->form('LogIntegracao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LogIntegracao', 'element_name' => 'logs_integracoes_consultar'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
			<?= $this->Buonny->input_codigo_cliente($this); ?>
			<?= $this->Buonny->input_periodo($this, 'LogIntegracao') ?>
			<?= $this->BForm->input('hora_inicial', array('label' => false, 'class' => 'hora input-mini')) ?>
			<?= $this->BForm->input('hora_final', array('label' => false, 'class' => 'hora input-mini')) ?>			
			<?php
				if(empty($codigo_cliente))
					echo $this->BForm->input('sistema_origem', array('class' => 'input-large', 'label'=>false, 'options'=>$sistema_origem,'empty'=>'Selecione a origem')); 
			?>
		</div>
		<div class="row-fluid inline">
			<?= $this->BForm->input('arquivo', array('class' => 'input-medium', 'placeholder' => 'Arquivo', 'label' => false)) ?>
			<?php echo $this->BForm->input('status', array('class' => 'input-large', 'label'=>false, 'options'=>array(0=>'INTEGRADA', 1=>'NÃO INTEGRADA'),'empty'=>'Selecione um status')); ?>
			<?= $this->BForm->input('descricao', array('class' => 'input-large', 'placeholder' => 'Descricao', 'label' => false)) ?>			
			<?= $this->BForm->input('tipo_operacao', array('class' => 'input-small', 'placeholder' => 'Tipo Operacao', 'label' => false)) ?>			
			<?= $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'placeholder' => 'Placa', 'label' => false)) ?>			
		</div>
		<div class="row-fluid inline">
			<?= $this->BForm->input('numero_pedido', array('class' => 'input-medium', 'placeholder' => 'Pedido/Loadplan', 'label' => false)) ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	$(".hora").mask("99:99");    	
    	setup_mascaras();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:LogIntegracao/element_name:logs_integracoes_consultar/" + Math.random())
        });
    });', false);

	if(!empty($_POST)){
		echo $this->Javascript->codeBlock('
	    jQuery(document).ready(function(){

	        atualizaListaLogsIntegracoes();        	
	
	    });', false);
	}

?>