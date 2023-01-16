<div class='well'>
	<strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['codigo']); ?>
	<strong>Cliente: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['razao_social']); ?>
</div>

<div class = 'form-procurar'>
	<?= $this->element('/filtros/clientes_setores_cargos') ?>
</div>
<div class='actionbar-right margin-bottom-10'>
	<?php echo $this->BForm->input('codigo_matriz', array('type' => 'hidden', 'id' => 'codigo_matriz', 'value' => $codigo_cliente)); ?>
	<?php echo $html->link('Copiar Hierarquia', array('controller' => 'clientes_setores_cargos', 'action' => 'copiar_hierarquia', $this->data['Cliente']['codigo'], $terceiros_implantacao), array('class' => 'btn btn-default', 'title' => 'Copiar Hierarquia')); ?>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', '#modalAdicionar', array('escape' => false, 'data-toggle' => 'modal', 'class' => 'btn btn-success', 'title' =>'Nova hierarquia'));?>
</div>
<div class='lista'></div>

<div class='form-actions well'>
	<?php if($terceiros_implantacao == 'terceiros_implantacao'): ?>
		<?php echo $html->link('Voltar para Estrutura', array('controller' => 'clientes_implantacao', 'action' => 'estrutura', $codigo_cliente, $referencia, $terceiros_implantacao), array('class' => 'btn', 'title' => 'Voltar para Lista Implantação')); ?>
	<?php else: ?>
		<?php echo $html->link('Voltar para Estrutura', array('controller' => 'clientes_implantacao', 'action' => 'estrutura', $codigo_cliente, $referencia), array('class' => 'btn', 'title' => 'Voltar para Lista Implantação')); ?>
	<?php endif; ?>
</div>


<div id="modalAdicionar" class="modal hide fade js-adicionar" tabindex="-1" role="dialog" aria-labelledby="modalAdicionarLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Inserir Hierarquia</h3>
	</div>
	<div class="modal-body">
		<div class="row-fluid">
			<div class="span12">
				<?php echo $this->Form->input('codigo_cliente_alocacao', array('label' => 'Unidade', 'class' => 'form-control input-xxlarge codigo_cliente_alocacao','options' => $unidades, 'empty' => 'Selecione uma unidade')); ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<?php echo $this->Form->input('codigo_setor', array('label' => 'Setor',  'class' => 'form-control input-xxlarge codigo_setor','options' => $setores, 'empty' => 'Selecione um setor')); ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<?php echo $this->Form->input('codigo_cargo', array('label' => 'Cargo',  'class' => 'form-control input-xxlarge codigo_cargo','options' => $cargos, 'empty' => 'Selecione um cargo')); ?>
			</div>
		</div>
	</div>
	<div class="modal-footer">	
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
		<button class="btn btn-primary salvar">Salvar</button>
		<div id="loading_save" style="margin: 5px 20px; background: #F5F5F5;display: none;">
	    	<img src="/portal/img/default.gif" style="padding: 15px;">Registrando Hierarquia...
		</div>
	</div>
</div>
<?php $this->addScript($this->Buonny->link_js('clientes_setores_cargos')); ?>
<?php echo $this->Javascript->codeBlock('
	function atualizaLista(){
		var div = jQuery("div.lista");
		bloquearDiv(div);            
		div.load(baseUrl + "clientes_setores_cargos/listagem/" + '.$codigo_cliente.');
	}
	$(document).ready(function(){
		// atualizaLista();
		setup_mascaras();
	});		
	', false);
	?>

	<script type="text/javascript">
		$(document).ready(function() {
			$('.js-adicionar .salvar').click(function(event) {
				var este = $(this);
				if(validaDados(este.parents('.js-adicionar').find('.codigo_cliente_alocacao'), este.parents('.js-adicionar').find('.codigo_setor'), este.parents('.js-adicionar').find('.codigo_cargo'))) {
					var button = $('.salvar').html();
					este.css({height: este.outerHeight(), width: este.outerWidth() }).html('<img src="' + baseUrl + 'img/default.gif">');
					$.ajax({
						url: baseUrl + 'clientes_setores_cargos/incluir',
						type: 'POST',
						dataType: 'json',
						data: {
							codigo_cliente_alocacao: este.parents('.js-adicionar').find('.codigo_cliente_alocacao').val(),
							codigo_matriz: $('#codigo_matriz').val(),
							codigo_setor: este.parents('.js-adicionar').find('.codigo_setor').val(),
							codigo_cargo: este.parents('.js-adicionar').find('.codigo_cargo').val()
						},
						beforeSend: function() {  
							este.html(button);
							$('#loading_save').show();
						},
						success: function(data) {									
							if(data.return == false) {
								swal({
									type: 'error',
									title: 'Erro!',
									text: data.msg
								});
								este.html(button);
								$('#loading_save').hide();
							} else {
								swal({
									type: 'success',
									title: 'Sucesso',
									text: data.msg
								});								
								atualizaLista();
								setup_mascaras();
								este.html(button);
								este.parents('.js-adicionar').modal('hide');
								este.parents('.js-adicionar').find('.codigo_cliente').val('');
								este.parents('.js-adicionar').find('.codigo_setor').val('');
								este.parents('.js-adicionar').find('.codigo_cargo').val('');
								$('#loading_save').hide();								
							}         
						},
						complete: function(data){
							este.html(button);
							$('#loading_save').hide();
						}
					});
				}
			});
		});



	</script>