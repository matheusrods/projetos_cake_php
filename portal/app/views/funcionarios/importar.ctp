<div class='form-procurar well'>
	<?php echo $this->BForm->create('Funcionario', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'funcionarios', 'action' => 'importar', $codigo_cliente, $referencia))); ?>
		<div class="row-fluid inline">	
			<?php echo $this->Html->link('<i class="icon-file"></i>Modelo para Importação', $this->webroot.'../../arquivos/modelo_importacao_cliente_funcionarios.csv', array('escape' => false, 'target' => '_blank',  'title' => 'Visualizar Modelo para Importação', 'style' => 'float:right; padding-right: 100px;')); ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('arquivo', array('type'=>'file', 'label' => false)); ?>
		</div>
		<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente)); ?>
		<?php echo $this->BForm->submit('Importar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		<?php echo $html->link('Voltar', array('controller'=>'funcionarios','action' => 'index', $codigo_cliente, $referencia), array('class' => 'btn')); ?>					
	<?php echo $this->BForm->end(); ?>
</div>

<?php if(!empty($this->data['arquivo'])): ?>
	<div class='well'>
		<h5>Ocorreu erro(s) durante a importação. Verifique o arquivo.</h5>
	<?php echo $html->link('Abrir Arquivo', array('controller'=>'funcionarios','action' => 'abre_arquivo',$this->data['arquivo']), array('class' => 'btn')); ?>
	</div>
<?php endif; ?>