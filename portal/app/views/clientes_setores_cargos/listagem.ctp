<?php if(!empty($cliente_setor_cargo)):?>
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th class="input-xxlarge">Unidade</th>
				<th class="input-xxlarge">Setor</th>
				<th class="input-xxlarge">Cargo</th>
				<th class="input-medium">Funcionários Alocados</th>
				<th class="input-medium">Data Inclusão</th>
				<th class="acoes" style="width:90px">Ações</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($cliente_setor_cargo as $key => $dados): ?>					
				<tr>
					<td class="input-xlarge"><?php echo $dados[0]['nome_fantasia'] ?></td>
					<td class="input-xxlarge"><?php echo $dados['Setor']['descricao'] ?></td>
					<td class="input-xxlarge"><?php echo $dados['Cargo']['descricao'] ?></td>
					<td class="input-mini"><?php echo $dados[0]['qtd_funcionarios'] ?></td>
					<td ><?php echo substr($dados['ClienteSetorCargo']['data_inclusao'],0,10); ?></td>
					<td>
	                    <?php
	                    	echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusHierarquia('{$dados['ClienteSetorCargo']['codigo']}')"));
	                    ?>
						<?php if($dados['ClienteSetorCargo']['ativo'] === 0): ?>
							<span class="badge-empty badge badge-important" title="Desativado" style="margin-right: 5px"></span>
						<?php elseif(empty($dados['ClienteSetorCargo']['ativo'])): ?>
							<span class="badge-empty badge badge-success" title="Ativo" style="margin-right: 5px"></span>
						<?php elseif($dados['ClienteSetorCargo']['ativo']== 1): ?>
							<span class="badge-empty badge badge-success" title="Ativo" style="margin-right: 5px"></span>
						<?php endif; ?>             

						<?php echo $this->Html->link('', '#modalEditar'.$key, array('data-toggle' => 'modal', 'class' => 'icon-edit ', 'title' => 'Editar')); ?>&nbsp;

						<!-- trecho comentado por causa da PC-2661 -->
						<?php //echo $this->Html->link('', '#', array('class' => 'icon-remove js-excluir', 'data-codigo' => $dados['ClienteSetorCargo']['codigo'], 'title' => 'Excluir')); ?>
					</td>
				</tr>

				<!-- Modal -->
				<div id="modalEditar<?php echo $key ?>" data-id="<?php echo $dados['ClienteSetorCargo']['codigo'] ?>" class="modal hide fade js-editar" tabindex="-1" role="dialog" aria-labelledby="modalEditar<?php echo $key ?>Label" aria-hidden="true">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h3 id="myModalLabel">Editar Hierarquia</h3>
					</div>
					<div class="modal-body">
						<div class="row-fluid">
							<div class="span12">
								<?php echo $this->Form->input('codigo_cliente_alocacao', $options = array('label' => 'Unidade', 'class' => 'form-control input-xxlarge codigo_cliente','options' => $unidades, 'default' => $dados['Cliente']['codigo'])); ?>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span12">
								<?php echo $this->Form->input('codigo_setor', $options = array('label' => 'Setor',  'class' => 'form-control input-xxlarge codigo_setor','options' => $setores, 'default' => $dados['Setor']['codigo'])); ?>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span12">
								<?php echo $this->Form->input('codigo_cargo', $options = array('label' => 'Cargo',  'class' => 'form-control input-xxlarge codigo_cargo','options' => $cargos, 'default' => $dados['Cargo']['codigo'])); ?>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
						<button class="btn btn-primary salvar">Salvar</button>
					</div>
				</div>

			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ClienteSetorCargo']['count']; ?></td>
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
	<?php echo $this->Js->writeBuffer(); ?>
	<?php 
	echo $this->Javascript->codeBlock('
		function atualizaLista(){
			var div = jQuery("div.lista");
			bloquearDiv(div);            
			div.load(baseUrl + "clientes_setores_cargos/listagem/" + '.$codigo_cliente.');
		}
		');
		?>
	<?php else:?>
		<div class="alert">Nenhum dado foi encontrado.</div>
	<?php endif;?>    

	<script type="text/javascript">
		$(document).ready(function() {
			$('.js-editar .salvar').click(function(event) {
				var este = $(this);
				if(validaDados(este.parents('.js-editar').find('.codigo_cliente'), este.parents('.js-editar').find('.codigo_setor'), este.parents('.js-editar').find('.codigo_cargo'))) {
					var button = 'Salvar;'
					este.css({height: $('.salvar').outerHeight(), width: $('.salvar').outerWidth() }).html('<img src="' + baseUrl + 'img/default.gif">');
					$.ajax({
						url: baseUrl + 'clientes_setores_cargos/editar',
						type: 'POST',
						dataType: 'json',
						data: {
							codigo: este.parents('.js-editar').attr('data-id'),
							codigo_cliente: este.parents('.js-editar').find('.codigo_cliente').val(),
							codigo_setor: este.parents('.js-editar').find('.codigo_setor').val(),
							codigo_cargo: este.parents('.js-editar').find('.codigo_cargo').val(),
						},
					})
					.done(function(response) {
						if(response) {
							swal({
								type: 'success',
								title: 'Sucesso',
								text: 'A hierarquia foi salva com sucesso.'
							});
							atualizaLista();
							setup_mascaras();
							este.parents('.js-editar').modal('hide');
							este.parents('.js-editar').find('.codigo_cliente').val('');
							este.parents('.js-editar').find('.codigo_setor').val('');
							este.parents('.js-editar').find('.codigo_cargo').val('');
						}
					})
					.fail(function() {
						este.html(button);
					})
					.always(function() {
						este.html(button);
					});
				} 
			});

			$('.js-excluir').click(function(event) {
				var este = $(this);
				swal({
					title: "Atenção",
					text: "Tem certeza que deseja excluir este dado?",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, delete it!",
					closeOnConfirm: false,
					cancelButtonText: 'Não',
					confirmButtonText: 'Sim',
					showLoaderOnConfirm: true,
				},
				function(){
					$.ajax({
						url: baseUrl + 'clientes_setores_cargos/excluir',
						type: 'POST',
						dataType: 'json',
						data: {codigo: este.attr('data-codigo')},
					})
					.done(function(response) {
						if(response) {
							swal("Excluído com sucesso!");
							atualizaLista();
							setup_mascaras();
						}
					});              
				});
			});

		});

	    function atualizaStatusHierarquia(codigo)
	    {

	        $.ajax({
	            type: 'POST',
	            url: baseUrl + 'clientes_setores_cargos/editar_status/' + codigo,
	            beforeSend: function(){
	                bloquearDivSemImg($('div.lista'));  
	            },
	            success: function(data){           
	                if(data == 1){
	                    atualizaLista();
	                    $('div.lista').unblock();
	                    swal('Sucesso!', 'Status da Hierarquia atualizado com sucesso!', 'success');	                 
	                } else {
	                    atualizaLista();
	                    $('div.lista').unblock();
	                    swal("Importante","Não foi possível mudar o status!", "error");	                    
	                }
	            },
	            error: function(erro){
	                $('div.lista').unblock();
	                swal("Importante","Não foi possível mudar o status!", "error");	   
	            }
	        });
	    }
	</script>
