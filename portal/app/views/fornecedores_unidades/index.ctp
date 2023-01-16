<?php if(isset($dados_fornecedor) && !empty($dados_fornecedor)): ?>
	<div class='well'>
	    <p><strong>Código Matriz: </strong><?=$dados_fornecedor['Fornecedor']['codigo']; ?></p>
	    <p><strong>Razão Social: </strong><?=$dados_fornecedor['Fornecedor']['razao_social']; ?></p>
	    <p><strong>Nome Fantasia: </strong><?=$dados_fornecedor['Fornecedor']['nome']; ?></p>
	</div>	
	<?php if($dados_fornecedor['Fornecedor']['tipo_unidade'] == 'O'): ?>
		<div class="alert alert-error">
			<p>Não foi possível gravar os dados de novas filiais.</BR>
			Por favor, selecione um Fornecedor do tipo <b>Fiscal</b>.
		</div>		
	<?php else: ?>
		<div class = 'form-procurar clickdisabled'>
			<?= $this->element('/filtros/fornecedores_unidades') ?>
		</div>

		<div class='actionbar-right'>
			<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'fornecedores_unidades', 'action' => 'incluir',$codigo_fornecedor_matriz), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Unidade'));?>
		</div>
		<div class='lista'></div>
	<?php endif;?>
<?php endif;?>
<div class='form-actions well'>
    <?php echo $html->link('Voltar', array('controller' => 'fornecedores', 'action' => 'index'), array('class' => 'btn')); ?>
</div>