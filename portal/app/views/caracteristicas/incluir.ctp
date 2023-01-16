<?php echo $this->BForm->create('Caracteristica'); ?>
<div class="well">
	<?php echo $this->BForm->input('titulo', array('class' => 'input-xxlarge', 'placeholder' => 'Título (*)', 'label' => false)) ?>
	<?php echo $this->BForm->input('alerta', array('class' => 'input-xxlarge', 'placeholder' => 'Alerta (*)', 'label' => false)) ?>
	<?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'placeholder' => 'Descrição (*)', 'label' => false)) ?>
	<hr>	
	<h5>Selecione as respostas que participarão desta característica</h5>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Questionario</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$i = 0;
			foreach ($questionarios as $key => $questionario) { ?>
			<tr>
				<td>
					<div class="pointer open"><i class="icon-chevron-right"></i> <?php echo $questionario['Questionario']['descricao'] ?></div>
					<div class="sub hide">
						<?php foreach ($questionario['Questao'] as $key2 => $questao) { ?>
						<div class="margin-left-30 margin-top-20"><strong><?php echo $questao['label'] ?></strong></div>
						<?php foreach ($questao['Respostas'] as $key3 => $resposta) { ?>
						<div class="margin-left-60">
							<?php echo $this->Form->input('Caracteristica.respostas.'.$i, array('type' => 'checkbox', 'style' => 'margin-top: 5px', 'id' => 'resposta'.$i, 'value' => $key3, 'label' => $resposta)); ?>
						</div>
						<?php $i++; } ?>
						<?php } ?>
					</div>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('.open').click(function(event) {
			if($(this).parent().find('.sub').hasClass('hide')) {
				$(this).find('i').removeClass('icon-chevron-right').addClass('icon-chevron-down');
				$(this).parent().find('.sub').removeClass('hide');
			} else {
				$(this).find('i').removeClass('icon-chevron-down').addClass('icon-chevron-right');
				$(this).parent().find('.sub').addClass('hide');
			}
		});
	});
</script>