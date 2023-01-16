<div class='well'>
  <?php echo $bajax->form('LogFaturamentoTeleconsult', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LogFaturamentoTeleconsult', 'element_name' => 'utilizacao_servicos_configuracoes'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('data_inclusao_inicio', array('placeholder'=>'Data Inicial', 'label' => 'Inicio', 'class' => 'input-small data', 'title' => 'Inicio')); ?>
        <?php echo $this->BForm->input('data_inclusao_fim', array('placeholder'=>'Data Final', 'label' => 'Fim', 'class' => 'input-small data', 'title' => 'Fim')); ?>
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_pagador', 'Pagador', 'Pagador','LogFaturamentoTeleconsult') ?>
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_utilizador', 'Utilizador', 'Utilizador','LogFaturamentoTeleconsult') ?> 
        <?php if(empty($authUsuario['Usuario']['codigo_seguradora'])): ?>
            <?php echo $this->BForm->input('codigo_seguradora', array('label' => 'Seguradora', 'class' => 'input-medium', 'options' => $seguradoras, 'empty' => 'Todas')); ?>
        <?php endif; ?>
        <?php echo $this->Buonny->input_codigo_corretora($this, 'codigo_corretora', 'Corretora', 'Corretora','LogFaturamentoTeleconsult_corretora', null, true, 'input-medium') ?>
        <?php echo $this->BForm->input('codigo_gestor', array('label' => 'Gestor', 'class' => 'input-medium', 'options' => $gestores, 'empty' => 'Todos')); ?>
    </div>
    <div class="row-fluid inline"> 
        <?php echo $this->Buonny->input_codigo_endereco_regiao($this, $filiais, 'Todas','codigo_endereco_regiao', 'Filiais', 'LogFaturamentoTeleconsult') ?>
        <?php echo $this->Buonny->input_produto_servico($this, $produtos, $servicos)?>
        <?php echo $this->BForm->input('tipo_cobranca', array('label' => 'Cobrado', 'class' => 'input-medium', 'options' => array(
            '1' => 'Sim', 
            '2'  => 'Não',
        ), 'empty' => 'Todos')); ?>
        <?php echo $this->BForm->input('tipo_smonline', array('label' => 'SM Online', 'class' => 'input-medium', 'options' => array(
            '1' => 'Sim', 
            '2'  => 'Não',
        ), 'empty' => 'Todos')); ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-ultilizacao_servicos', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ setup_datepicker(); });', false); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "logs_faturamento/listagem_utilizacao_servicos/" + Math.random());

        setup_datepicker();
        jQuery("#limpar-filtro-ultilizacao_servicos").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:LogFaturamentoTeleconsult/element_name:utilizacao_servicos_configuracoes/" + Math.random())
        });
        
        jQuery(".codigo-cliente-tipo").bind("change",
            function() {
                jQuery.ajax({
                    "url": baseUrl + "/clientes_sub_tipos/combo/" + jQuery(this).val() + "/" + Math.random(),
                    "success": function(data) {                        
                        jQuery(".codigo-cliente-sub-tipo").html(data).val();                        
                    }
                });
            }
        );
    });', false);?>



