<?php if(isset($authUsuario['Usuario']['codigo_seguradora']) && $authUsuario['Usuario']['codigo_seguradora']): ?>
    <div class='well'>
        <strong>Seguradora: </strong> <?php echo $this->data['Recebsm']['descricao_seguradora']; ?>
    </div>
<?php endif; ?>
<?php if(isset($authUsuario['Usuario']['codigo_corretora']) && $authUsuario['Usuario']['codigo_corretora']): ?>
    <div class='well'>
        <strong>Corretora: </strong> <?php echo $this->data['Recebsm']['descricao_corretora']; ?>
    </div>
<?php endif; ?>
<?php if(isset($authUsuario['Usuario']['codigo_filial']) && $authUsuario['Usuario']['codigo_filial']): ?>
    <div class='well'>
        <strong>Filial: </strong> <?php echo $this->data['Recebsm']['descricao_filial']; ?>
    </div>
<?php endif; ?>
<div class='well'>
    <?php echo $this->Bajax->form('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Recebsm', 'element_name' => 'estatisticas_sm_analitico'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this,'Recebsm','data_inicial','data_final',false) ?>
            <?php echo $this->Buonny->input_codigo_cliente_base($this,'codigo_pagador','Pagador',false,'Recebsm',true) ?>
            <?php echo $this->Buonny->input_codigo_cliente_base($this,'codigo_embarcador','Embarcador',false,'Recebsm',true) ?>
            <?php echo $this->Buonny->input_codigo_cliente_base($this,'codigo_transportador','Transportador',false,'Recebsm',true) ?>
        </div>
        <div class="row-fluid inline">
            <?php if(empty($authUsuario['Usuario']['codigo_corretora'])): ?>
                <?php echo $this->BForm->input('codigo_seguradora', array('label' => false, 'empty' => 'Todas Seguradoras','options' => $seguradoras)); ?>
            <?php endif; ?>

            <?php if(empty($authUsuario['Usuario']['codigo_seguradora'])): ?>
                <?php echo $this->BForm->input('codigo_corretora', array('label' => false, 'empty' => 'Todas Corretoras','options' => $corretoras)); ?>
            <?php endif; ?>

            <?php if(empty($authUsuario['Usuario']['codigo_filial'])): ?>
                <?php echo $this->BForm->input('codigo_filial', array('label' => false, 'empty' => 'Todas Filiais','options' => $filiais)); ?>
            <?php endif; ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id' => 'buscar')); ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
        </div>
    <?php echo $this->BForm->end() ?>
</div>
<?php 

echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Recebsm/element_name:estatisticas_sm_analitico/" + Math.random())
            jQuery(".lista").empty();
        });
    });', false);
if(isset($this->data['Recebsm']) && count($this->data['Recebsm']) > 2){
    echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){ 
        atualizaListaEstatisticaSmAnalitico();   
    });', false);
}
?>