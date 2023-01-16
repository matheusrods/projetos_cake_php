<?php if (!empty($anexos)):?>
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>
	
	<div class="well">
        <div class='actionbar-right'>
			<? $codigo_fornecedor == "" ? null : $codigo_fornecedor ?>
            <?= $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => 'anexos', 'action' => 'listagem_reprovados/'.$codigo_fornecedor.'/1'), array('escape' => false, 'title' =>'Imprimir', 'target' => '_blank'));?>
        </div>
    </div>

	<table class="table table-striped">
		<thead>
		<tr>
			<th>Cód. do pedido</th>
			<th>Cód. credenciado</th>
			<th>Nome Fantasia</th>
			<th>Funcionário</th>
			<th>Unidade</th>
			<th>Exame</th>
			<th>Data realização exame</th>
			<th>Usuário anexo</th>
			<th>Data anexo</th>
			<th>Motivo</th>
			<th>Observacôes</th>
			<th>Ações</th>
		</tr>
		</thead>
		<tbody>
			<?php foreach($anexos as $anexos) :?>
				<tr>
					<td><?php echo $anexos['PedidoExame']['codigo'] ?></td>
					<td><?php echo $anexos['Fornecedor']['codigo'] ?></td>
					<td><?php echo $anexos['Fornecedor']['nome'] ?></td>
					<td><?php echo $anexos['Funcionario']['nome'] ?></td>
					<td><?php echo $anexos['Clientes']['nome_fantasia'] ?></td>
					<td><?php echo $anexos['Exame']['descricao'] ?></td>
					<td><?php echo $anexos['ItemPedidoExame']['data_realizacao_exame'] ?></td>
										
					<td><?php echo $anexos['Usuario']['nome'] ?></td>
					<td><?php echo $anexos['AuditoriaExame']['data_inclusao'] ?></td>
					<td><?php echo $anexos['TipoGlosas']['descricao'] ?></td>
					<td><?php echo $anexos['Glosas']['motivo_glosa'] ?></td>
					<td>
					
						<div class="modal-open icon-upload" data-modalname="modal_enexos"
						 data-codigo_pedido_exame="<?php echo $anexos['PedidoExame']['codigo']; ?>" 
						 data-codigo_cliente="<?php echo $anexos['PedidoExame']['codigo_cliente']; ?>" 
						 data-codigo_item_pedido_exame="<?php echo $anexos['ItemPedidoExame']['codigo']; ?>" 
						 data-codigo_exame="<?php echo $anexos['Exame']['codigo'];?>" 
						 data-codigo_status_auditoria_imagem="<?php echo $anexos['AuditoriaExame']['codigo_status_auditoria_imagem']; ?>"
						 data-caminho_arquivo="<?php echo base64_encode($anexos['AnexosExames']['caminho_arquivo']); ?>"
						 data-codigo_ficha_clinica="<?php echo $anexos['FichasClinicas']['codigo']; ?>"
						 data-caminho_ficha_clinica="<?php echo base64_encode($anexos['AnexosFichasClinicas']['caminho_arquivo']); ?>"
						 title="Submeter nova imagem"></div>

					</td>
					
					<!-- <td style="text-align: center">
						<?php echo $this->Html->link('', array('controller' => 'anexos', 'action' => 'reprovados', $cliente['Cliente']['codigo']), array('class' => 'icon-cog', 'title' => 'Visualizar'));?>
					</td> -->
				</tr>
			<?php endforeach; ?>
		</tbody>

		<tfoot>
            <tr>
                <td colspan = "12"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['PedidoExame']['count']; ?></td>
            </tr>
        </tfoot> 
	</table>

	<div class='row-fluid'>
        <div class='numbers span5'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
<?php else:?>
	<div class="alert">Nenhum dado foi encontrado!</div>
<?php endif;?>


