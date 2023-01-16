<div id="divDadosProdutos">
	<h4 id="titProdutos" style="display: <?=(((isset($this->data['PropostaProduto'])) && (is_array($this->data['PropostaProduto']['codigo_produto'])) && count($this->data['PropostaProduto']['codigo_produto'])>0) ? "block" : "none" )?>">Características da Operação</h4>
	<? if ( (isset($this->data['PropostaProduto'])) && (is_array($this->data['PropostaProduto']['codigo_produto'])) && count($this->data['PropostaProduto']['codigo_produto'])>0): ?>
		<? foreach ($this->data['PropostaProduto']['codigo_produto'] as $seq => $codigo_produto) : ?>
			<? $this->set(compact('codigo_produto')) ?>
			<? echo $this->element('propostas/detalhes_produto'); ?>
		<? endforeach; ?>
	<? endif; ?>
</div>

