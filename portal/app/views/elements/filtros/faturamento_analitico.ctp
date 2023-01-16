<div class='well'>
	<?php echo $this->Bajax->form('DetalheItemPedido', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'DetalheItemPedido', 'element_name' => 'faturamento_analitico'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('mes_referencia', array('class' => 'input-medium', 'label' => 'Mês', 'options' => $meses, 'default' => $mes_atual)); ?>
		<?php echo $this->BForm->input('ano_referencia', array('class' => 'input-small', 'label' => 'Ano', 'options' => $anos, 'default' => $ano_atual)); ?>
		<?php echo $this->BForm->input('regiao', array('class' => 'input-large', 'label' => 'Filial', 'options' => $regioes, 'empty' => 'Selecione')); ?>
		<?php echo $this->BForm->input('tipo_faturamento', array('class' => 'input-small', 'label' => 'Tipo Faturamento', 'options' => array(1 => 'Total', 2 => 'Parcial'), 'empty' => 'Selecione')); ?>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'DetalheItemPedido'); ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('gestor', array('class' => 'input-xlarge', 'label' => 'Gestor', 'options' => $gestores, 'empty' => 'Selecione')); ?>
		<?php echo $this->BForm->input('corretora', array('class' => 'input-xlarge', 'label' => 'Corretora', 'options' => $corretoras, 'empty' => 'Selecione')); ?>
		<?php echo $this->BForm->input('seguradora', array('class' => 'input-xlarge', 'label' => 'Seguradora', 'options' => $seguradoras, 'empty' => 'Selecione')); ?>
		
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaDetalhesItensPedidos();

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:DetalheItemPedido/element_name:faturamento_analitico/" + Math.random())
        });
    });', false);
?>