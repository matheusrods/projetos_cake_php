	<div class="row-fluid inline">
		<? if (isset($campos[$codigo_produto])): ?>
			<? foreach ($campos[$codigo_produto] as $key => $dados_campo): ?>
				<? if ($readonly) $dados_campo['opcoes']['disabled'] = true; ?>
				<?=$this->BForm->input($dados_campo['nome'],$dados_campo['opcoes']);?>
			<? endforeach;?>
		<? endif; ?>
	</div>
