
<?php if(is_array($listagem) && count($listagem) >= 1) : ?>
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>

	<?php if($mensageria): ?>
		<div class='well'>
		    <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
		</div>
	<?php endif; ?>

    <div class="row-fluid inline">

		<?php echo $this->BForm->create('IntEsocialEventos', array('type' => 'post' ,'url' => array('controller' => 'mensageria_esocial','action' => 'integracao_eventos_all'))); ?>
    		<div class="row-fluid inline" style="text-align:right; ">
				<span id="div_salvar">
					<!-- <button class="btn btn-success btn-lg" id="button_submit"><i class="icon-upload icon-white"></i> Integrar Eventos</button> -->
				</span>
    		</div>
	    	
	        <table class="table table-striped social_id">
	            <thead>
	                <tr>
	                	<th >Nome Fantasia</th>
	                    <th >CPNJ</th>
	                    <th >Funcionário</th>
	                    <th >CPF</th>
	                    <th >Matricula</th>
	                    <th >Tipo Evento</th>
	                    <th >Codigo Registro</th>
	                    <th >Status</th>

	                    <th >Data Integração</th>
	                    <th >Recibo</th>
	                    <th >Retorno Esocial</th>
	                    <th >Data Retorno Esocial</th>
	                    <th class="acoes" style="width:75px">Ações</th>
	                </tr>
	            </thead>
	            <tbody>
	                <?php foreach ($listagem as $key => $linha): ?>               	
	                    <tr>
	                        <td class="input-mini"><?= $linha['Cliente']['nome_fantasia']; ?></td>
	                        <td><?= Comum::formatarDocumento($linha['Cliente']['codigo_documento']); ?></td>
	                        <td><?= $linha['Funcionario']['nome']; ?></td>
	                        <td><?= Comum::formatarDocumento($linha['Funcionario']['cpf']); ?></td>
	                        <td><?= $linha['ClienteFuncionario']['matricula']; ?></td>
	                        <td><?= $linha['IntEsocialTipoEvento']['descricao']; ?></td>
	                        <td><?php echo $linha['IntEsocialEventos']['codigo_registro_sistema']; ?></td>
	                        <td><?php echo $linha['IntEsocialStatus']['descricao']; ?></td>

	                        <td><?php echo $linha['IntEsocialEventos']['data_integracao']; ?></td>
	                        <td><?php echo $linha['IntEsocialEventos']['codigo_recibo']; ?></td>
	                        <td><?php echo $linha['IntEsocialEventos']['mensagem_retorno_integradora']; ?></td>
	                        <td><?php echo $linha['IntEsocialEventos']['data_retorno_integradora']; ?></td>
	                        <td>
	                        	<a href="getXmlEvento/<?php echo $linha['IntEsocialEventos']['codigo']; ?>" id="xml" title='Xml enviado ao Esocial' target="_blank" ><i id="xml" class="icon-download-alt"></i></a>
	                        	&nbsp;
	                        	<?php if($linha['IntEsocialEventos']['codigo_int_esocial_status'] == 4): ?>
                        			<a href="#void" id="expandir_1" onclick="ocorrencias(<?php echo $linha['IntEsocialEventos']['codigo'];?>,'1');" title='Ocorrências Retorno Esocial' ><i id="icone_1" class="icon-eye-open"></i></a>
                        		<?php endif; ?>

                        		&nbsp;
	                        	<?php if($linha['IntEsocialEventos']['codigo_int_esocial_status'] == 3 && $linha[0]['codigo_s3000'] == ''): ?>
                        			<a href="#void" id="exclusao" onclick="evento_s3000(<?php echo $linha['IntEsocialEventos']['codigo'];?>);" title='S-3000 Exclusão do Evento' ><i id="icone_s3000" class="icon-remove-circle"></i></a>
                        		<?php endif; ?>


                   			</td>
	                    </tr>
	                <?php endforeach; ?>        
	            </tbody>
	        	<tfoot>
		            <tr>
		                <td colspan = "20"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['IntEsocialEventos']['count']; ?></td>
		            </tr>
		        </tfoot>
		    </table>
		    <div class='row-fluid'>
		        <div class='numbers span6'>
		            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
		            <?php echo $this->Paginator->numbers(); ?>
		            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
		        </div>
		        <div class='counter span6'>
		            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
		            
		        </div>
		    </div>
    	<?php echo $this->BForm->end(); ?>
    </div>
 	
 	<div class="modal fade " style="width:900px; left: 37%;top: 15%;" id="modal_ocorrencia_esocial" data-backdrop="static"></div>

