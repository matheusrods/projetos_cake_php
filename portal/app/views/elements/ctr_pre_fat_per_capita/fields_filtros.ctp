<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'CtrPreFatPerCapita'); ?>

	<?php echo $this->BForm->input('mes_faturamento', array('label' => false, 
															'placeholder' => 'Mês', 
															'class' => 'input-medium', 
															'options' => $meses, 
															'empty' => 'Selecione',
															'title' => 'Mês de Faturamento')
		  ) 
	?>
	
	<?php echo $this->BForm->input('ano_faturamento', array('label' => false, 
															'placeholder' => 'Ano',
															'class' => 'input-mini just-number numeric',
															'maxLength' => 4,
															'title' => 'Ano de Faturamento')) ?>
</div>        
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-clientes', 'class' => 'btn')) ;?>
<?php echo $this->Javascript->codeBlock('
									$(document).ready(function(){
										
										setup_mascaras();
									});

		')?>