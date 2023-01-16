<div class="well">		
	
	<table class='table'>
			<tr class='input-large'>
				<td colspan="3" class='input-large'>
					<div class='row-fluid inline'>
						<?php echo $this->Form->hidden('codigo');?>
						<strong><?php  echo $this->BForm->input('descricao',array('label' => 'Critérios','class'=>'input-large'));?>
						</strong>	
					</div>
				</td>
				<td class='input-large'></td>
			</tr>	

			<tr class='input-large'>
				<td class="action-icon">		
					
					<?php echo $this->BForm->checkbox('controla_qtd'); ?>
					
				</td>
				<td class="first">	<strong>INFORMAR QUANTIDADE </strong></td>
				<td class='input-large'></td>
				<td></td>

			</tr>
			<tr class='input-large'>	
				<td class="action-icon">	
					<div class="control-group input checkbox">	
						<?php echo $this->BForm->checkbox('aceita_texto'); ?>
					</div>	
				</td>
				<td class="first"><strong>INCLUIR TEXTO </strong></td>
				<td class='input-large'></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
	</table>
</div>					
			
			<div class='form-actions'>
			    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
			    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
			</div>
			<?php echo $this->BForm->end(); ?>
		
	