<?php echo $this->Js->writeBuffer(); ?>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>

<?php echo $this->Buonny->link_js('jquery.doubleScroll'); ?>


<script>
	$(document).ready(function(){

		$('.double-scroll').doubleScroll();

		$('#button_submit').on("click", function(e){
			e.preventDefault();      	     

	        retorno = false;  

			$(".social_id tbody tr :checkbox").each(function(){
				if ($(this).is(":checked")) {
					var codigo = $(this).attr("id");
					codigo = codigo.substring(11);
					if(codigo){					     			
						retorno = true;         
					}																
				}
			});
			
			if(retorno == false){
				swal('Erro!', 'Para integrar os eventos, é necessario selecionar pelo menos 1 da listagem. :)', 'error');
			} else {
				if ( $("#IntEsocialEventosListagemEventosForm").length ) {
					$("#IntEsocialEventosListagemEventosForm").submit();
				}
			}
		});//fim

        ocorrencias = function(codigo_esocial_evento,mostra) {
            if(mostra) {
                var div = jQuery("div#modal_ocorrencia_esocial");
                bloquearDiv(div);
                div.load(baseUrl + "mensageria_esocial/modal_ocorrencia_esocial/" + codigo_esocial_evento + "/" + Math.random());
        
                $("#modal_ocorrencia_esocial").css("z-index", "1050");
                $("#modal_ocorrencia_esocial").modal("show");

            } else {
                $(".modal").css("z-index", "-1");
                $("#modal_ocorrencia_esocial").modal("hide");
            }
        }

        //faz toda a lógica para enviar o evento s3000
        evento_s3000 = function(codigo_evento) {

        	swal({
			  title: "Confirmar que deseja excluir este RECIBO deste evento do E-Social?",
			  text: "Lembre-se, esta essa ação irá enviar um evento S-3000 para o E-Social excluindo o RECIBO!",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonColor: "#47B22C",
			  confirmButtonText: "Sim, concordo e estou CIENTE da ação!",
			  closeOnConfirm: true
			},
			function(){
			  
				var div = jQuery(".lista");
			 	bloquearDiv(div);

			 	//envia via ajax o evento s3000
				$.ajax({
					url: baseUrl + 'mensageria_esocial/setIntegS3000',
					type: 'POST',
					dataType: 'json',
					data: {
						"codigo_evento": codigo_evento
					}
				})
				.done(function(data) {

					// console.log(data);

					if(data.retorno == 'false') {
						swal({
							type: 'warning',
							title: 'Atenção',
							text: data.mensagem,
						});
						

					} else {
						swal({type: 'success', title: 'Sucesso', text: 'Ir para a tela de integração para acompanhar sua evolução!'});
					}

					// desbloquearDiv(div);
		           	div.load(baseUrl + "mensageria_esocial/listagem_eventos/" + Math.random());

				});				
			
			});

        }//fim evento_s3000


	});

	function mostra_botao(element) {
		if($(element).val()) {
			$("#botao").show();
		} else {
			$("#botao").hide();
		}
	}
	//se ele marca todos ou desmarcar pra otimizar
	$('body').on('change', '.all', function() {
  		$('.esocial_codigo').find('input[type="checkbox"]').prop('checked', this.checked);
	});
</script>