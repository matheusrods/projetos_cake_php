<div class='well'>
	<row class="row-fluid">
		<div class="span2"><strong>Código: </strong> <?php echo $plano_servico['Servico']['codigo']; ?></div>
		<div class="span6"><strong>Plano: </strong> <?php echo $plano_servico['Servico']['descricao']; ?></div>   
	</row>
</div>
<div class='actionbar-right'>
	<a href="#myModal" role="button" class="btn btn-success" data-toggle="modal"><i class="icon-plus icon-white"></i></a>
</div>
<?php echo $this->BForm->create('ServicoPlanoSaude', array('url' => array('controller' => 'servicos_planos_saude', 'action' => 'selecionar_servicos', $codigo))); ?>
<?php echo $this->Form->hidden('codigo', array('value' => (int)$codigo, 'name' => 'data[codigo]')); ?>
<div class='lista margin-top-10'>
	<table class="table table-striped">
		<thead>
			<tr>
				<th class="input-xxlarge">Serviço</th>
				<th class="input-medium">Tipo Uso</th>
				<th class="input-medium numeric">Máximo</th>            
				<th class="input-small" style="text-align:center">Ações</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			if(!empty($this->data)) {
				$incluidos = array();
				foreach ($this->data as $key => $value) {
					$incluidos[] = $value['ClassificacaoServico']['codigo'];
					?>
					<tr data-codigo="<?php echo $key ?>" data-id="<?php echo $value['ServicoPlanoSaude']['codigo'] ?>">
						<input type="hidden" name="data[nao_excluir][]" value="<?php echo (int)$value['ClassificacaoServico']['codigo'] ?>">
						<td><?php echo $value['ClassificacaoServico']['descricao']; ?></td>
						<td><?php echo $value['TipoUso']['descricao']; ?></td>
						<td class="numeric"><?php echo $value['ServicoPlanoSaude']['maximo']; ?></td>
						<td  style="text-align:center"><span class="excluir pointer" data-toggle="tooltip" title="Excluir serviço"><i class="icon-trash"></i></span></td>
					</tr>
					<?php 
				} 
			} ?>
		</tbody>
		<tfoot>
		</tfoot>    
	</table>
</div>

<div class="form-actions">
	<?php // echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	<?php echo $html->link('Voltar',array('action'=>'listar_planos_saude') , array('class' => 'btn')); ?>
</div>
<?php echo $this->Form->end(); ?>


<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Selecione o Serviço</h3>
	</div>
	<form id="insere_servico">
		<div class="modal-body">
			<div class="row-fluid">
				<div class="span4">
					<?php echo $this->Form->input('codigo_classificacao_servico', array('label' => 'Serviço:', 'name' => 'codigo_classificacao_servico', 'options' => $servicos, 'empty' => 'Selecione um serviço', 'class' => 'span12', 'required' => true)); ?>
				</div>
				<div class="span4">
					<?php echo $this->Form->input('codigo_tipo_uso', array('label' => 'Tipo de Uso:', 'name' => 'codigo_tipo_uso', 'options' => $tipos_uso, 'empty' => 'Selecione um tipo de uso', 'class' => 'span12', 'required' => true)); ?>
				</div>
				<div class="span4">
					<label>Máximo:</label>
					<input type="text" class="for-quant numeric span6 just-number" required="required">
					<input type="hidden" class="for-val numeric span6 moeda_com_decimal" required="required" disabled="true">
				</div> 
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
			<button class="btn btn-primary">Inserir Serviço</button>
		</div>
	</form>
</div>

<input type="hidden" id="codigo_servico" value="<?php echo $codigo ?>">
<input type="hidden" id="cont" value="<?php echo (isset($key)? $key+1 : 0) ?>">

<style type="text/css">
	.modal {
		width: 980px;
		margin-left: -490px;
	}
</style>