<div id="modal_enexos" class="modal hide fade" rel="modal:open">
    
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Editar anexos</h3>
    </div>
    
    <div class="modal-body"> 
        
        <div id="modal_content"></div>      
		<div class="blockUI blockMsg blockElement" style="text-align: center;width:100%;height:50px;"><img src="/portal/img/loading.gif"></div>   
    </div>
    
    <div class="modal-footer">
        <a href="#" class="btn close_modal" data-dismiss="modal">Voltar</a>
        <div id="salvar_form"  class="btn btn-primary">Salvar Mudanças</div>
    </div>
</div>

<?php

	echo $this->Javascript->codeBlock("
	function atualizaListaRiscosTipo() {   
		var div = jQuery('div.lista');
		bloquearDiv(div);
		div.load(baseUrl + 'riscos_tipos/listagem/' + Math.random());
	}

	function atualizaStatusRiscosTipo(codigo)
	{

		$.ajax({
			type: 'POST',
			url: baseUrl + 'riscos_tipos/editar_status/' + codigo,
			beforeSend: function(){
				bloquearDivSemImg($('div.lista'));  
			},
			success: function(data){           
				if(data == 1){
					atualizaListaRiscosTipo();
					$('div.lista').unblock();
					viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
				} else {
					atualizaListaRiscosTipo();
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

	function fecharMsg()
	{
		setInterval(
			function(){
				$('div.message.container').css({ 'opacity': '0', 'display': 'none' });
			},
			4000
		);     
	}

	function gerarMensagem(css, mens)
	{
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
	");

	?>




<script>

	$(function(){

		$(".modal-open").on("click",function(){
			$("#modal_content").html('');
			$(".blockUI").show();
			var _this = $(this);                      // recupera info do objeto que esta sendo clicado
			var modalName = _this.data("modalname");  // recupera o nome da modal que será aberta
			var codigo_cliente = _this.data("codigo_cliente");  // recupera o codigo do cliente
			var codigo_pedido_exame = _this.data("codigo_pedido_exame");  // recupera o codigo do pedido_exame
			var codigo_exame = _this.data("codigo_exame");  // recupera o codigo do exame
			var codigo_item_pedido_exame = _this.data("codigo_item_pedido_exame");  // recupera o codigo do item pedido exame
			var codigo_status_auditoria_imagem = _this.data("codigo_status_auditoria_imagem");  // recupera o codigo_status_auditoria_imagem
			var caminho_arquivo = _this.data("caminho_arquivo");  // recupera o caminho_arquivo
			var codigo_ficha_clinica = _this.data("codigo_ficha_clinica");  // recupera o codigo_ficha_clinica
			var caminho_ficha_clinica = _this.data("caminho_ficha_clinica");  // recupera o caminho_ficha_clinica
			
			var modalObject = $("#"+modalName+"");

			jQuery("#upload-exame-images").empty();       
        	jQuery("#upload-ficha-images").empty();
        
			modalObject.css("z-index", "1050");
			modalObject.modal({backdrop: 'static', keyboard: false}); // Abre o modal

			jQuery("#modal_content").load(baseUrl + "anexos/modal_reprovados/" + codigo_item_pedido_exame + "/" + codigo_exame + "/" + codigo_status_auditoria_imagem + "/" + codigo_cliente + "/" + codigo_pedido_exame + "/" + caminho_arquivo + "/" + codigo_ficha_clinica + "/" + caminho_ficha_clinica + "/" + Math.random(), function(){
				$(".blockUI").hide();
			});			
		});

		$("#salvar_form").on("click", function(){
			//
			var frm = $('#AnexoExameModalReprovadosForm');
			var frmFicha = $('#AnexoFichaClinicaModalReprovadosForm');
			
			console.log("salvando....")
			console.log(frm.attr('method'))
			
			console.log(frm.attr('action'));
		
			bloquearDiv(frm);
			bloquearDiv(frmFicha);

			frm.submit();
			frmFicha.submit();

			window.setTimeout(function(){
				$('#modal_enexos').modal('hide');
				atualizaLista();   
			}, 2000);
		
			
		});		

		function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "anexos/listagem_reprovados/<?= $codigo_fornecedor?>/" + Math.random());
        }
	})

</script>