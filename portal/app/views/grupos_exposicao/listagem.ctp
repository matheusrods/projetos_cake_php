<?php if (!empty($grupos_exposicao)) : ?>
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th class="input-small">Codigo</th>
				<th class="input-xlarge">Setor</th>
				<th class="input-xlarge">Cargo</th>
				<th class="input-xlarge">Grupo Homogêneo</th>
				<th class="input-xlarge">Funcionário</th>
				<th class="acoes" style="width:75px">Ações</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($grupos_exposicao as $dados) : ?>
				<tr>
					<td class="input-small"><?php echo $dados['GrupoExposicao']['codigo']; ?></td>
					<td class="input-xlarge"><?php echo $dados['Setor']['descricao']; ?></td>
					<td class="input-xlarge"><?php echo $dados['Cargo']['descricao']; ?></td>
					<td class="input-medium"><?php echo empty($dados['GrupoHomogeneo']['descricao']) ? '' : $dados['GrupoHomogeneo']['descricao']; ?></td>
					<td class="input-medium"><?php echo empty($dados['Funcionario']['nome']) ? '' : $dados['Funcionario']['nome']; ?></td>
					<td>
						<?php echo $this->Html->link('', array('action' => 'editar', $dados['ClienteSetor']['codigo_cliente_alocacao'], $dados['GrupoExposicao']['codigo']), array('class' => 'icon-edit ', 'data-toggle' => 'tooltip', 'title' => 'Editar')); ?> &nbsp;

						<?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'data-toggle' => 'tooltip', 'title' => 'Excluir', 'onclick' => 'excluirGrupoExposicao(' . $dados['ClienteSetor']['codigo_cliente_alocacao'] . ',' . $dados['GrupoExposicao']['codigo'] . ')')) ?>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['GrupoExposicao']['count']; ?></td>
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
<?php else : ?>
	<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>
