<div class='well'>
	<?php echo $bajax->form('ClienteFuncionarioVidas', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteFuncionarioVidas', 'element_name' => 'vidas'), 'divupdate' => '.form-procurar')) ?>
	<?php echo $this->BForm->input('ClienteFuncionarioVidas.ativo', array('options' => array('1'=>'ATIVO', '0' => 'INATIVO'), 'empty' => 'Selecione o Status', 'legend' => false, 'label' => false, 'div' =>'control-group input select')) ?>   
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id' => 'bt-vidas')) ?>			
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-vidas', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "clientes_funcionarios/vidas_listagem/" + Math.random());
		jQuery("#limpar-filtro-vidas").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteFuncionarioVidas/element_name:vidas/" + Math.random())
        });
    });', false);
?>