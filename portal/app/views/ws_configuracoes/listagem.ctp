<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'ws_configuracoes', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Nova Configuração'));?>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Tipo Mensagem', 'tipo_mensagem') ?></th>
            <th><?= $this->Paginator->sort('SOAP URL', 'soap_url') ?></th>
            <th><?= $this->Paginator->sort('SOAP Função', 'soap_funcao') ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($configuracoes as $configuracao): ?>
        <tr>
            <td><?= $configuracao['WsConfiguracao']['tipo_mensagem'] ?></td>
            <td><?= $configuracao['WsConfiguracao']['soap_url'] ?></td>
            <td><?= $configuracao['WsConfiguracao']['soap_funcao'] ?></td>
            <td class="pagination-centered">
				<?= $html->link('', array('action' => 'editar', $configuracao['WsConfiguracao']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
                <?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "excluir_ws_configuracao({$configuracao['WsConfiguracao']['codigo']})")) ?>
			</td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>

<div class='row-fluid'>
	<div class='numbers span6'>
		<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
	  <?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	</div>
	<div class='counter span6'>
		<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	</div>
</div>
<?php echo $this->Js->writeBuffer(); ?>
<?= $this->Javascript->codeBlock("
    function excluir_ws_configuracao(codigo) {
        if (confirm('Deseja realmente excluir ?')){
            jQuery.ajax({
                type: 'POST',
                    url: baseUrl + 'ws_configuracoes/excluir/' + codigo + '/' + Math.random()
                    ,success: function(data) {
                            atualizaListaWsConfiguracoes();
                    }
            });
        }
    }
    "
    );
?>