<script type="text/javascript">
	$(document).ready(function() {
		setup_mascaras();
		var servicos_inseridos = [];
		servicos = '<?php echo isset($incluidos)? json_encode($incluidos) : '' ?>';
		if(servicos != '') {
			servicos_inseridos = servicos.replace('[', '').replace(']', '').split(',');
		}
		var i = $('#cont').val();
		function limpa_campos(este) {
			este.find('[name="codigo_classificacao_servico"]').val('');
			este.find('[name="codigo_tipo_uso"]').val('');
			este.find('[name="maximo"]').val('');
		}

		function grava_dados(dados, callback) {
			$.ajax({
				url: baseUrl + 'servicos_planos_saude/incluir',
				type: 'POST',
				dataType: 'json',
				data: {ServicoPlanoSaude: dados},
				success: function(response) {
					callback(response);
				}
			})
		}

		function exclui_dado(codigo, callback) {
			$.ajax({
				url: baseUrl + 'servicos_planos_saude/excluir',
				type: 'POST',
				dataType: 'json',
				data: {codigo: codigo},
				success: function(response) {
					callback(response);
				}
			})
		}

		$('#insere_servico').submit(function(event) {
			event.preventDefault();
			var este = $(this);

			//captura os valores do formulario
			var codigo_servico = $('#codigo_servico').val();
			var codigo_classificacao_servico =  $(this).find('[name="codigo_classificacao_servico"]').val();
			var descricao_servico = $(this).find('[name="codigo_classificacao_servico"] option:selected').text();
			var descricao_tipo_uso = $(this).find('[name="codigo_tipo_uso"] option:selected').text();
			var codigo_tipo_uso =  $(this).find('[name="codigo_tipo_uso"]').val();
			var maximo =  $(this).find('[name="maximo"]').val();

			if(!servicos_inseridos.includes(codigo_classificacao_servico)) {
			bloquearDiv($('#myModal'));
			var dados = {
				codigo_servico: codigo_servico,
				codigo_classificacao_servico: codigo_classificacao_servico,
				codigo_tipo_uso: codigo_tipo_uso,
				maximo: maximo
			};
			grava_dados(dados, function(response) {
				if(response.return == true) {

					var html = $('<tr>', {'data-codigo': i, 'data-id': response.codigo});
					html.append($('<td>', {text: descricao_servico}));
					html.append($('<td>', {text: descricao_tipo_uso}));
					html.append($('<td>', {text: maximo, class: 'numeric'}));
					html.append($('<td>', {style: 'text-align:center'}).append('<span class="excluir pointer" data-toggle="tooltip" title="Excluir serviço"><i class="icon-trash"></i></span>') );

					$('.lista').find('table tbody').append(html);
					este.parents('#myModal').modal('hide');
					servicos_inseridos[i] = codigo_classificacao_servico;
					limpa_campos(este);
					i++;
					desbloquearDiv($('#myModal'));

				}
			});
			
		} else {
			swal({
				type: 'warning',
				title: 'Atenção',
				text: 'Este serviço já está adicionado ao plano.'
			});
			desbloquearDiv($('#myModal'));
		}
		
	});

		$('[name="codigo_tipo_uso"]').change(function(event) {
			switch(parseInt($(this).val())) {
				// para quantidade
				case 1: 
				$('.for-val').val('').attr({disabled: true, type: 'hidden', name: false});
				$('.for-quant').attr({disabled: false, type: 'text', name: 'maximo'});
				break;

				// para valor
				case 2:
				$('.for-quant').val('').attr({disabled: true, type: 'hidden', name: false});
				$('.for-val').attr({disabled: false, type: 'text', name: 'maximo'});
				break;
			}
		});

		$('body').on('click', '.excluir', function() {
			este = $(this).parents('tr');
			var codigo = este.attr('data-id');
			swal({
				type: 'warning',
				title: 'Atenção',
				text: 'Tem certeza que deseja excluir este servico?',
				showCancelButton: true,
				closeOnConfirm: false,
				showLoaderOnConfirm: true,
				confirmButtonText: 'Sim',
				cancelButtonText: 'Cancelar'
			}, function(isConfirm){
				if(isConfirm) {
					exclui_dado(codigo, function(response) {
						if(response == true) {
							swal.close();
							este.remove();
							delete servicos_inseridos[este.attr('data-codigo')];
						} else {
							swal({
								type: 'error',
								title: 'Erro',
								text: 'Ouve um erro ao exlcuir o servico, por favor tente novamente.',
							});
						}
					});
				}
			});
		});
	});
</script>