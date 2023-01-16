<div class='well'>
  <?php echo $bajax->form('AnexoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AnexoExame', 'element_name' => 'moderacao_anexos'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php if(empty($codigo_cliente)) { ?>
            <?php //echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'AgendamentoExame'); ?>
       	    <?php echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'AnexoExame'); ?>
        <?php } ?>

        <?php 
            if(empty($codigo_fornecedor)) {     
       	        echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Fornecedor','AnexoExame');
            } else {
                echo $this->BForm->hidden('codigo_fornecedor');
            } 
        ?>
        
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo_pedido_exame', array('label' => 'Código do Pedido', 'class' => 'input-medium just-number', 'type' => 'text')); ?>
        <?php echo $this->BForm->input('tipos_status', array('options' => $tipos_status, 'empty' => 'Todos', 'class' => 'input-medium', 'label' => 'Status')); ?>
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
        div.load(baseUrl + "consultas_agendas/moderacao_anexos_listagem/" + Math.random());
		
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AnexoExame/element_name:moderacao_anexos/" + Math.random())
        });
    });', false);
?>