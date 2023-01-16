<?php $filtrado = (isset($this->data['TransitTime']['codigo_cliente']) && $this->data['TransitTime']['codigo_cliente'] != null); ?>
<div class='well'>
	<h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id='filtros'>
		<?php echo $this->Bajax->form('TransitTime', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TransitTime', 'element_name' => 'transit_time'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_cliente', 'Cliente', false, 'TransitTime') ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('loadplan', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Loadplan')); ?>
			<?php echo $this->BForm->input('nf', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Nota Fiscal')); ?>
			<?php echo $this->BForm->input('viag_codigo_sm', array('class' => 'input-small just-number', 'label' => false, 'placeholder' => 'Nº SM')); ?>
			<?php echo $this->BForm->input('veic_placa', array('class' => 'input-small placa-veiculo', 'label' => false, 'placeholder' => 'Placa')); ?>
			<?php echo $this->BForm->input('veic_placa_carreta', array('class' => 'input-small placa-veiculo', 'label' => false, 'placeholder' => 'Placa Carreta')); ?>
			<?php echo $this->BForm->input('status_posicao', array('class' => 'input-medium', 'label' => false, 'options' => $status_posicao, 'empty' => 'Status Posição')); ?>
			<?php echo $this->BForm->input('calculo', array('class' => 'input-medium', 'label' => false, 'options' => $calculo)); ?>
			<?php echo $this->BForm->input('desconsiderar_alvos', array('class' => 'input-medium numeric just-number', 'label' => 'Desconsiderar tempo alvo', 	'type' => 'checkbox')); ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('quantidade_sms', array('class' => 'input-small numeric just-number', 'label' => 'Qtd.Viagens')); ?>
			<?php echo $this->BForm->input('intervalo', array('class' => 'input-medium numeric just-number', 'label' => 'Segundos para Atualização')); ?>
			<?php echo $this->BForm->input('tempo_atraso', array('class' => 'input-medium numeric just-number', 'label' => 'Qtd.Minutos Muito Atrasado')); ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end();?>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	$.placeholder.shim();
        setup_datepicker();
        setup_mascaras();
        setupLimparTransitTime()
		atualizaListaTransitTime();
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>