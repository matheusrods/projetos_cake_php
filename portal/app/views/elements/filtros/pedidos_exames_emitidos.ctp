<div class='well'>
  <?php echo $bajax->form('PedidoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PedidoExame', 'element_name' => 'pedidos_exames_emitidos'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">

            <?php echo $this->BForm->input('codigo_pedido', array('label' => 'Pedido', 'class' => 'input-mini just-number', 'title' => 'Número do Pedido', 'type' => 'text')); ?>

            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true, 'PedidoExame'); ?>
            <?php echo $this->BForm->input('nome_funcionario', array('label' => 'Nome Funcionário', 'class' => 'input-xlarge', 'type' => 'text')); ?>
            <?php echo $this->BForm->input('data_inclusao', array('label' => 'Emissão', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>      
        </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker(); 
        var div = jQuery(".lista");
        bloquearDiv(div);
        div.load(baseUrl + "pedidos_exames/listagem_pedidos_exames_emitidos/" + Math.random());
		
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PedidoExame/element_name:pedidos_exames_emitidos/" + Math.random())
        });
    });', false);
?>