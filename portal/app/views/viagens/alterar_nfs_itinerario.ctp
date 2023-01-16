
<?php echo $this->BForm->create('TViagViagem', array('type' => 'post','url' => array('controller' => 'Viagens','action' => 'alterar_nfs_itinerario',$cliente['Cliente']['codigo'],$this->data['TViagViagem']['viag_codigo'])));?>
<div class='row-fluid inline'>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
	</div>

	<div id="destino" class='well'>
		<div class="row-fluid inline" >
			<?php echo $this->BForm->hidden('viag_codigo') ?>
			<?php echo $this->BForm->input('viag_codigo_sm', array('class' => 'input-small', 'label' => 'SM', 'readonly' => true)) ?>
			<?php echo $this->BForm->input('viag_previsao_inicio', array('class' => 'input-medium', 'label' => 'Previsão Viagem', 'readonly' => true, 'type' => 'text')) ?>
		</div>
		<div class="row-fluid inline" >
			<?php echo $this->BForm->input('Destino.refe_descricao', array('class' => 'input-xlarge', 'label' => 'Destino', 'readonly' => true)) ?>
		</div>
	</div>
</div>
<?php echo $this->BForm->hidden('viag_valor_carga') ?>
<?php echo $this->BForm->hidden('viag_data_fim') ?>
<?php echo $this->BForm->hidden('TVestViagemEstatus.vest_estatus') ?>
<?php echo $this->BForm->hidden('viag_tran_pess_oras_codigo') ?>
<?php echo $this->BForm->hidden('viag_emba_pjur_pess_oras_codigo') ?>
<?php echo $this->BForm->hidden('Recebsm.transportador',array('value' => $tran_cliente['Cliente']['codigo'])) ?>
<?php echo $this->BForm->hidden('Recebsm.codigo_cliente',array('value' => $emba_cliente['Cliente']['codigo'])) ?>

<h4>Itinerário</h4>
<div class='row-fluid inline destino' id="divDestino">
<?php for ($key = 0; $key < (isset($this->data['RecebsmAlvoDestino']) ? count($this->data['RecebsmAlvoDestino']) : 1); $key++): ?>
	<table class='table table-striped destino' data-index="0" id="tableAlvo<?=$key?>">
		<thead>
			<th>
				<div class="row-fluid inline">
					<!--<?php echo $this->Buonny->input_referencia($this, '#RecebsmCodigoCliente', 'RecebsmAlvoDestino', 'refe_codigo', $key, 'Local Parada', true, true, '#RecebsmTransportador') ?>-->
					<?php echo $this->BForm->hidden("RecebsmAlvoDestino.{$key}.vloc_codigo",array()) ?>
					<?php echo $this->BForm->hidden("RecebsmAlvoDestino.{$key}.refe_codigo",array()) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.refe_codigo_visual", array('label' => 'Itinerario Alvo', 'class' => 'input-xlarge','readonly'=>true)) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.dataFinal", array('label' => 'Previsão Chegada', 'class' => 'data input-small','readonly'=>true)) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.horaFinal", array('label' => 'Hora', 'class' => 'hora input-mini','readonly'=>true)) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.janela_inicio", array('label' => 'Janela', 'class' => 'hora input-mini','readonly'=>true)) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.janela_fim", array('label' => '&nbsp', 'class' => 'hora input-mini','readonly'=>true)) ?>
					<?php echo $this->BForm->hidden("RecebsmAlvoDestino.{$key}.tipo_parada", array()) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.tipo_parada_visual", array('label' => 'Tipo de Parada', 'class' => 'input-medium', 'readonly'=>true)) ?>
				</div>
			</th>
		</thead>
		<tbody>
			<?php for ($keyNotas = 0; $keyNotas < (isset($this->data['RecebsmAlvoDestino'][$key]['RecebsmNota']) ? count($this->data['RecebsmAlvoDestino'][$key]['RecebsmNota']) : 1); $keyNotas++): ?>
				<tr>
					<td>
						<div class="row-fluid inline">
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaLoadplan", array('class' => 'input-medium', 'label' => false,'placeholder' => 'Loadplan/Chassi')); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaNumero", array('class' => 'input-small', 'label' => false,'placeholder' => 'Nº Nota Fiscal')); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaSerie", array('class' => 'input-mini', 'label' => false,'placeholder' => 'Série')); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.carga", array('class' => 'input-medium','options' => $tipo_carga , 'empty' => 'Produto','label' => false)) ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaValor", array('class' => 'input-small moeda', 'label' => false,'placeholder' => 'Valor da Nota')); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaVolume", array('class' => 'input-mini just-number', 'label' => false,'placeholder' => 'Volume', 'maxlength' => 9)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaPeso", array('class' => 'input-mini just-number', 'label' => false,'placeholder' => 'Peso', 'maxlength' => 9)); ?>
							<?if ($keyNotas>0): ?>
								<?php echo $this->Html->link('<i class="icon-plus icon-white " ></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-nota-fiscal', 'escape' => false, 'style'=>'display: none;')); ?>

								<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-nota-remove', 'escape' => false)); ?>
							<? else: ?>
								<?php echo $this->Html->link('<i class="icon-plus icon-white "></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-nota-fiscal', 'escape' => false)); ?>
								<?php echo $this->Html->link('<i class="icon-minus icon-black " ></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-nota-remove', 'escape' => false, 'style'=>'display: '.(count($this->data['RecebsmAlvoDestino'][$key]['RecebsmNota']) > 1 ? "" : "none").';')); ?>
							<? endif; ?>
						</div>
					</td>
				</tr>
			<?php endfor ?>
		</tbody>
	</table>
<?php endfor ?>

</div>
<div class="form-actions">
	  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	  <?php echo $html->link('Voltar', 'itinerarios', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	
    var contador_destino = $("div.destino table").length;
    
	$(document).ready(function() {
		setup_mascaras();
		setup_time();

		$("a.novo-destino").click(function(){
			var conteiner = $("div.destino");
			contador_destino++;

			$.ajax({
				url: baseUrl + "solicitacoes_monitoramento/novo_destino/"+ (contador_destino-1) +"/"+ false +"/"+ Math.random(),
				dataType: "html",
				success: function(data){
					conteiner.prepend(data);
					setup_time();
					setup_mascaras();
				}
			});
		});

		$(document).on("click", "a.novo-nota-fiscal",function(){
			var conteiner = $(this).parent().parent().parent().parent();
			var table = $(this).parents("table:first");

			$.ajax({
				url: baseUrl + "solicitacoes_monitoramento/novo_nota_fiscal/"+ table.attr("data-index") +"/"+ conteiner.children("tr").length +"/"+  false +"/"+ Math.random(),
				dataType: "html",
				success: function(data){
					conteiner.append(data);
					setup_mascaras();
				}
			});

			$(this).parent().find(".novo-nota-remove").show();
		});

		$(document).on("click","a.novo-nota-remove",function(e){
			var id_table = ($(this).closest("table").attr("id"));

			$(this).parents("tr:eq(0)").remove();

			var rowCount = $("#"+id_table+" >tbody >tr").length;
			$("#"+id_table+" >tbody >tr").find(".novo-nota-fiscal").eq(0).show();
			if (rowCount==1) {
				$("#"+id_table+" >tbody >tr").find(".novo-nota-remove").hide();
			}

			return false;
		});
		
		$(document).on("click","a.novo-destino-remove",function(){
			$(this).parents("table:eq(0)").remove();
			return false;
		});

		
	});
	
');
?>