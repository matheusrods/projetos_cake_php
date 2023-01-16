<table class="table table-striped">
    <thead>
        <tr>
            <th>Código</th>
            <th>Descrição</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($operacoes as $operacao): ?>
        <tr>
            <td><?php echo $operacao['Operacao']['codigo'] ?></td>
            <td><?php echo $operacao['Operacao']['descricao'] ?></td>
            <td>
                <?php echo $this->Html->link('', array('controller' => 'operacoes', 'action' => 'editar', $operacao['Operacao']['codigo']), array('class' => 'icon-edit', 'title' => 'editar')); ?>
                <?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "excluir_operacao({$operacao['Operacao']['codigo']})")) ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
<?= $this->Javascript->codeBlock("
	function excluir_operacao(codigo) {
        if (confirm('Deseja realmente excluir ?')){
			jQuery.ajax({
				type: 'POST',
					url: baseUrl + 'operacoes/excluir/' + codigo + '/' + Math.random()
					,success: function(data) {
							atualizaListaOperacoes();
					}
			});
		}
    }
	"
	);
?>