<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content">
		<div class="modal-header" style="text-align: center;">
			<h3>Editar - Matricula Data Fim</h3>
		</div>

		<div class="modal-body" style="min-height: 295px;">
			<span >
				Funcionário: <?php echo $funcionario['Funcionario']['nome']; ?>
			</span>
			<br />
			<span >
				CPF: <?php echo Comum::formatarDocumento($funcionario['Funcionario']['cpf']); ?>
			</span>
			<br /><br />

			<span >
				<b>Dados da Matricula</b>
			</span>
			<br />

			<span >
				Código: <?php echo $funcionario['ClienteFuncionario']['codigo']; ?>
			</span>
			<br />
			
			<span >
				Matrícula: <?php echo $funcionario['ClienteFuncionario']['matricula']; ?>
			</span>
			<br />

			<span >
				Unidade: <?php echo $funcionario['Cliente']['nome_fantasia']; ?>
			</span>
			<br />

			<span >
				Inclusão: <?php echo $funcionario['ClienteFuncionario']['data_inclusao']; ?>
			</span>
			<br />

			<span >
				Data Demissão: <?php echo $this->BForm->input('data_demissao', array('id' => 'data_demissao', 'type' => 'text', 'label' => '', 'class' => 'data input-small', 'value' => AppModel::dbDateToDate($funcionario['ClienteFuncionario']['data_demissao']))); ?>
			</span>
			
		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);" onclick="editar_data(<?php echo $codigo_matricula; ?>, 0);" class="btn btn-danger">FECHAR</a>
				<a href="javascript:void(0);" onclick="salvar_realizacao(<?php echo $funcionario['ClienteFuncionario']['codigo_cliente_matricula']; ?>,<?php echo $codigo_matricula; ?>);" class="btn btn-success">SALVAR</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	
	setup_mascaras(); 
	setup_datepicker(); 

});

function salvar_realizacao(codigo_cliente_matricula,codigo_cliente_funcionario) {

	//pega a data
	var data_demissao = $('#data_demissao').val();
	
	//verifica se existe a data de realizacao
	if(data_demissao == '' || data_demissao == undefined) {
		swal({
			type: 'warning',
			title: 'Atenção',
			text: 'Favor setar a Data de Demissão',
		});
	}
	else {

		//envia via ajax a data de realizacao
		$.ajax({
			url: baseUrl + 'funcionarios/salvar_data_demissao',
			type: 'POST',
			dataType: 'json',
			data: {"codigo_cliente_funcionario": codigo_cliente_funcionario, "data_demissao": data_demissao},
		})
		.done(function(data) {
			
			if(data.retorno == 'false') {
				swal({
					type: 'warning',
					title: 'Atenção',
					text: 'Erro ao atualizar a data de demissão'
				});
			} 
			else {

				swal({
					type: 'success',
					title: 'Sucesso',
					text: 'Data demissão atualizados com sucesso.'
				});

				editar_data(codigo_cliente_funcionario, 0);
				
				var destino = "funcionarios/listagem_percapita/" + codigo_cliente_matricula;
				var div = "div.lista";
				atualizaLista(div, destino);
			}
		});
		
	}//fim verificacao da data


}//fim function salvar_realizacao

</script>