    <div class="modal-dialog modal-sm" style="position: static;">
    	<div class="modal-content">
    		<div class="modal-header" style="text-align: center;">
    			<h3>PEDIDO NÚMERO: <?php echo $codigo_pedido; ?></h3>
    		</div>

    		<div class="modal-body" style="min-height: 295px;max-height: 365px;">

    			<?php foreach ($agenda as $dados) : ?>

    				<span style="font-size: 1.2em">
    					<b>Data do Pedido:</b><br />
    					<?php echo $dados['PedidoExame']['data_inclusao']; ?>
    				</span>
    				<br /><br />

    				<span style="font-size: 1.2em">
    					<b>Data do Agendamento:</b><br />
    					<?php
							if (empty($dados['AgendamentoExame']['data_inclusao'])) {
								echo $dados['PedidoExame']['data_agendamento'];
							} else {
								$data_hora = explode(" ", $dados['PedidoExame']['data_agendamento']);
								echo AppModel::formataData($data_hora[0]) . " " . sprintf('%04s', $data_hora[1]);
							}
							?> por <?php echo $dados['Usuario']['nome']; ?>
    				</span>
    				<br /><br />

    				<span style="font-size: 1.2em">
    					<b>Exame:</b><br />
    					<?php echo $dados['Exame']['descricao']; ?>
    				</span>
    				<br /><br />

    				<span style="font-size: 1.2em">
    					<b>Tipo Exame:</b><br />
    					<?php if ($dados['PedidoExame']['exame_admissional']) : ?>
    						Exame Admissional
    					<?php elseif ($dados['PedidoExame']['exame_periodico']) : ?>
    						Exame Periodico
    					<?php elseif ($dados['PedidoExame']['exame_demissional']) : ?>
    						Exame Demissional
    					<?php elseif ($dados['PedidoExame']['exame_retorno']) : ?>
    						Exame de Retorno
    					<?php elseif ($dados['PedidoExame']['exame_mudanca']) : ?>
    						Mudança de Riscos Ocupacionais
    					<?php elseif ($dados['PedidoExame']['qualidade_vida']) : ?>
    						Qualidade Vida
    					<?php endif; ?>
    				</span>
    				<br /><br />
    				<span style="font-size: 1.2em">
    					<b>Tipo de Agendamento:</b><br />
    					<?php echo $dados['ItemPedidoExame']['tipo_agendamento'] == '1' ? 'Interno' : 'Externo'; ?>
    				</span>
    				<br /><br />

    				<span style="font-size: 1.2em">
    					<b>Cliente:</b><br />
    					<?php echo $dados['Cliente']['razao_social']; ?>
    				</span>
    				<br /><br />

    				<span style="font-size: 1.2em">
    					<b>Funcionario:</b><br />
    					<?php echo $dados['Funcionario']['nome']; ?>
    				</span>
    				<br /><br />

    				<span style="font-size: 1.2em">
    					<b>Fornecedor:</b><br />
    					<?php echo $dados['Fornecedor']['razao_social']; ?>
    				</span>
    				<br /><br />

    				<span style="font-size: 1.2em">
    					<b>Data Agendado:</b><br />
    					<?php echo $dados['AgendamentoExame']['data']; ?> - <?php echo substr(str_pad($dados['AgendamentoExame']['hora'], 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($dados['AgendamentoExame']['hora'], 4, 0, STR_PAD_LEFT), 2, 2); ?>
    				</span>
    				<br /><br />

    				<span style="font-size: 1.2em">
    					<b>Realizado:</b><br />
    					<?php echo isset($dados['ItemPedidoExameBaixa']['data_realizacao_exame']) && !empty($dados['ItemPedidoExameBaixa']['data_realizacao_exame']) ?  $dados['ItemPedidoExameBaixa']['data_realizacao_exame'] : 'NÃO'; ?>
    				</span>
    				<br />
    				<hr />
    			<?php endforeach; ?>
    		</div>

    		<div class="modal-footer">
    			<div class="right">
    				<a href="javascript:void(0);" onclick="fechar(<?php echo $codigo_pedido; ?>);" class="btn btn-danger">FECHAR</a>
    			</div>
    		</div>
    	</div>
    </div>

    <?php echo $this->Javascript->codeBlock('

	jQuery(document).ready(function() {
		$("#modal_agendamento_"+codigo_pedido).css("z-index", "1050");
		$("#modal_agendamento_"+codigo_pedido).modal("show");

		alert("poa");
	});

	function fechar(id) {
		$(".modal").css("z-index", "-1");
		$("#modal_agendamento_" + id).modal("hide");
	}
		
'); ?>