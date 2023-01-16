	
<?php if( !empty($pedidos) ): ?>

	<?php echo $this->BForm->create('RemessaBancariaPedidos', array('url' => array('controller' => 'remessa_bancaria','action' => 'gerar_remessa'))); ?>

		<div class='actionbar-right'>
			<div class='actionbar-right'>
	            <?php echo $this->BForm->button('<i class="icon-plus icon-white"></i> Gerar Remessa', array('id' => 'gerar_remessa','type'=>'button','div' => false, 'class' => 'btn btn-success', 'onclick' => 'gerarRemessa()','title' =>'Gerar Remessa')); ?>
			</div>
		</div>

		<table class='table'>
			<thead>
				<th class='action-icon'><?php echo $this->BForm->input('todos_select',array('type'=>'checkbox','value'=>'S', 'class' => 'input-large', 'label' => 'Todos', 'onchange' => 'marcardesmarcar()')) ?></th>
				<th>Pedido</th>
				<th>Mês</th>
				<th>Ano</th>
				<th>Cond.Pgto</th>
				<th>Dt.Inserção</th>
				<th class='input-small numeric'>Total</th>
			</thead>
			<tbody>			
				<?php foreach($pedidos as $key => $value):?>
					<tr>
						<td class='action-icon'>
							<?php echo $this->BForm->input($value['Pedido']['codigo'],array('type'=>'checkbox','value'=>'S', 'class' => 'input-large marcar', 'label' => '')) ?>
						</td>
						<td><?php echo $value['Pedido']['codigo']; ?></td>
						<td><?php echo $meses[$value['Pedido']['mes_referencia']]; ?></td>
						<td><?php echo $value['Pedido']['ano_referencia']; ?></td>
						<td><?php echo strtoupper($value['CondPag']['descricao']); ?></td>
						<td><?php echo $value['Pedido']['data_inclusao']; ?></td>
						<td class='input-small numeric'><?php echo $this->Buonny->moeda($value[0]['valor_total']); ?></td>
					</tr>
				<?php endforeach; ?>
				
			</tbody>
			
		</table>

		<div class="modal fade" id="modal_carregando">
			<div class="modal-dialog modal-sm" style="position: static;">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="gridSystemModalLabel">Aguarde, carregando informações...</h4>
					</div>
					<div class="modal-body">
						<img src="/portal/img/ajax-loader.gif" style="padding: 10px;">
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modal_selecao_data" data-backdrop="static">
			<div class="modal-dialog modal-lg" style="position: static;">
				<div class="modal-content">
					<div class="modal-header">
						<div class="msg_error" style="position: absolute;display: none;">
							<div class="alert alert-error">
								<p>Não foi possível gravar os dados, verifique os erros abaixo, os campos com ( * ) deve ser preenchidos!!!</p>
							</div>
						</div>			
						<h4 class="modal-title" id="gridSystemModalLabel">Gerar Remessa</h4>
					</div>
					<div class="modal-body" style="max-height: 100%;">
						<div class="row-fluid">
							<div class="span12">
								<div style="padding-top: 10px;">
									<?php echo $this->BForm->input('data_vencimento', array('label' => 'Data da <span id="data">vencimento</span> <span class="pull-right margin-right-20"><i class="icon-question-sign" data-toggle="tooltip" title="Insira a data."></i></span>', 'class' => 'data', 'value' => date('d/m/Y'))) ?>
									<div style="clear: both;"></div>
									<hr />
								</div>
							</div>
						</div>
						<div class="form-actions center" id="rodape_botoes">
							<a href="/portal/remessa_bancaria/" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-fast-backward"></i> Voltar</a>

							<a href="javascript:void(0);" onclick="envia_dados();" class="btn btn-success btn-lg" id="botao-avancar">
								<i class="glyphicon glyphicon-share"></i> Avançar
							</a>	
						</div>				
					</div>
				</div>
			</div>
		</div>			

	<?php echo $this->BForm->end();?>	
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>  




    <?php 
echo $this->Javascript->codeBlock("

	jQuery(document).ready(function() {
		setup_mascaras();
		setup_datepicker();
	});

    function atualizaLista() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'remessa_bancaria/listagem/' + Math.random());
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

    function marcardesmarcar(){ 
	    $('.marcar').each(function() {
	   		
			if ($(this).prop('checked')) {
			    $(this).prop('checked', false);
			} else {
			    $(this).prop('checked', true);
			}

	    });
	}


    function gerarRemessa()
    {
    	var erro = 1;

    	//verifica se tem algum pedido selecionado
    	$('.marcar').each(function() {
			if ($(this).is(':checked')) {
				erro = 0;
		    	modal_gerar_remessa();
    		}
    	});

		if(erro == 1) {
			swal({
				type: 'warning',
				title: 'Atenção',
				text: 'É necessário selecionar um pedido para Gerar a Remessa?',				
			});

		}//fim erro

    } //fim gerarRemessa

    //apresenta modal
    function modal_gerar_remessa()
    {
    	$('.modal').css('z-index', '-1');
		
		$('#modal_selecao_data').css('z-index', '1050');
		$('#modal_selecao_data').modal('show');
    	
    }//fim modal_gerar_remessa

    function envia_dados()
    {
    	//envia para geracao do arquivo de remessa_bancaria
   		$('#RemessaBancariaPedidosListagemForm').submit();
    }

");
?>

