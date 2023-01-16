<div class='well'>
	<?php echo $bajax->form('CronogramaGestaoPpra', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'CronogramaGestaoPpra', 'element_name' => 'gestao_cronograma_ppra'), 'divupdate' => '.form-procurar', 'callback' => 'atualizaListaGestaoCronogramaPpra')) ?>
    	<div class="row-fluid inline">
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'CronogramaGestaoPcmso'); ?>
            </div>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('codigo_cliente_alocacao', array('label' => 'Unidades', 'class' => 'input-xlarge', 'options' => $data_lista_unidades, 'empty' => 'Todos')) ?>
                <?php echo $this->BForm->input('codigo_setor', array('label' => 'Setores', 'class' => 'input-xlarge', 'options' => $data_lista_setores, 'empty' => 'Todos')); ?>
                <?php echo $this->BForm->input('codigo_tipo_acao', array('label' => 'Tipos de Ação', 'class' => 'input-xlarge', 'options' => $data_tipo_acoes, 'empty' => 'Todos')); ?>
                <?php echo $this->BForm->input('status', array( 'label' => 'Status', 'class' => 'input-medium', 'options' => array('NULL' => 'Pendente', 0 => 'Concluido', 1 => 'Cancelado'), 'empty' => 'Todos')); ?>
            </div>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('data_inicial', array( 'label' => 'Periodo Inicial', 'class' => 'data input-small')); ?>
                <?php echo $this->BForm->input('data_final', array( 'label' => 'Periodo final', 'class' => 'data input-small')); ?>
            </div>
    	</div> 
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:CronogramaGestaoPpra/element_name:gestao_cronograma_ppra/" + Math.random());
        });
    });

</script>