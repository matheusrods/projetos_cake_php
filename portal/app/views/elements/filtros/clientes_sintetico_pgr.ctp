<div class='well'>
    <?php echo $bajax->form('ClientesPGR', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClientesPGR', 'element_name' => 'clientes_sintetico_pgr'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php if(empty($authUsuario['Usuario']['codigo_seguradora'])): ?>
        <?php echo $this->BForm->input('codigo_seguradora', array('label' => 'Seguradora', 'class' => 'input-medium', 'options' => $seguradoras, 'empty' => 'Todas Seguradoras')); ?>
        <?php endif; ?>

        <?php if(empty($authUsuario['Usuario']['codigo_corretora'])): ?>
        <?php echo $this->BForm->input('codigo_corretora', array('label' => 'Corretora', 'class' => 'input-medium', 'options' => $corretoras, 'empty' => 'Todas Corretoras')); ?>
        <?php endif; ?>
        <?php echo $this->BForm->input('codigo_gestor', array('label' => 'Gestor Comercial', 'class' => 'input-medium', 'options' => $gestores, 'empty' => 'Todos Gestores')); ?>
        <?php echo $this->Buonny->input_codigo_endereco_regiao($this, $filiais, 'Todas Filiais','codigo_endereco_regiao', 'Filial', 'ClientesPGR') ?>
        <?php echo $this->BForm->input('codigo_gestor_npe', array('label' => 'Gestor NPE', 'class' => 'input-medium', 'options' => $gestores_npe, 'empty' => 'Todos Gestores NPE')); ?>
        <?php echo $this->BForm->input('vppj_validade_apolice', array('label' => 'Apólice Válida', 'class' => 'input-medium', 'options' => array(1=>'Sim', 2=>'Não'), 'empty' => 'Apolice Válida')); ?>
        <?php echo $this->BForm->input('vppj_verificar_regra', array('label' => 'Verifica regra de aceite', 'class' => 'input-medium', 'options' => array(1=>'Sim', 2=>'Não'), 'empty' => 'Verifica regra de aceite')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo_status_produto_buonnysat', array('label' => 'Status Produto Buonnysat', 'class' => 'input-medium', 'options' => $motivos, 'empty' => 'Status Produto')); ?>
    </div>
    <div class="row-fluid inline">
        <span class="label label-info">Agrupar por:</span>
        <div id='agrupamento'>
            <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'legend' => false, 'label' => array('class' => 'radio inline','style'=>'width:105px'))) ?>
        </div>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn' )) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-clientes', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "clientes/listagem_clientes_pgr_sintetico/" + Math.random());
        setup_datepicker();
        jQuery("#limpar-filtro-clientes").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClientesPGR/element_name:clientes_sintetico_pgr/" + Math.random())
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