<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('RemessaBancaria', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RemessaBancaria', 'element_name' => 'remessa_bancaria'), 'divupdate' => '.form-procurar')) ?>
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'RemessaBancaria'); ?>
                <?php echo $this->BForm->input('codigo_banco', array('class' => 'input-xmedium bselect2', 'label' => false, 'options' => $bancos, 'empty' => 'Todos os Bancos')); ?>
                <?php echo $this->BForm->input('codigo_remessa_status', array('class' => 'input-xlarge bselect2', 'label' => false, 'options' => $status, 'empty' => 'Todos os Status')); ?>
                <?php //echo $this->BForm->input('codigo_remessa_retorno', array('class' => 'input-xlarge bselect2', 'label' => false, 'options' => $retorno, 'empty' => 'Todos as Ocorrências do Retorno')); ?>
                <?php echo $this->BForm->input('tipo_arquivo', array('class' => 'input-xlarge bselect2', 'label' => false, 'options' => $tipo_arquivo, 'empty' => 'Todos Tipos de Arquivo')); ?>
            </div>
            <div class="row-fluid inline">
                <span class="label label-info">Período por:</span>
                <div id='agrupamento'>
                    <?php echo $this->BForm->input('tipos_periodo', array('type' => 'radio', 'options' => $tipos_periodo, 'default' => 'I', 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
                </div>
                <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
                <?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
            </div>           
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-atestados', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end();?>
        <div class="carregando" style="display: none;">
            <img src="/portal/img/loading.gif" style="padding: 10px;">
        </div>
    </div>
</div>
<div class='actionbar-right'>
    <div class='actionbar-right'>
    <?php echo $html->link('Importar Remessa',array('controller'=>'remessa_bancaria','action'=>'importar_remessa') , array('class' => 'btn btn-warning')); ?>
    <?php echo $html->link('Importar Retorno',array('controller'=>'remessa_bancaria','action'=>'importar_retorno') , array('class' => 'btn btn-warning')); ?>    
    <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array('controller'=>'remessa_bancaria','action'=>'exportar_dados'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
    
    </div>
</div>
<?php 
echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
        setup_mascaras(); setup_time(); setup_datepicker();
        
        atualizaLista();        
        function atualizaLista() {
            var div = jQuery('div.lista');
            bloquearDiv(div);
            div.load(baseUrl + 'remessa_bancaria/listagem/' + Math.random());
        }
        jQuery('#limpar-filtro-atestados').click(function(){
            bloquearDiv(jQuery('.form-procurar'));
            jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:RemessaBancaria/element_name:remessa_bancaria/' + Math.random())
        });
    });
"); ?>