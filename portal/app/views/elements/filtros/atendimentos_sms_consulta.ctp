<?php $filtrado = (isset($this->data['AtendimentoSm']) && $this->data['AtendimentoSm'] != null); ?>
<div class='well'>
    <h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('AtendimentoSm', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AtendimentoSm', 'element_name' => 'atendimentos_sms_consulta'), 'divupdate' => '.form-procurar')) ?>
        <div class="pull-right">
            <?php 
                echo $this->BForm->input('codigo_passo_atendimento', array('label' => 'Passo Atendimento', 'class' => 'input-large', 'options' => $passos_atendimentos, 'empty' => 'Todos'));
            ?>
        </div>
        <div class="row-fluid inline">                    
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, 'AtendimentoSm'); ?>
            <?php echo $this->BForm->input('codigo_sm', array('maxlength' => 10, 'label' => false, 'placeholder' => 'SM', 'class' => 'input-small')); ?>
            <?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'label' => false, 'placeholder' => 'Placa')) ?>
            <?php echo $this->Buonny->input_periodo($this, 'AtendimentoSm') ?>
            <div style="float:left">
                <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
                <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
            </div>
        </div>
        <div class="row-fluid inline">
            <span class="label label-info">Status SLA</span>
            <div id='status'>
                <?php echo $this->BForm->input('tipo_sla', array('label' => false, 'class' => 'radio inline input-xlarge', 'options' => array(1 => 'Fora do SLA', 2 => 'Dentro do SLA'), 'empty' => 'Sem SLA')); ?>
            </div>
        </div>
        <div class="row-fluid inline">
            <span class="label label-info">Status</span>
            <span class='pull-right'>
                <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("status")')) ?>
                <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("status")')) ?>
            </span>
            <div id='status'>
                <?php echo $this->BForm->input('status_atendimento', array('label' => false, 'class' => 'checkbox inline input-xlarge','options' => array(01 => 'Em análise', 02 => 'Encaminhado', 03 => 'Finalizado'), 'multiple' => 'checkbox')); ?>
            </div>
        </div>
        <div class="row-fluid inline">
            <span class="label label-info">Tecnologias</span>
            <span class='pull-right'>
                <?= $this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("tecnologias")')) ?>
                <?= $this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("tecnologias")')) ?>
            </span>
            <div id='tecnologias'>
                <?php echo $this->BForm->input('codigo_tecnologia', array('label' => false, 'class' => 'checkbox inline input-large', 'options' => $tecnologias, 'multiple' => 'checkbox')); ?>
            </div>
        </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $this->BForm->end() ?>        
    </div>
</div>
<?php if(!empty($filterValidated)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){
        atualizaListaAtendimentosSmsConsulta();
        jQuery("div#filtros").hide();
    });', false); ?>
<?php else: ?>    
<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){        
        $("div.listagem").remove();
    });', false); ?>    
<?php endif; ?>
<?php echo $javascript->codeBlock('jQuery(document).ready(function(){
        $.placeholder.shim();
        setup_mascaras();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AtendimentoSm/element_name:atendimentos_sms_consulta/" + Math.random())
        });
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
    });');
?>