<div class='form-actions well'>
	<?php if (!is_null($ordemServico) && isset($ordemServico['OrdemServico']['status_ordem_servico']) && $ordemServico['OrdemServico']['status_ordem_servico'] != 3 && $ordemServico['OrdemServico']['status_ordem_servico'] != 5) { ?>

		<?php echo $html->link('Concluir', '#', array('data-codigo' => $this->data['Unidade']['codigo'], 'class' => 'js-concluir-processo btn btn-primary')); ?>
		<?php //} elseif(!is_null($ordemServico) && isset($ordemServico['OrdemServico']['status_ordem_servico']) && $ordemServico['OrdemServico']['status_ordem_servico'] == 3) { 
		?>
		<?php //echo $html->link('Desfazer', array('controller' => 'clientes_implantacao','action' => 'atualiza_status_ppra',  $this->data['Unidade']['codigo'], 1), array('class' => 'btn btn-danger pull-left margin-right-10', 'data-toggle' => 'tooltip', 'title' => 'Cancela o status de concluído.')); 
		?>

		<?php //echo $this->BForm->submit('Concluído', array('type' => 'button', 'class' => 'btn btn-primary pull-left margin-right-10', 'data-toggle' => 'tooltip', 'title' => 'O processo já está concluído', 'disabled' => true)); 		
		?>

	<?php } elseif (!is_null($ordemServico) && isset($ordemServico['OrdemServico']['status_ordem_servico']) && $ordemServico['OrdemServico']['status_ordem_servico'] == 5) { ?>

		<?php echo $html->link('Desfazer', array('controller' => 'clientes_implantacao', 'action' => 'atualiza_status_ppra',  $this->data['Unidade']['codigo'], 1), array('class' => 'btn btn-danger pull-left margin-right-10', 'data-toggle' => 'tooltip', 'title' => 'Cancela o status de concluído.')); ?>

		<?php echo $html->link('Finalizar Processo', '#', array('data-codigo' => $this->data['Unidade']['codigo'], 'class' => 'js-finalizar-processo btn btn-success')); ?>

	<?php } elseif (!is_null($ordemServico) && isset($ordemServico['OrdemServico']['status_ordem_servico']) && $ordemServico['OrdemServico']['status_ordem_servico'] != 3) { ?>
		<?php 
			// PD-154
			$Configuracao = &ClassRegistry::init('Configuracao');
			$codigo_servico_ppra = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PPRA');
			
			echo $this->Html->link('Localizar Credenciado', array('controller' => 'clientes_implantacao', 'action' => 'localizar_credenciado', $codigo_cliente, $codigo_servico_ppra), array('class' => 'btn')); ?>
	<?php } ?>

	<?php echo $html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'gerenciar_ppra', $this->data['Matriz']['codigo']), array('class' => 'btn')); ?>

	<?php if (!is_null($ordemServico) && isset($ordemServico['OrdemServico']['status_ordem_servico']) && $ordemServico['OrdemServico']['status_ordem_servico'] == 5) { ?>
		<?php echo $html->link('Preencher com ausência de risco', '#', array('data-href' => Router::url(array('controller' => 'grupos_exposicao', 'action' => 'preenche_com_ausencia_risco', $codigo_cliente)), 'class' => 'btn btn-warning pull-right submit-load', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Preenche automaticamente os grupos de exposição que não possuem nenhum risco cadastrado com Ausência de Risco.')); ?>
	<?php } ?>
</div>

<div id="myModal" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Concluir PGR</h3>
	</div>
	<div class="modal-body">

		<div class="row-fluid">
			<div class="control-group span9 ">
				<label for="inicio_vigencia_pcmso">Insira a data de início de vigência:</label>
				<input type="text" id="inicio_vigencia_ppra" class="data input-small required" name="inicio_vigencia_ppra">
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group span9 ">
				<label for="vigencia_em_meses">Selecione a vigência do contrato (em meses):</label>
				<select id="vigencia_em_meses" name="vigencia_em_meses" class="input-small required">
					<option value="">Selecione</option>
					<option value="3">3</option>
					<option value="6">6</option>
					<option value="9">9</option>
					<option value="12">12</option>
					<option value="24">24</option>
				</select>
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group span12">
				<?php echo $this->BForm->input('codigo_medico', array('label' => 'Selecione um Profissional responsável:', 'empty' => 'Selecione', 'options' => $profissionais)); ?>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
		<a href="#" class="js-salvar btn btn-primary">Salvar</a>
	</div>
</div>
<?php echo $this->Js->writeBuffer(); ?>
<?php
echo $this->Javascript->codeBlock("
	$(document).ready(function() {

		setup_datepicker();
        $('.js-finalizar-processo').click(function(event) {
			window.location = baseUrl + 'clientes_implantacao/atualiza_status_ppra_versionamento/" . $this->data['Unidade']['codigo'] . "/3';
        });//FINAL CLICK js-finalizar-processo

        $('.js-concluir-processo').click(function(event) {
        	
            $('input#codigo').val($(this).attr('data-codigo'));
            $('#myModal').modal('show');

            $('.js-salvar').click(function(event) {
                var execute = true;
                //console.log($('#inicio_vigencia_ppra').val().replace(/\//g, '-'));
                $(this).parents('#myModal').find('input, select').each(function(index, val) {
                    if(val.value == '') {
                        $(this).css({borderColor: 'red'});
                        execute = false;
                    } else {
                       $(this).removeAttr('style');
                   }
               });   
                if(execute) {
                    window.location = baseUrl + 'clientes_implantacao/atualiza_status_ppra_concluido/" . $this->data['Unidade']['codigo'] . "/5/' + $('#inicio_vigencia_ppra').val().replace(/\//g, '-') + '/' + $('#vigencia_em_meses').val()+ '/' + $('#codigo_medico').val();
                }
            });
        });//FINAL CLICK js-concluir-processo

		$('[data-toggle=\"tooltip\"]').tooltip();
		 $('.submit-load').click(function(event) {
                var este = $(this);
                var width = este.width();
                var link = este.attr('data-href');
                swal({
                    type: 'warning',
                    title: 'Atenção',
                    text: 'Tem certeza que deseja preencher todos os cargos e setores sem risco com ausência de risco? Este processo poderá criar novos grupos de exposição automaticamente.',
                    showCancelButton: true,
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Cancelar',
                    closeOnConfirm: false,
                    confirmButtonColor: '#5783db',
                    showLoaderOnConfirm: true
                }, function() {
                    este.css({'width': width, cursor: 'wait'}).removeAttr('onclick').html($('<img>', {src: baseUrl + 'img/loading.gif'}));
                    window.location.href = link;
                });
            });		
	});
	function atualizaStatus(codigo, status, codigo_cliente){
		$.ajax({
			type: 'POST',
			url: baseUrl + 'grupos_exposicao/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
			beforeSend: function(){
				bloquearDivSemImg($('div.lista'));  
			},
			success: function(data){
				if(data == 1){
					atualizaLista(codigo_cliente);
					$('div.lista').unblock();
					viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
				} else {
					atualizaLista(codigo_cliente);
					$('div.lista').unblock();
					viewMensagem(0,'Não foi possível mudar o status!');
				}
			},
			error: function(erro){
				$('div.lista').unblock();
				viewMensagem(0,'Não foi possível mudar o status!');
			}
		});
	}

	function fecharMsg(){
		setInterval(
		function(){
			$('div.message.container').css({ 'opacity': '0', 'display': 'none' });
		},
		4000
		);     
	}

	function gerarMensagem(css, mens){
		$('div.message.container').css({ 'opacity': '1', 'display': 'block' });
		$('div.message.container').html('<div class=\"alert alert-'+css+'\"><p>'+mens+'</p></div>');
		fecharMsg();
	}

	function viewMensagem(tipo, mensagem){
		switch(tipo){
			case 1:
			gerarMensagem('success',mensagem);
			break;
			case 2:
			gerarMensagem('success',mensagem);
			break;
			default:
			gerarMensagem('error',mensagem);
			break;
		}    
	} 

	function atualizaLista(codigo_cliente) {
		var div = jQuery('div.lista');
		bloquearDiv(div);
		div.load(baseUrl + 'grupos_exposicao/listagem/'+ codigo_cliente +'/' + Math.random());
	}

	function excluirGrupoExposicao(codigo_cliente, codigo){
		if (confirm('Deseja realmente excluir ?')){
			$.ajax({
				type: 'POST',        
				url: baseUrl + 'grupos_exposicao/excluir/' + codigo +  '/' + Math.random(),        
				dataType : 'json',
				success : function(data){ 
					if(data == 1){
						atualizaLista(codigo_cliente);
					}
					else{
						alert('Não foi possível excluir, tente novamente.');
					}
				},
				error : function(error){
					console.log(error);
				}
			}); 
		}
		return false;
	}
	");
?>

<?php if (!empty($visualizar_gge) && $visualizar_gge) : ?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery(".icon-trash, .js-concluir-processo").remove();
			jQuery(".icon-edit").attr("data-original-title", "Visualizar");
			jQuery(".icon-edit").addClass("icon-eye-open").removeClass("icon-edit");
		});
	</script>
<?php endif; ?>