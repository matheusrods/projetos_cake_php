<div class='well'>
  <?php echo $bajax->form('AgendamentoSugestao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AgendamentoSugestao', 'element_name' => 'agendamento'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
       	<?php echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false)) ?>
       	<?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Fornecedor','AgendamentoExame');?>
       	<?php echo $this->BForm->input('nome_funcionario', array('label' => 'Nome Funcionário', 'class' => 'input-xlarge', 'type' => 'text')); ?>
       	<?php echo $this->BForm->input('notificado', array('type' => 'checkbox', 'label' => 'Pedidos Notificados')); ?>
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
        div.load(baseUrl + "agendamento/listagem/" + Math.random());
		
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AgendamentoSugestao/element_name:agendamento/" + Math.random())
        });
    });', false);
?>