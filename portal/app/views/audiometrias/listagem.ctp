<?php if (!empty($audiometrias)):?>
<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini">Código Pedido</th>
            <th class="input-small">Data do pedido</th>
            <th class="input-large">Cliente</th>
            <th class="input-large">Funcionário</th>
            <th class="input-small">Tipo de exame</th>
            <th class="input-small">Status</th>
            <th class="input-small">Data do exame</th>
            <th class="input-small">Resultado</th>
            <th class="acoes" style="width:150px">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($audiometrias as $dados): ?>
        <tr>
            <td class="input-mini">
                <?php echo $dados['PedidoExame']['codigo'] ?>
            </td>
            <td class="input-mini">
                <?php echo $dados['PedidoExame']['data_solicitacao'] ?>
            </td>
            <td class="input-mini">
                <?php echo $dados['Cliente']['razao_social'] ?>
            </td>
            <td class="input-mini">
                <?php echo $dados['Funcionario']['nome'] ?>
            </td>
            <td class="input-mini">
                <?php echo $dados[0]['tipo_exame'] ?>
            </td>
            <td>
                <?php echo ($dados['Audiometria']['codigo'])? 'REALIZADO' : 'PENDENTE'  ?>
            </td>
            <td>
                <?php echo $dados['Audiometria']['data_exame'] ?>
            </td>
            <td>
                <?php echo (!is_null($dados['Audiometria']['resultado']))? (($dados['Audiometria']['resultado'] == 1)? 'Alterado' : 'Normal')  : ''  ?>
            </td>
            <td>
                <?php if ($dados['Audiometria']['codigo']) { ?>
                <div class="elementos_editar">
                    <?php echo $this->Html->link('', array('action' => 'editar', $dados['Audiometria']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar', 'data-toggle' => 'tooltip')); ?>&nbsp;&nbsp;

                    <?php echo $this->Html->link('', array('action' => 'imprimir_relatorio', $dados['Audiometria']['codigo']), array('class' => 'icon-print ', 'title' => 'Imprimir relatório', 'data-toggle' => 'tooltip')); ?>&nbsp;&nbsp;

                    <?php echo $this->Html->link('', array('action' => 'ver_relatorio', $dados['Audiometria']['codigo']), array('class' => 'icon-search', 'title' => 'Ver relatório', 'data-toggle' => 'tooltip', 'target' => '_blank')); ?>
                </div>
                <?php } else { ?>
                <div class="elementos_incluir">
                    <?php echo $this->Html->link('Atender', array('controller' => 'audiometrias', 'action' => 'incluir', $dados['ItemPedidoExame']['codigo']), array('class' => 'btn btn-small')); ?>
                    <a class="btn btn-small" href="javascript:void(0);"
                        onclick="print_relatorio(this, <?php echo $dados['PedidoExame']['codigo']; ?>, <?php echo $dados['PedidoExame']['codigo_cliente_funcionario']; ?>);">Imprimir</a>
                </div>
                <?php } ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10">
                <strong>Total</strong> <?php echo $this->Paginator->params['paging']['ItemPedidoExame']['count']; ?>
            </td>
        </tr>
    </tfoot>
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
<?php  echo $this->Js->writeBuffer(); ?>
<?php
echo $this->Javascript->codeBlock("
		
    $(document).ready(function() {
        $('[data-toggle=\"tooltip\"]').tooltip();
    });
		
	function print_relatorio(element, codigo_pedido, codigo_cliente_funcionario) {
		var element_origin = $(element).html();
		
		$.ajax({
	        type: 'POST',
	        url: '/portal/audiometrias/visualizar/',
	        dataType: 'json',
	        data: 'codigo_pedido=' + codigo_pedido,
	        beforeSend: function() {
				$(element).html('<img src=\'/portal/img/default.gif\'>');
			},
	        success: function(json) {
				if(json) {
                    var protocol = (window.location.host == 'tstportal.rhhealth.com.br' || window.location.host == 'portal.rhhealth.com.br') ? 'https://' : 'http://';
					$.each(json, function(key_fornecedor, item) {
						window.open(protocol + window.location.host + '/portal/pedidos_exames/imprime/' + codigo_pedido + '/' + key_fornecedor + '/' + codigo_cliente_funcionario + '/6');
					});
				}
	        },
	        complete: function() {
				$(element).html(element_origin);
			}
	    });				
	}		
");
?>
<?php else:?>
<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>
