<div class='well'>
	<?php echo $bajax->form('PpraVersoes', array('autocomplete' => 'off', 
												  'url' => array('controller' => 'filtros', 
												  				 'action' => 'filtrar', 
												  				 'model' => 'PpraVersoes', 
												  				 'element_name' => 'ppra_versoes'), 
												   'divupdate' => '.form-procurar')
							) ?>
	
	<div class='row-fluid inline'>
		<?php echo $this->Buonny->input_consulta_versao($this, 'PpraVersoes', $unidades); 
			  echo $this->BForm->input('codigo_medico', array('label' => 'Profissional Responsável', 
															      'class' => 'input-xlarge',
															      'options' => $medicos, 
															      'empty' => 'Selecione um Profissional')
										   );
		?>
	</div>	
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-clientes-funcionarios', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "ppra_versoes/listagem/" + Math.random());
		jQuery("#limpar-filtro-clientes-funcionarios").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PpraVersoes/element_name:ppra_versoes/" + Math.random())
        });
    });', false);
?>