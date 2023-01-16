<?php $exibirFiltroCliente = !isset($_SESSION['Auth']['Usuario']['codigo_cliente']) || empty($_SESSION['Auth']['Usuario']['codigo_cliente']); ?>
<div class="well">
    <?php echo $bajax->form('LogFaturamentoTeleconsult', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LogFaturamentoTeleconsult', 'element_name' => 'log_faturamento_teleconsult'), 'divupdate' => '.form-procurar')); ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('numero_liberacao', array('label' => false, 'placeholder' => 'N. Consulta', 'class' => 'input-small', 'maxlength' => 11)); ?>
            <?php if ($exibirFiltroCliente): ?>
                <?php echo $this->BForm->input('codigo_cliente', array('label' => false, 'placeholder' => 'Código Cliente', 'class' => 'input-small', 'maxlength' => 11)); ?>
                <?php echo $this->BForm->input('razao_social', array('label' => false, 'placeholder' => 'Razão Social', 'class' => 'input-xlarge')); ?>
            <?php endif; ?>
            <?php echo $this->BForm->input('codigo_documento', array('label' => false, 'class' => 'cpf', 'placeholder' => 'CPF')); ?>
            <?php echo $this->BForm->input('codigo_produto', array('label' => false, 'placeholder' => 'Produto', 'type' => 'select', 'empty' => 'Selecione o produto', 'options' => array(1 => 'TELECONSULT STANDARD', 2 => 'TELECONSULT PLUS'))); ?>
            <?php echo $this->BForm->input('placa_veiculo', array('label' => false, 'placeholder' => 'Placa', 'class' => 'placa input-small', 'maxlength' => 8)); ?>
            <?php echo $this->BForm->input('data_inclusao_inicio', array('label' => false, 'placeholder' => 'Data Inicial', 'type' => 'text', 'class' => 'data input-small')); ?>
            <?php echo $this->BForm->input('data_inclusao_fim', array('label' => false, 'placeholder' => 'Data Final', 'type' => 'text', 'class' => 'data input-small')); ?>
        </div>
            
        <div class="control-group">
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        </div>
    <?php echo $this->BForm->end(); ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
    <?php if ($this->params['controller'] == 'filtros'): ?>
    atualizaListaConsultaProfissionalSegundaVia();
    <?php endif; ?>
    setup_mascaras();
    setup_datepicker();

    jQuery('#limpar-filtro')
    .unbind('click')
    .bind('click', function(){
        jQuery(".form-procurar").load(baseUrl + 'filtros/limpar/model:LogFaturamentoTeleconsult/element_name:log_faturamento_teleconsult/' + Math.random());
    });
});
</script>