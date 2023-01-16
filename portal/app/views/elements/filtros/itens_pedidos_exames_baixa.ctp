
<div class='well'>
  <?php echo $bajax->form('PedidoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PedidoExame', 'element_name' => 'itens_pedidos_exames_baixa'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
       	<?php echo $this->BForm->input('codigo_pedido', array('label' => 'Número do Pedido', 'class' => 'input-medium just-number', 'title' => 'Número do Pedido', 'type' => 'text', 'style' => 'width: 120px')); ?>
      <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'PedidoExame', isset($codigo_cliente) ? $codigo_cliente : ''); ?>
       	<?php echo $this->BForm->input('nome_funcionario', array('label' => 'Nome Funcionário', 'class' => 'input-xlarge', 'type' => 'text')); ?>
       	<?php echo $this->BForm->input('codigo_status_pedidos_exames', array('label' => 'Status Pedido', 'class' => 'input-xlarge', 'options' => $lista_status_pedidos_exames, 'style' => 'width: 200px')); ?>       	
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery(".lista");
        bloquearDiv(div);
        div.load(baseUrl + "itens_pedidos_exames_baixa/listagem/" + Math.random());
		
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PedidoExame/element_name:itens_pedidos_exames_baixa/" + Math.random())
        });
    });', false);
?>