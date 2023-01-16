<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Condições</h3>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;">

			<?php
			echo $this->BForm->hidden('codigo', array('value' => $codigo));
			echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente));
			echo $this->BForm->hidden('codigo_tema', array('value' => $codigo_tema));

			echo $this->BForm->hidden('codigo_pda_config_regra_condicao', array('value' => $codigo_pda_config_regra_condicao));
			
			?>

			<?php if($codigo_tema == 12) : // Notificar de acordo com a criticidade ?>

				<div class='row-fluid inline acao_melhoria' >
					<?php echo $this->BForm->input('codigo_pos_criticidade', array('label' => 'Criticidade', 'class' => 'input-notificar-criticidade input-acao-melhoria input_codigo_pos_criticidade', 'empty' => 'Selecione um Criticidade', 'options' => $criticidade)); ?>
		        </div>
				<?php
				echo $this->BForm->hidden('tipo_sla', array('value' => ''));
				echo $this->BForm->hidden('qtd_dias', array('value' => ''));
				echo $this->BForm->hidden('codigo_cliente_unidade', array('value' => ''));
				echo $this->BForm->hidden('codigo_setor', array('value' => ''));
				echo $this->BForm->hidden('codigo_cliente_opco', array('value' => ''));
				echo $this->BForm->hidden('codigo_cliente_bu', array('value' => ''));
				echo $this->BForm->hidden('codigo_pos_swt_form_titulo', array('value' => ''));
				echo $this->BForm->hidden('codigo_pos_swt_form_questao', array('value' => ''));
				?>
			<?php elseif($codigo_tema == 13) : //Observações em atraso de tratativa ?>
		    	<div class='row-fluid inline ' >
	                <?php echo $this->BForm->input('tipo_sla', array('label' => 'Sla', 'class' => 'input input-follow-up', 'empty' => 'Selecione um Tipo SLA', 'options' => $tipo_sla)); ?>
	                <img src="/portal/img/loading.gif" title="carregando..." id="loading_tipo_sla" style="position: relative; margin-top: 30px; display: none;" />
	                <?php echo $this->BForm->input('qtd_dias', array('label' => 'Quantidade de dias corridos após o atraso na tratativa de uma observação', 'class' => 'input-large input-follow-up')); ?>
		    	</div>
		    	<div class='row-fluid inline ' >
		            <?php echo $this->BForm->input('codigo_cliente_unidade', array('label' => 'Unidades', 'class' => 'input input-follow-up', 'empty' => 'Selecione uma Unidade', 'options' => $unidades)); ?>
		        </div>
		    	<div class='row-fluid inline ' >
		            <?php echo $this->BForm->input('codigo_cliente_opco', array('label' => 'Opco', 'class' => 'input input-follow-up', 'empty' => 'Selecione um Opco', 'options' => $cliente_opco)); ?>
		            <?php echo $this->BForm->input('codigo_cliente_bu', array('label' => 'Bussiness Unit', 'class' => 'input input-follow-up', 'empty' => 'Selecione uma Bussiness Unit', 'options' => $cliente_bu)); ?>
	            </div>
		            <?php
					echo $this->BForm->hidden('codigo_pos_swt_form_titulo', array('value' => ''));
					echo $this->BForm->hidden('codigo_setor', array('value' => ''));
					echo $this->BForm->hidden('codigo_pos_swt_form_questao', array('value' => ''));
					echo $this->BForm->hidden('codigo_pos_criticidade', array('value' => ''));
		            ?>
		    <?php endif; ?>
		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="cad_condicoes_obs(0);"class="btn btn-danger">FECHAR</a>
				<a id="Ok" href="javascript:void(0);" class="btn btn-success" onclick="salvar_condicoes_obs();">SALVAR</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	setup_mascaras();
	setup_datepicker();
	setup_time();

	$("#tipo_sla").on('change', function() {
		get_tipo_sla($(this).val());		
	});

	get_tipo_sla = function(codigo_tipo_sla) {

		$("#qtd_dias").attr('readonly',false);
		$("#qtd_dias").val("");

		if(codigo_tipo_sla == "") {
			return false;
		}

		if(codigo_tipo_sla == 2) {
			return false;
		}

		$("#loading_tipo_sla").show();

		var codigo_cliente = $("#codigo_cliente").val();
		
		//envia via ajax a busca das questoes
		$.ajax({
			"url": baseUrl + 'pda_config_regra/get_tipo_sla/'+codigo_cliente+ "/" + Math.random(),
			"success": function(data) {
				$("#qtd_dias").attr('readonly',true);
				$("#qtd_dias").val(data);
				$("#loading_tipo_sla").hide();
			}
		});
	}

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

function salvar_condicoes_obs() {

	//pega a data
	var codigo = $("#codigo").val();
	var codigo_cliente = $("#codigo_cliente").val();
    var codigo_pda_config_regra_condicao = $("#codigo_pda_config_regra_condicao").val(); 

	var codigo_pos_criticidade = $("#codigo_pos_criticidade").val();
	var codigo_pos_swt_form_titulo = $("#codigo_pos_swt_form_titulo").val();
	var codigo_pos_swt_form_questao = $("#codigo_pos_swt_form_questao").val();

	var tipo_sla = $("#tipo_sla").val();
	var qtd_dias = $("#qtd_dias").val();
	var codigo_cliente_unidade = $("#codigo_cliente_unidade").val();
	var codigo_setor = $("#codigo_setor").val();
	var codigo_cliente_opco = $("#codigo_cliente_opco").val();
	var codigo_cliente_bu = $("#codigo_cliente_bu").val();

	var div = jQuery('#modal_data');
    bloquearDiv(div);
	
	//envia via ajax a data de realizacao
	$.ajax({
		url: baseUrl + 'pda_config_regra/salvar_condicoes',
		type: 'POST',
		dataType: 'json',
		data: {
			"codigo": codigo,
			"codigo_cliente": codigo_cliente,
			"codigo_pda_config_regra_condicao": codigo_pda_config_regra_condicao,
			
			"codigo_pos_criticidade": codigo_pos_criticidade,
			"codigo_pos_swt_form_titulo": codigo_pos_swt_form_titulo,
			"codigo_pos_swt_form_questao": codigo_pos_swt_form_questao,

			"tipo_sla": tipo_sla,
			"qtd_dias": qtd_dias,
			"codigo_cliente_unidade": codigo_cliente_unidade,
			"codigo_setor": codigo_setor,
			"codigo_cliente_opco": codigo_cliente_opco,
			"codigo_cliente_bu": codigo_cliente_bu
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
			cad_condicoes_obs(0);
			atualizaListaCondicoesObs();
		}
	});
	

}//fim function salvar_realizacao

</script>