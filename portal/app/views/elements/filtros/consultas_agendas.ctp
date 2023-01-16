<div class='well'>
  <?php echo $bajax->form('AgendamentoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AgendamentoExame', 'element_name' => 'consultas_agendas'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php 
            if($this->Buonny->seUsuarioForMulticliente()) { 
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'AgendamentoExame'); 
            }else{
                echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'AgendamentoExame');
            }

           	echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Fornecedor','AgendamentoExame');?>
      
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo_pedido_exame', array('label' => 'Código do Pedido', 'class' => 'input-medium just-number', 'type' => 'text')); ?>
        <?php echo $this->BForm->input('tipos_status', array('options' => $tipos_status, 'empty' => 'Todos', 'class' => 'input-medium', 'label' => 'Status')); ?>
        <?php echo $this->BForm->input('tipos_agendamento', array('options' => $tipos_agendamento, 'empty' => 'Todos', 'class' => 'input-medium',  'label' => 'Tipos de Agendamento')); ?>
        <?php echo $this->BForm->input('nome_funcionario', array('label' => 'Nome Funcionário', 'class' => 'input-medium', 'type' => 'text')); ?>
        <?php echo $this->BForm->input('cpf', array('label' => 'CPF Funcionário', 'class' => 'input-medium cpf', 'type' => 'text')); ?>
        <?php echo $this->BForm->input('com_anexo_aso', array('options' => $com_anexo_aso, 'empty' => 'Todos', 'class' => 'input-medium',  'label' => 'Com Anexo ASO')); ?>
        <?php echo $this->BForm->input('com_anexo_ficha_clinica', array('options' => $com_anexo_ficha_clinica, 'empty' => 'Todos', 'class' => 'input-medium',  'label' => 'Com Anexo Ficha Clinica')); ?>
    </div>
    <div class="row-fluid inline">
        <span class="label label-info">Período por:</span>
        <div id='agrupamento'>
            <?php echo $this->BForm->input('tipo_periodo', array('type' => 'radio', 'options' => $tipos_periodo, 'default' => 6, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
        </div>
        <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
        <?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>        
    </div>

    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker();
        setup_mascaras();
		
        var div = jQuery(".lista");
        bloquearDiv(div);
        div.load(baseUrl + "consultas_agendas/listagem/" + Math.random());
		
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AgendamentoExame/element_name:consultas_agendas/" + Math.random())
        });
    });', false);
?>