<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Relacionar Unidades ao Certificado</h3>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;">

			<?php
			echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente));
			echo $this->BForm->hidden('codigo_certificado', array('value' => $codigo_certificado));
			?>

			<?php 
			if(!empty($todas_unidades)) {
				
				echo "<div class='row-fluid inline ' >";
				echo '<p><input type="checkbox" id="checkTodos" name="checkTodos"> Selecionar Todos</p>';

				//verifica os dados de todas as unidades
				foreach($todas_unidades AS $codigo_cliente_matriz => $arr_dados) {
					//varre tdas as unidades da matriz
					foreach($arr_dados AS $dados) {

						if($dados['Unidade']['ativo'] == 1) {
							
							$codigo_documento = ($dados['Unidade']['codigo_documento_real'] <> '') ? $dados['Unidade']['codigo_documento_real'] : $dados['Unidade']['codigo_documento'];
							//cnpj é valido
							if(Comum::validarCNPJ($codigo_documento)) {
								$codigo_unidade = $dados['GrupoEconomicoCliente']['codigo_cliente'];
								$nome_unidade = $dados['Unidade']['nome_fantasia'];

								$nome_unidade_imp = ($codigo_cliente_matriz == $codigo_unidade) ? "<b>{$nome_unidade}</b>" : $nome_unidade;
								echo $this->BForm->input("codigo_unidade_certificado_".$codigo_unidade, array('label' => $nome_unidade_imp, 'class'=>'unidades','type' => 'checkbox', 'value'=>$codigo_unidade, 'div' => true));
							}
						}
					}//fim dados_unidades
				}//fim todasunidades

				echo "</div>";
			}
			?>
		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="relacionar_unidades_certo(0);"class="btn btn-danger">FECHAR</a>
				<a id="Ok" href="javascript:void(0);" class="btn btn-success" onclick="salvar_unidades();">SALVAR</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	setup_mascaras();
	setup_datepicker();
	setup_time();

	$("#checkTodos").change(function () {
	    $("input:checkbox").prop('checked', $(this).prop("checked"));
	});

});

var mensagem = function(mensagem, tipo, titulo){
	
	this.tipo = tipo || 'warning'
	this.titulo = titulo || 'Atenção'

	return swal({
		type: this.tipo,
		title: this.titulo,
		text: mensagem
	});
}

function isValidDate(d) {
  return d instanceof Date && !isNaN(d);
}

function salvar_unidades() {

	//pega a data
	
	var codigo_cliente = $("#codigo_cliente").val();
    var codigo_certificado = $("#codigo_certificado").val(); 

    var arr_obj_unidades = [];
	$(".unidades").each(function () {
		if($(this).prop("checked")) {
        	arr_obj_unidades.push({id:$(this).val()});
		}
    });

	var div = jQuery('#modal_data');
    bloquearDiv(div);
	
	//envia via ajax a data de realizacao
	$.ajax({
		url: baseUrl + 'mensageria_esocial/salvar_rel_cliente_certificado',
		type: 'POST',
		dataType: 'json',
		data: {
			"codigo_cliente": codigo_cliente,
			"codigo_certificado": codigo_certificado,
			"arr_obj_unidades": arr_obj_unidades
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

            //swall
            swal("Atenção", "Certificado integrado com sucesso.", "success");

            atualizaLista();
			relacionar_unidades_certo(0);
			
            // $("div.lista").unblock();

		}
	});
	

}//fim function salvar_realizacao

</script>