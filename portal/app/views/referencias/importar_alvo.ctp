<?php echo $this->BForm->create('TRefeReferencia', array(
			'url' => array(
				'controller'=>'Referencias',
				'action'=>'importar_alvo',
				$this->passedArgs[0]
			),
			'enctype'=>'multipart/form-data'
			)
		);
	?>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
		<strong style="margin:0 0 0 20px">Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
        <span class="pull-right">
            <?php
			echo $this->Html->Link(
				'<i class="cus-page-white-excel"></i>&nbsp;Arquivo Exemplo', 
				'/files/Modelo_de_Alvo.xls', 
				array(
					'class' => 'button', 
					'escape' => false,
					'title' =>'Arquivo Exemplo',
					'target' => '_blank'
				)
			); 
			?> 
        </span>
	    
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('arquivo_csv', array('label' => 'Selecione o arquivo CSV', 'type' => 'file','class' => 'input-xlarge endereco')); ?>
	</div>
	<div class="retornoCarregamento"></div>
	<div class="form-actions" style="clear:both;">
		  <?php echo $this->BForm->submit('Processar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		  <?php if(!$isAjax): ?>
		  	<?php echo $html->link('Voltar',array('controller' => 'Referencias', 'action' => 'adicionar_referencia'), array('class' => 'btn')) ;?>
		  <?php endif; ?>
	</div>
<?php echo $this->BForm->end(); ?>