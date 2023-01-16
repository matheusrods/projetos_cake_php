<?php echo $this->BForm->create('TIpcpInformacaoPcp', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'pcp', 'action' => 'index'))); ?>

<?php if(!$cliente): ?>
	<div class="row-fluid inline">
	    <?php echo $this->Buonny->input_codigo_cliente($this,'codigo_cliente','Cliente',true,'TIpcpInformacaoPcp'); ?>
    </div>
   	<?php echo $this->BForm->submit('Consultar', array('div' => false, 'class' => 'btn btn-primary')); ?>
<?php else: ?>
	<?php echo $this->BForm->hidden('codigo_cliente'); ?>
	<div class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
		<span class="pull-right">
			<?php echo $this->Html->Link(
			'<i class="cus-page-white-excel"></i>&nbsp;Arquivo Exemplo',
			'/files/Modelo_de_Importação_PCP.xls',
			array(
				'class'  => 'button',
				'target' => '_blank',
				'escape' => false,
				)
			);?>
	    </span>
	</div>
	<div class='row-fluid inline'>
	    <?php echo $this->BForm->input('arquivo', array('type'=>'file', 'label'=>'Selecione o arquivo CSV', 'class' => 'input-xlarge' )); ?>
	</div>
	<div class="form-actions">
	    <?php echo $this->BForm->submit('Processar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	    <?php echo $this->Html->link('Voltar',array('controller' => 'pcp','action' => 'listar_pcp'),array('class' => 'btn')); ?>
	</div>
	<?php if(isset($importados)): ?>
		<div class='well'>
			<strong>Importados: </strong><?= $importados ?>
			<strong>Não Importados: </strong><?= $nao_importados ?>
		</div>
	<?php endif; ?>
	<?php if(isset($erros)): ?>
		<?php foreach($erros as $erro): ?>
			<div><?php echo $erro ?></div>
		<?php endforeach; ?>
	<?php endif; ?>
<?php endif; ?>