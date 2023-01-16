<?php $filtrado = (isset($this->data['MSmitinerario']['codigo_cliente']) && $this->data['MSmitinerario']['codigo_cliente'] != null); ?>
<div class='well'>
	<h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id="filtros">
		<?php echo $this->Bajax->form('MSmitinerario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MSmitinerario', 'element_name' => 'itinerarios_sms_por_cliente'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_periodo($this, 'MSmitinerario') ?>
			<?php echo $this->Buonny->input_cliente_tipo($this, 0, $clientes_tipos) ?>
	        <?php echo $this->BForm->input('status', array('class' => 'input-large', 'options' => $status, 'label' => false, 'empty' => 'Todos Status')); ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('loadplan', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Loadplan')); ?>
			<?php echo $this->BForm->input('nf', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Nota Fiscal')); ?>
			<?php echo $this->BForm->input('sm', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Nº SM')); ?>
			<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'label' => false, 'placeholder' => 'Placa')); ?>
			<?php echo $this->BForm->input('placa_carreta', array('class' => 'input-small placa-veiculo', 'label' => false, 'placeholder' => 'Placa Carreta')); ?>
			<?php echo $this->BForm->input('itens_por_pagina', array('class' => 'input-mini', 'label' => false, 'placeholder' => 'Itens Por Página', 'title' => 'Itens Por Página')); ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end();?>
	</div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	$.placeholder.shim();
        setup_mascaras();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MSmitinerario/element_name:itinerarios_sms_por_cliente/" + Math.random())
        });
        atualizaListaItinerariosSm();
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>