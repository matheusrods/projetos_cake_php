<div class='well'>
	<?php echo $bajax->form('PcmsoVersoes', array('autocomplete' => 'off', 
												  'url' => array('controller' => 'filtros', 
												  				 'action' => 'filtrar', 
												  				 'model' => 'PcmsoVersoes', 
												  				 'element_name' => 'pcmso_versoes'), 
												   'divupdate' => '.form-procurar')
							) ?>	
	<div class='row-fluid inline'>
		<?php echo $this->Buonny->input_consulta_versao($this, 'PcmsoVersoes', $unidades); 
			  echo $this->BForm->input('codigo_medico', array('label' => 'Médico Coordenador', 
															      'class' => 'input-xlarge',
															      'options' => $medicos, 
															      'empty' => 'Selecione um Médico')
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
		div.load(baseUrl + "pcmso_versoes/listagem/" + Math.random());
		jQuery("#limpar-filtro-clientes-funcionarios").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PcmsoVersoes/element_name:pcmso_versoes/" + Math.random())
        });
    });', false);
?>