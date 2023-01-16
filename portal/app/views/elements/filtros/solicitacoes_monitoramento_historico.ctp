<?php $filtrado = (isset($this->data['EstatisticaSm']) && $this->data['EstatisticaSm'] != null); ?>
<div class='well'>
    <h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('EstatisticaSm', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'EstatisticaSm', 'element_name' => 'solicitacoes_monitoramento_historico'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('sm', array('class' => 'input-small', 'placeholder' => 'SM', 'label' => false, 'type' => 'text')) ?>
                <?php echo $this->BForm->input('cod_operador', array('class' => 'input-small', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
                <?php echo $this->BForm->input('operador', array('class' => 'input-mediun', 'placeholder' => 'Nome', 'label' => false, 'type' => 'text')) ?>       
                <?php echo $this->BForm->input('data', array('class' => 'data input-small', 'placeholder' => 'Data', 'label' => false, 'type' => 'text')) ?>
                <div style="float:left">
                    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
                    <?php //echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
                </div>
            </div>
            <div class="row-fluid inline">
                <span class="label label-info">Status</span>
                <div id='status'>
                    <?php echo $this->BForm->input('status', array('label' => false, 'class' => 'checkbox inline input-large', 'options' => array(0 => 'Em Aberto', 1 => 'Em Viagem'), 'multiple' => 'checkbox')); ?>
                </div>
            </div>
            <div class="row-fluid inline">
                <span class="label label-info">Tecnologias</span>
                <span class='pull-right'>
                    <?= $this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("tecnologias")')) ?>
                    <?= $this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("tecnologias")')) ?>
                </span>
                <div id='tecnologias'>
                    <?php echo $this->BForm->input('codequipamento', array('label' => false, 'class' => 'checkbox inline input-large', 'options' => $tecnologias, 'multiple' => 'checkbox')); ?>
                </div>
            </div>
            <div class="row-fluid inline">
                <span class="label label-info">Operações</span>
                <?= $this->BForm->input('tipo_filtro_operacoes', array('label' => array('class' => 'radio inline'), 'div' => false, 'legend' => false, 'options' => array('e', 'ou'), 'type' => 'radio', 'value' => (!isset($this->data['EstatisticaSm']['tipo_filtro_operacoes']) ? '0' : $this->data['EstatisticaSm']['tipo_filtro_operacoes']) )) ?>
                <span class='pull-right'>
                    <?= $this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("operacoes")')) ?>
                    <?= $this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("operacoes")')) ?>
                </span>
                <div id='operacoes'>
                    <?php echo $this->BForm->input('cod_operacao', array('label' => false, 'class' => 'checkbox inline input-large', 'options' => $operacoes, 'multiple' => 'checkbox')); ?>
                </div>
            </div>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php //echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        $.placeholder.shim();
        setup_datepicker();
        atualizaListaSolicitacoesMonitoramentoHistorico();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:EstatisticaSm/element_name:solicitacoes_monitoramento_historico/" + Math.random())
        });      
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>