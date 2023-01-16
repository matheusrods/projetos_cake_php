<div class='well'>
  	<?php echo $bajax->form('Veiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Veiculo', 'element_name' => 'status_checklist'), 'divupdate' => '.form-procurar')) ?>
		<?php echo $this->element('veiculos/fields') ?>
	<?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'veiculos/status_checklist_listagem/' + Math.random()); 
        $('#limpar-filtro').click(function(){
            bloquearDiv($('.form-procurar'));
            $('.form-procurar').load(baseUrl + '/filtros/limpar/model:Veiculo/element_name:status_checklist/' + Math.random())
        });
        
    });", false);
?>