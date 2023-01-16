<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Manutenção Pedido do Exame</h3>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;" >
			<div>
				<span style="font-size: 1.2em;display: none;" id="span_resultado">
					<b>Função:<font color="red">*</font></b>
				</span>
				<?php echo $this->BForm->input('codigo_matricula', array('label' => false, 'class' => 'input-xxlarge resultado', 'options' => $matriculas, 'empty' => 'Selecione Matricula para este pedido de exames', 'default' => $pedidos_exames[0]['PedidoExame']['codigo_func_setor_cargo'], 'type' => 'select')) ?>
				
			</div>
			<div class="modal-header" style="text-align: center;">
				<h3>Exames</h3>
			</div>
			<div >
				<table class='table table-striped tablesorter'>
			        <thead>
			            <tr>
			                <th>Exame</th>
			                <th>Tipo</th>
			                <th>Data Realização</th>
			                <th>Data Baixa</th>
			            </tr>
			        </thead>
			        <tbody>
			            <?php foreach ($pedidos_exames as $dados): ?>

			            	<?php 

			            	if($dados['PedidoExame']['exame_admissional'] == 1) {
								$condicao_tipo_exame = 'Exame Admissional';
							} else if($dados['PedidoExame']['exame_demissional'] == 1) {
								$condicao_tipo_exame = 'Exame Demissional';
							} else if($dados['PedidoExame']['exame_periodico'] == 1) {
								$condicao_tipo_exame = 'Exame Periódico';
							} else if($dados['PedidoExame']['pontual'] == 1) {
								$condicao_tipo_exame = 'Pontual';
							} else if($dados['PedidoExame']['exame_retorno'] == 1) {
								$condicao_tipo_exame = 'Exame Retorno';
							} else if($dados['PedidoExame']['exame_mudanca'] == 1) {
								$condicao_tipo_exame = 'Exame Mudança';
							}
			            	?>

			                <tr>
			                    <td><?php echo $dados['Exame']['descricao'] ?></td>
			                    <td><?php echo $condicao_tipo_exame; ?></td>
			                    <td><?php echo $dados['ItemPedidoExameBaixa']['data_realizacao_exame'] ?></td>
			                    <td><?php echo substr($dados['ItemPedidoExameBaixa']['data_inclusao'],0,10) ?></td>
			                </tr>
			            <?php endforeach; ?> 
			        </tbody>        
			    </table>
				
			</div>
		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="editar_realizacao_datas(<?php echo $codigo_pedido; ?>, 0);"class="btn btn-danger">FECHAR</a>
				<a href="javascript:void(0);"onclick="salvar_realizacao(<?php echo $codigo_pedido; ?>);" class="btn btn-success">SALVAR</a>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	setup_mascaras();
	setup_datepicker();
	setup_time();
	
	atualizaListaManutencao2 = function() {
		var codigo_cliente = $("#ImportarCodigoCliente").val();
		var cpf = $("#ImportarCpf").val();
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "importar/manutencao_pedido_exame_listagem/" + codigo_cliente + "/"+cpf+"/"+ Math.random());
    }

	salvar_realizacao = function(codigo_pedido) {

		//pega os dados para alteracao
		var codigo_func_setor_cargo = $('#codigo_matricula').val();

		if(codigo_func_setor_cargo == "") {
			swal({
				type: 'warning',
				title: 'Atenção',
				text: "Favor selecione uma matricula.",
			});

			return false;
		}
		
		var div = jQuery('#modal_data');
	    bloquearDiv(div);
		
		//envia via ajax a data de realizacao
		$.ajax({
			url: baseUrl + 'importar/manutencao_salvar',
			type: 'POST',
			dataType: 'json',
			data: {
				"codigo_pedido"   			: codigo_pedido,
				"codigo_func_setor_cargo" 	: codigo_func_setor_cargo,			
			}

		})
		.done(function(data) {
			
			if(data.retorno == 'false') {
				swal({
					type: 'warning',
					title: 'Atenção',
					text: data.mensagem,
				});
				
			desbloquearDiv(div);

			} else {
				swal({
					type: 'success',
					title: 'Sucesso',
					text: 'Dados atualizados com sucesso.'
				});
				
				editar_realizacao_datas(codigo_pedido, 0);			
				atualizaListaManutencao2();
			}
		});


	}//fim function salvar_realizacao
});


</script>
