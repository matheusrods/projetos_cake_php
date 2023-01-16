<div class='well'>
  <?php echo $bajax->form('ResultadoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ResultadoExame', 'element_name' => 'resultados_exames'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
    
       	<?php echo $this->BForm->input('codigo_pedido', array('label' => 'Pedido', 'class' => 'input-mini just-number', 'title' => 'Número do Pedido', 'type' => 'text')); ?>

        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true, 'ResultadoExame'); ?>
       	<?php echo $this->BForm->input('nome_funcionario', array('label' => 'Nome Funcionário', 'class' => 'input-xlarge', 'type' => 'text')); ?>      
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery(".lista");
        bloquearDiv(div);
        div.load(baseUrl + "resultados_exames/listagem/" + Math.random());
		
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ResultadoExame/element_name:resultados_exames/" + Math.random())
        });
    });', false);
?>