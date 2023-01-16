<?php $filtrado = (isset($this->data['AtendimentoSm']) && $this->data['AtendimentoSm'] != null); ?>
<div class='well'>
    <h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('AtendimentoSm', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AtendimentoSm', 'element_name' => 'atendimentos_sms'), 'divupdate' => '.form-procurar')) ?>
        <div class="pull-right">
            <?php 
                if(isset($admin)){
                    echo $this->BForm->input('codigo_passo_atendimento', array('label' => 'Passo Atendimento', 'class' => 'input-large', 'options' => $passos_atendimentos));
                } elseif(isset($pronta_resposta)) {
                    echo $this->BForm->hidden('codigo_passo_atendimento', array('value' => $pronta_resposta));
                } elseif(isset($buonnysat)){
                    echo $this->BForm->hidden('codigo_passo_atendimento', array('value' => $buonnysat));
                }
            ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', FALSE,'AtendimentoSm'); ?>
            <?php echo $this->BForm->input('codigo_sm', array('maxlength' => 10, 'label' => false, 'placeholder' => 'SM', 'class' => 'input-small')); ?>
            <?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'label' => false, 'placeholder' => 'Placa')) ?>
            <div style="float:left">
                <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
                <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
            </div>
        </div>
        <div class="row-fluid inline">
            <span class="label label-info">Status</span>
            <span class='pull-right'>
                <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("status")')) ?>
                <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("status")')) ?>
            </span>
            <div id='status'>
                <?php echo $this->BForm->input('status_atendimento', array('label' => false, 'class' => 'checkbox inline input-xlarge','options' => array(01 => 'Em análise', 02 => 'Encaminhado'), 'multiple' => 'checkbox')); ?>
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
<?php    
    echo $javascript->codeBlock('jQuery(document).ready(function(){
        $.placeholder.shim();
        atualizaListaAtendimentosSms();
		setup_mascaras();        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AtendimentoSm/element_name:atendimentos_sms/" + Math.random())
        });
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
    });');
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>