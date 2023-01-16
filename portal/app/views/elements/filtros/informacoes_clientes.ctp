<div class='well'>
	<?php echo $bajax->form('InformacaoCliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'InformacaoCliente', 'element_name' => 'informacoes_clientes'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
			<?php echo $this->BForm->input('razao_social', array('class' => 'input', 'placeholder' => 'Nome', 'label' => false)) ?>
			<?php echo $this->BForm->input('codigo_area_atuacao', array('options' => $areasAtuacao, 'empty' => 'Selecione uma Área Buonnysat', 'label' => false)) ?>
			<?php echo $this->BForm->input('codigo_sistema_monitoramento', array('options' => $sistemasMonitoramento, 'empty' => 'Selecione um Sistema de Monitoramento', 'label' => false)) ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaInformacoesClientes();
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:InformacaoCliente/element_name:informacoes_clientes/" + Math.random())
        });
    });', false);

?>
