<h4>Itinerario</h4>
<div class="actionbar-right">
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-destino', 'escape' => false)); ?>
</div>
<div class='row-fluid inline destino'>

<?php $localizador_cliente2 = ($this->data['Recebsm']['codigo_cliente'] == $this->data['Recebsm']['embarcador'] ? '#RecebsmTransportador' : '#RecebsmEmbarcador') ?>
<?php for ($key = 0; $key < (isset($this->data['RecebsmAlvoDestino']) ? count($this->data['RecebsmAlvoDestino']) : 1); $key++): ?>
	<table class='table table-striped destino' data-index="<?php echo $key ?>" data-code="<?php echo $this->data['Recebsm']['codigo_cliente'] ?>">
		<thead>
			<th>
				<div class="row-fluid inline">
					<?php echo $this->Buonny->input_referencia($this, '#RecebsmCodigoCliente', 'RecebsmAlvoDestino', 'refe_codigo', $key, 'Itinerario Alvo', true, true, $localizador_cliente2) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.dataFinal", array('label' => 'Previsão Chegada', 'class' => 'data input-small')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.horaFinal", array('label' => 'Hora', 'class' => 'hora input-mini')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.janela_inicio", array('label' => 'Janela Inicio', 'class' => 'hora input-mini')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.janela_fim", array('label' => 'Janela Fim', 'class' => 'hora input-mini')) ?>				    
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.tipo_parada", array('label' => 'Tipo Itinerario', 'class' => 'input-small', 'options' => $tipo_parada, 'empty' => 'Selecione um Tipo')) ?>

					<?php if($key > 0): ?>
						<div class="control-group input text">
							<label for="RecebsmDtaFim">&nbsp</label>
							<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-destino-remove', 'escape' => false)); ?>				
						</div>
					<?php endif; ?>
				</div>
			</th>
		</thead>
		<thead style="display:none;">
			<th>
				<?php echo $this->Buonny->input_referencia_endereco($this, 'RecebsmAlvoDestino', 'refe_codigo', $key ); ?>
			</th>
		</thead>
		<tbody>
			<tr>
				<td>
					<?php for ($keyNotas = 0; $keyNotas < (isset($this->data['RecebsmAlvoDestino'][$key]['RecebsmNota']) ? count($this->data['RecebsmAlvoDestino'][$key]['RecebsmNota']) : 1); $keyNotas++): ?>
						<div class="row-fluid inline">
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaLoadplan", array('class' => 'input-medium', 'label' => false,'placeholder' => 'Loadplan/Chassi', 'maxlength' => 15)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaNumero", array('class' => 'input-mini', 'label' => false,'placeholder' => 'Nº NF', 'maxlength' => 15)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaSerie", array('class' => 'input-micro', 'label' => false,'placeholder' => 'Série', 'maxlength' => 10)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.carga", array('class' => 'input-medium','options' => $tipo_carga , 'empty' => 'Produto','label' => false)) ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaValor", array('class' => 'input-small moeda', 'label' => false,'placeholder' => 'Valor da Nota')); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaVolume", array('class' => 'input-mini just-number', 'label' => false,'placeholder' => 'Volume', 'maxlength' => 9)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaPeso", array('class' => 'input-mini just-number', 'label' => false,'placeholder' => 'Peso', 'maxlength' => 9)); ?>
							<?php echo $this->Html->link('<i class="icon-plus icon-white "></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-nota-fiscal', 'escape' => false)); ?>
						</div>
					<?php endfor ?>
				</td>

			</tr>
		</tbody>
	</table>
<?php endfor ?>

</div>
<?php echo $this->Javascript->codeBlock('
	
    var contador_destino = $("div.destino table").length;
    
	$(document).ready(function() {
		
		setup_mascaras();
		setup_time();

		$(document).off("click","a.novo-destino");
		$(document).on("click","a.novo-destino",function(){
			var conteiner = $("div.destino");
			var codigo_cliente = '.(!empty($this->data['Recebsm']['codigo_cliente']) ? $this->data['Recebsm']['codigo_cliente'] : NULL).';
			cliente = '.json_encode($this->data['Recebsm']).';
			contador_destino++;
			transportador = cliente.transportador;
			embarcador = cliente.embarcador;
			$.ajax({
				url: baseUrl + "solicitacoes_monitoramento/novo_destino/"+ (contador_destino-1) +"/"+ transportador +"/"+ embarcador +"/"+ codigo_cliente +"/"+ Math.random(),
				dataType: "html",
				success: function(data){
					conteiner.prepend(data);
					setup_datepicker();
					setup_time();
					setup_mascaras();
				},
				complete: function(){
					$.placeholder.shim();
				}
			});
		});

		$(document).on("click", "a.novo-nota-fiscal",function(){
			var conteiner = $(this).parents("tbody:first");
			var table = $(this).parents("table:first");
			cliente = '.json_encode($this->data['Recebsm']).';

			$.ajax({
				url: baseUrl + "solicitacoes_monitoramento/novo_nota_fiscal/"+ table.attr("data-index") +"/"+ conteiner.children("tr").length +"/"+ cliente.transportador +"/"+ cliente.embarcador +"/"+ Math.random(),
				dataType: "html",
				success: function(data){
					conteiner.append(data);
					setup_mascaras();
				},
				complete: function(){
					$.placeholder.shim();
				}
			});
		});

		$(document).on("click","a.novo-nota-remove",function(){
			$(this).parents("tr:eq(0)").remove();
			return false;
		});
		
		$(document).on("click","a.novo-destino-remove",function(){
			$(this).parents("table:eq(0)").remove();
			return false;
		});

		
	});
	
');
?>