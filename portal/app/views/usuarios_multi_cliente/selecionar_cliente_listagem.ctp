<?php if(!empty($clientes)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
            <th class="input-mini">Código</th>
            <th>Razão Social</th>
            <th>Nome Fantasia</th>
            <th>CNPJ</th>
            <th class="input-mini"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $dados): ?>
	            <tr>
	                <td class="input-mini"><?php echo $dados['Cliente']['codigo'] ?></td>
	                <td><?php echo $dados['Cliente']['razao_social'] ?></td>
	                <td><?php echo $dados['Cliente']['nome_fantasia'] ?></td>
	                <td><?php echo $buonny->documento($dados['Cliente']['codigo_documento']) ?></td>
	                <td class="input-mini">
		                <?php echo $this->Html->link('Acessar', array('action' => 'emular_cliente', $dados['Cliente']['codigo']), array('title' => 'Emular', 'class' => 'btn btn-success')); ?>
	                </td>
	            </tr>
        	<?php endforeach ?>
    	</tbody>
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    
    <?php echo $this->Js->writeBuffer(); ?>
    <?php 
$this->addScript($this->Buonny->link_js('comum.js'));
echo $this->Javascript->codeBlock("

    function atualizaListaMultiEmpresa(){
	    var div = jQuery('div.listaSelecionar');
	    bloquearDiv(div);
	    div.load(baseUrl + 'usuarios_multi_cliente/selecionar_cliente_listagem/' + Math.random());
}
   
");
?>