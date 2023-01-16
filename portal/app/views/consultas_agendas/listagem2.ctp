<?php 
// debug($agenda);
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>

<?php if(!empty($agenda)):?>

	<?php if ($permite_export === true):  ?>
		<div class='well'>
			<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'destino','export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
		</div>
	<?php endif; ?>

<table class="table table-striped" style='width:1800px;max-width:none;'>
	<thead>
		<tr>
			<th style="width:120px;">Ações</th>
			<th style="width:35px;">Número Pedido</th>
			<th class="input-mini">Data Agendamento</th>
			<th class="input-mini">Horário Agendamento</th>			
			<th class="input-medium">Nome Fantasia</th>
			<th class="input-medium">Funcionário</th>
			<th class="input-large">Prestador</th>
			<th class="input-mini">Tipo de exame</th>
			<th class="input-medium">Exame</th>
			<th class="input-mini">Data Emissão</th>
			<th class="input-mini">Status</th>
			<th class="input-mini">Data conclusão de Exame</th>
			<th class="input-mini">Data Baixa</th>
			<th class="input-mini">Usuário Responsável Baixa</th>
			<th class="input-mini">Usuário Responsável Emissão Pedido</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($agenda as $dados): ?>
			<tr>
				<td>
	            	<?php if( in_array( $_SESSION['Auth']['Usuario']['codigo_uperfil'], $permissoes_acoes['editar_datas'][0] ) ): ?>
	            		<a href="javascript:void(0);" onclick="editar_realizacao_datas('<?php echo $dados['ItemPedidoExame']['codigo']; ?>', 1);"><i class="icon-edit" title="Atualizar Datas do Exame"></i></a>
	            	<?php endif; ?>

	            	<?php if( in_array( $_SESSION['Auth']['Usuario']['codigo_uperfil'], $permissoes_acoes['editar_recebimento'][0] ) ): ?>
	            		<a href="javascript:void(0);" onclick="editar_realizacao_recebimento('<?php echo $dados['ItemPedidoExame']['codigo']; ?>', 1);"><i class="icon-wrench" title="Atualizar Recebimento do Exame"></i></a>
	            	<?php endif; ?>

	            	<!-- Ajuste feito na pc-2707 bloquear imagens-->
	            	<?php //if(!$bloqueia_anexo): ?>

	            		<!-- ajuste feito para o CDCT-169 para que o usuario de cliente possa ver o anexo de exame -->
	            		<?php if(!empty($_SESSION['Auth']['Usuario']['codigo_uperfil']) && !empty($permissoes_acoes['anexar'][0]) OR !empty($_SESSION['Auth']['Usuario']['codigo_cliente'])): ?>	       

		            		<?php if( empty($dados['AnexoExame']['codigo']) ): ?>
		            			<!-- so deixara incluir ou visualizar anexo se tiver baixa no exame, ajuste CDCT-283  -->
	            				<?php
												if( !empty($dados['ItemPedidoExameBaixa']['data_inclusao']) || $dados['Exame']['anexo_nao_comparecimento'] == 1 ):
											?> 
			            			<a href="javascript:void(0);" onclick="listagem_anexo_exames('<?php echo $dados['ItemPedidoExame']['codigo']; ?>', 1);"><i class="icon-upload" title="Anexo de Exame"></i></a>
			            		<?php endif; ?>
							<?php elseif((!empty($dados['AnexoExame']['codigo'])) && ($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == null || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 1 || (($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 4 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 5 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 6) && $dados['AnexoExame']['aprovado_auditoria'] == null))): ?>
								<a><i class="icon-file waiting"  title="Aguardando Auditoria de Imagem"></i></a>
							<?php elseif((!empty($dados['AnexoExame']['codigo'])) && ($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 2) && $dados['AuditoriaExame']['libera_anexo_exame']==0): ?>
								<a><i class="icon-file danger"  title="<?php echo $dados['TipoGlosas']['visualizacao_do_cliente']; //old PC-3181 "Imagem reprovada - Aguardando Ajuste" ?>"></i></a>
		                	<?php elseif($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 3 || (($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 4 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 6 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 5) && $dados['AnexoExame']['aprovado_auditoria'] != null) || ( $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 2 && $dados['AuditoriaExame']['libera_anexo_exame']== 1)):?>
								<?php if($dados['AnexoExame']['codigo'] != ''):  ?>
									<?php 
										$caminho_arquivo = '/files/anexos_exames/'.$dados['AnexoExame']['caminho_arquivo'];
										//quando tiver no fileserver
										if(strstr($dados['AnexoExame']['caminho_arquivo'],'https://api.rhhealth.com.br')) {
											$caminho_arquivo = $dados['AnexoExame']['caminho_arquivo'];
										}

										echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $caminho_arquivo, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do exame')); 
									?>
								<?php endif;  ?>
		                	<?php endif; ?>
	                	<?php endif; ?>

	                <?php //endif; ?>

		            <a href="javascript:void(0);" onclick="window_log('<?php echo $dados['PedidoExame']['codigo']; ?>', '<?php echo $dados['ItemPedidoExame']['codigo']; ?>');"><i class="icon-eye-open" title="Log do Registro"></i></a>

                    <?php if(is_null($dados['ItemPedidoExameRecusado']['codigo']) && $dados['PedidoExame']['exame_demissional'] == 1) : ?>
                        <a href="#" class="icon-remove-circle" title="Recusa de Exame" onclick="return fnc_modal_recusa_pedido_exame('<?=$dados['PedidoExame']['codigo']?>', '<?=substr($dados['Exame']['descricao'],0, 30)?>', '<?=$dados['ItemPedidoExame']['codigo']?>');"></a>
                    <?php endif; ?>

					<!-- imprimir exames -->
						<a href="javascript:void(0)" data-codigo-cf="<?php echo $dados['PedidoExame']['codigo_cliente_funcionario']; ?>" data-codigo-fornecedor="<?php echo $dados['Fornecedor']['codigo']; ?>" data-codigo-pedido="<?php echo $dados['PedidoExame']['codigo']; ?>" class="icon-print linha-imprimir" title='Kit de Atendimento'></a>
						<br>
                											

	            	<?php if($dados['0']['codigo_aso'] == $dados['Exame']['codigo']): ?>						
								
	            		<?php if(is_null($dados['FichaClinica']['codigo'])): ?>

							<!-- inlcuir ficha clinica-->
							<a href="/portal/fichas_clinicas/incluir/<?php echo $dados['PedidoExame']['codigo']; ?>/agenda" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: 3px -69px; background-color: #33CCFF;" class="icon-pencil" title="Incluir Ficha Clinica" ></a>

						<?php else: ?>
							
							<!-- editar ficha clinica -->
	            			<a href="/portal/fichas_clinicas/editar/<?php echo $dados['FichaClinica']['codigo']; ?>/agenda" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: -93px -69px; background-color: #33CCFF;" class="icon-edit" title="Editar Ficha Clinica" ></a>

							<!-- imprimir ficha clinica -->
										<a href="/portal/fichas_clinicas/imprimir_relatorio/<?php echo $dados['FichaClinica']['codigo']; ?>/<?php echo $dados['PedidoExame']['codigo']; ?>/<?php echo $dados['Funcionario']['codigo']; ?>" target="_blank" class="icon-print" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: -93px -45px; background-color: #33CCFF; " title='Ficha Clinica Preenchida'></a>		            		

		            		<!-- Ajuste feito na pc-2707 bloquear imagens-->
	            			<?php //if(!$bloqueia_anexo): ?>

			            		<?php if( in_array( $_SESSION['Auth']['Usuario']['codigo_uperfil'], $permissoes_acoes['anexar'][0] ) ): ?>
				            		<?php if( empty($dados['AnexoFichaClinica']['codigo']) ): ?>
				            			<a href="javascript:void(0);" onclick="listagem_anexo_ficha_clinica('<?php echo $dados['ItemPedidoExame']['codigo']; ?>', 1);"><i class="icon-upload" style="border: 1px solid; text-decoration: none; border-radius: 100%; background-position: -141px -21px; padding: 3px; background-color: #33CCFF;" title="Anexo de Ficha Clínica"></i></a>
									<?php elseif((!empty($dados['AnexoFichaClinica']['codigo'])) && ($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == null || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 1 || (($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 4 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 5 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 6) && $dados['AnexoFichaClinica']['aprovado_auditoria'] == null))): ?>
										<a><i class="icon-file waiting"  title="Aguardando Auditoria de Imagem"></i></a>

									<?php elseif($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 3 || (($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 4 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 6 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 5) && $dados['AnexoFichaClinica']['aprovado_auditoria'] != null) || ( $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 2 && $dados['AuditoriaExame']['libera_anexo_ficha']== 1)):?>
										<?php $caminho_arquivo = '/files/anexos_exames/'.$dados['AnexoFichaClinica']['caminho_arquivo'];
										//quando tiver no fileserver
										if(strstr($dados['AnexoFichaClinica']['caminho_arquivo'],'https://api.rhhealth.com.br')) {
											$caminho_arquivo = $dados['AnexoFichaClinica']['caminho_arquivo'];
										}

										echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo','style'=> 'border: 1px solid; text-decoration: none; border-radius: 100%; background-position: -20px -21px; padding: 3px; background-color: #33CCFF;')), $caminho_arquivo, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo de Ficha Clínica')) ?>
									<?php elseif((!empty($dados['AnexoFichaClinica']['codigo'])) && ($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 2 && $dados['AuditoriaExame']['libera_anexo_ficha']==0) ): ?>
										<a><i class="icon-file danger"  title="<?php echo $dados['TipoGlosas']['visualizacao_do_cliente']; //old PC-3181 "Imagem reprovada - Aguardando Ajuste" ?>"></i></a>
									<?php endif; ?>
								<?php endif; ?>
							<?php //endif; ?>

						<?php endif; ?>

	            	<?php endif;?>

					<?php 
					 $Configuracao = &ClassRegistry::init('Configuracao'); 
					if ($dados['Exame']['codigo'] == $Configuracao->getChave('FICHA_PSICOSSOCIAL')) :?>

						<?php 
							
							if (isset($dados['FichaPsicossocial']['codigo']) && !empty($dados['FichaPsicossocial']['codigo'])) {
						?>
							<a href="/portal/ficha_psicossocial/editar/<?php echo $dados['PedidoExame']['codigo']; ?>/<?php echo $dados['FichaPsicossocial']['codigo']; ?>/consulta_agendada" target="_blank" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: -93px -69px; background-color: #a8ed8c;" class="icon-edit" title="Editar Ficha Psicossocial" ></a>
							<a href="/portal/ficha_psicossocial/imprimir_relatorio/<?php echo $dados['FichaPsicossocial']['codigo']; ?>" data-toggle="tooltip" title="Av. Psicossocial Preenchida" class="icon-print" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: -93px -45px; background-color: #a8ed8c; " data-original-title="Av. Psicossocial Preenchida"></a>					
						<?php
							} else {
						?>
							<a href="/portal/ficha_psicossocial/incluir/<?php echo $dados['PedidoExame']['codigo']; ?>/consulta_agendada" target="_blank" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: 3px -69px; background-color: #a8ed8c;" class="icon-pencil" title="Incluir Ficha Psicossocial" ></a>
						<?php		
							}							
						?>

					<?php endif;?>	

                    <?php if(!is_null($dados['ItemPedidoExameRecusado']['codigo'])) : ?>
                        <a href="#" onclick="return fnc_modal_recusa_pedido_exame_exibe('<?=$dados['ItemPedidoExameRecusado']['codigo']?>', '<?=$dados['PedidoExame']['codigo']?>', '<?=substr($dados['Exame']['descricao'],0, 30)?>');" style="border: 1px solid #990000; text-decoration: none; border-radius: 100%; padding: 3px; background-position: -166px -93px; background-color: #ff9999;" class="icon-remove-circle" title="Recusa de Exame"></a>
                    <?php endif; ?>

	            	<?php if($dados['0']['codigo_audiometrico'] == $dados['Exame']['codigo']): ?>

						<?php if(is_null($dados['Audiometria']['codigo'])): ?>

	            			<!-- incluir audiometria -->
	            			<a href="/portal/audiometrias/incluir/<?php echo $dados['ItemPedidoExame']['codigo']; ?>/agenda" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: 3px -69px; background-color: #FF9933; " class="icon-pencil" title="Incluir Audiometria" ></a>
	            		
	            		<?php else: ?>

	            			<!-- editar audiometria -->
	            			<a href="/portal/audiometrias/editar/<?php echo $dados['Audiometria']['codigo']; ?>/agenda" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: -93px -69px; background-color: #FF9933; " class="icon-edit" title="Editar Audiometria" ></a>

		            		<!-- imprimir audiometria -->
										<a href="/portal/audiometrias/imprimir_relatorio/<?php echo $dados['Audiometria']['codigo']; ?>" target="_blank" class="icon-print" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: -93px -45px; background-color: #FF9933; " title='Audiometria Ocupacional Preenchida'></a>		            		

	            		<?php endif;?>

	            	<?php endif;?>

	            	<?php 
	            		//codigos para apresentação da fichas assistenciais
	            		$codigos_exames = explode(',',$dados['0']['codigos_ficha_assistencial']);
	            		if(in_array($dados['Exame']['codigo'], $codigos_exames)): 
	            	?>
	            		
						<?php if(is_null($dados['FichaAssistencial']['codigo'])): ?>

	            			<!-- incluir audiometria -->
	            			<a href="/portal/fichas_assistenciais/incluir/<?php echo $dados['PedidoExame']['codigo']; ?>/agenda" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: 3px -69px;" class="icon-pencil" title="Incluir Ficha Assistencial" ></a>
	            		
	            		<?php else: ?>

	            			<!-- editar audiometria -->
	            			<a href="/portal/fichas_assistenciais/editar/<?php echo $dados['FichaAssistencial']['codigo']; ?>/agenda" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: -93px -69px;  " class="icon-edit" title="Editar Ficha Assistencial" ></a>

		            		<!-- imprimir fichas_assistenciais -->
		            		<a href="/portal/fichas_assistenciais/imprimir_ficha_assistencial/<?php echo $dados['FichaAssistencial']['codigo']; ?>/<?php echo $dados['PedidoExame']['codigo']; ?>/<?php echo $dados['Funcionario']['codigo']?>" target="_blank" class="icon-print" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: -93px -45px; " title='Imprimir Ficha Assistencial'></a>

			            	<?php if($dados['FichaAssistencialResposta']['resposta'] == 1) : ?>
			            		<!-- imprimir receita medica -->
			            		<a href="/portal/fichas_assistenciais/imprimir_receita_medica/<?php echo $dados['FichaAssistencial']['codigo']; ?>/<?php echo $dados['PedidoExame']['codigo']; ?>/<?php echo $dados['Funcionario']['codigo']?>" target="_blank" class="icon-print" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: -93px -45px; background-color: #3CB371; " title='Imprimir Receita Médica'></a>
			            	<?php endif;?>

			            	<?php if($dados['Atestado']['exibir_ficha_assistencial'] == 1) : ?>
			            	
			            		<!-- imprimir atestado medico -->
			            		<a href="/portal/fichas_assistenciais/imprimir_atestado_medico/<?php echo $dados['FichaAssistencial']['codigo']; ?>/<?php echo $dados['PedidoExame']['codigo']; ?>/<?php echo $dados['Funcionario']['codigo']?>" target="_blank" class="icon-print" style="border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: -93px -45px; background-color: #E9967A; " title='Imprimir Atestado Médico'></a>
			            	<?php endif;?>
		            		
	            		<?php endif;?>

	            	<?php endif;?>

	            	<?php if(empty($dados['ItemPedidoExameBaixa']['data_inclusao'])): ?>
	            		<?php if($dados['ItemPedidoExame']['tipo_atendimento'] != 0): ?>
	            			<?php echo $this->Html->link('<i class="icon-calendar"></i>', array('controller' => 'pedidos_exames', 'action' => 'agendamento_grupo',$dados[0]['codigo_grupo_economico'], $dados['PedidoExame']['codigo'], 'reagendamento'), array('escape' => false, 'title' =>'Reagendar')); ?>
	            		<?php endif; ?>
	            	<?php endif; ?>

	            </td>
	        	<td><?php echo $this->Buonny->modal_pedidos_exames($this, $dados['PedidoExame']['codigo'],  'modal_agendamento');?></td>
						<td class="input-mini">
							<?php 
							
							if(!empty($dados['ItemPedidoExame']['data_agendamento'])) {

								echo $dados['ItemPedidoExame']['data_agendamento'];
							}
							?>
						</td>
						<td>
							<?php
								if(!empty($dados['AgendamentoExame']['hora'])) {

									$tmpHoraExame = str_pad($dados['AgendamentoExame']['hora'], 4, '0', STR_PAD_LEFT);
									$tmpHoraExame = substr($tmpHoraExame, 0, 2) . ':' . substr($tmpHoraExame, 2, 2);

									echo $tmpHoraExame;
								}		
								else {
									echo "Ordem de Chegada";							
								}			
							?>					
						</td>							        	
	       		<td><?php echo $this->Buonny->leiamais($dados['ClienteUnidade']['nome_fantasia'],22) ?></td>
	       		<td><?php echo $buonny->documento($dados['Funcionario']['nome']) ?></td>
	       		<td><?php echo $this->Text->truncate($dados['Fornecedor']['razao_social'], 40, array('ellipsis' => '...', 'exact' => false)); ?></td>
	            <td><?php echo $dados['PedidoExame']['tipo_exame']; ?></td>
				<td><?php echo $this->Buonny->leiamais($dados['Exame']['descricao'],30); ?></td>
				<td><?php echo $dados['PedidoExame']['data_solicitacao']; ?></td>

	         

				<td><?php echo $dados[0]['Exames_status'] ?></td>
				<td class="input-mini"><?php echo $dados['ItemPedidoExameBaixa']['data_realizacao_exame'] ?></td>
				<td class="input-mini"><?php echo $dados['ItemPedidoExameBaixa']['data_inclusao'] ?></td>
				<td><?php echo $dados['UsuarioBaixa']['apelido'] ?></td>
				<td><?php echo $dados['PedidoExame']['usuario_resp'] ?></td>				
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<div class="modal fade" id="modal_realizacao" data-backdrop="static"></div>

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
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<!-- Modal -->
<div id="modal_recusa_exame" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal_recusa_exame_label" aria-hidden="true" >
    <form>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="modal_recusa_exame_label">Motivo Recusa Exame</span></h3>
        </div>
        <div class="modal-body">
            <input type="hidden" name="data[ItemPedidoExameRecusado][codigo_item_pedido_exame]" />
            <div><strong>Nº Pedido: </strong><br /><span></span></div>
            <div><strong>Exame: </strong><br /><span></span></div>
            <?php echo $this->BForm->input('ItemPedidoExameRecusado.codigo_motivo_recusa_exame', array('type' => 'select', 'class' => 'input-xxlarge', 'empty' => 'Selecione..', 'label' => '<strong>* Motivo Recusa Exame:</strong>', 'options' => $motivos_recusas_exames)); ?>
            <?php echo $this->BForm->input('ItemPedidoExameRecusado.descricao', array('type' => 'textarea', 'class' => 'input-xxlarge', 'label' => '<strong>Descrição:</strong>')); ?>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <input type="submit" class="btn btn-success" value="Confirmar">
        </div>
    </form>
</div>
<!-- FIM Modal -->

<div class="modal fade" id="modal-imprime-exames">
    <div class="modal-dialog modal-lg" style="position: static;">
        <div class="modal-content">
            <div class="modal-body" style="height: 600px;" id="conteudo-imprime-exames">

            </div>
        </div>
    </div>
</div>

<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		setup_mascaras(); setup_time(); setup_datepicker();
		$(".modal").css("z-index", "-1");
		$(".modal").css("width", "43%");

        $("#modal-imprime-exames").css("z-index", "1050");


        jQuery(".linha-imprimir").each(function() {

            var element = jQuery(this);

            var codigo_cliente_funcionario = element.attr("data-codigo-cf");

            var codigo_pedido = element.attr("data-codigo-pedido");    
            
            var codigo_fornecedor = element.attr("data-codigo-fornecedor");            

            element.on("click", function() {
                carregar_exames(codigo_cliente_funcionario, codigo_pedido, codigo_fornecedor);
            });
        });

        function carregar_exames(codigo_cliente_funcionario, codigo_pedido, codigo_fornecedor) {

            jQuery.ajax({
                type: "POST",
                url: "/portal/pedidos_exames/imprimir_relatorios_credenciado/",
                dataType: "html",
                data: "codigo_pedido=" + codigo_pedido + "&codigo_cliente_funcionario=" + codigo_cliente_funcionario + "&codigo_fornecedor=" + codigo_fornecedor,
                beforeSend: function() {                    
                },
                success: function(retorno) {
                    
                    jQuery("#modal-imprime-exames").modal("show");
                    jQuery("#conteudo-imprime-exames").html(retorno);
                },
                complete: function() {                    
                }
            });     
        }           	
	});

	function editar_realizacao_datas(codigo_item_pedido,mostra) {
		if(mostra) {
			
			var div = jQuery("div#modal_realizacao");
			bloquearDiv(div);
			div.load(baseUrl + "consultas_agendas/modal_pedido_realizacao_data/" + codigo_item_pedido + "/" + Math.random());
	
			$("#modal_realizacao").css("z-index", "1050");
			$("#modal_realizacao").modal("show");

		} else {
			$(".modal").css("z-index", "-1");
			$("#modal_realizacao").modal("hide");
		}

	}

	function editar_realizacao_recebimento(codigo_item_pedido,mostra) {
		if(mostra) {
			
			var div = jQuery("div#modal_realizacao");
			bloquearDiv(div);
			div.load(baseUrl + "consultas_agendas/modal_pedido_realizacao_recebimento/" + codigo_item_pedido + "/" + Math.random());
	
			$("#modal_realizacao").css("z-index", "1050");
			$("#modal_realizacao").modal("show");

		} else {
			$(".modal").css("z-index", "-1");
			$("#modal_realizacao").modal("hide");
		}

	}

	function listagem_anexo_exames(codigo_item_pedido,mostra) {
		if(mostra) {
			
			var div = jQuery("div#modal_realizacao");
			bloquearDiv(div);
			div.load(baseUrl + "consultas_agendas/modal_anexo_exames/" + codigo_item_pedido + "/" + Math.random());
	
			$("#modal_realizacao").css("z-index", "1050");
			$("#modal_realizacao").modal("show");

		} else {
			$(".modal").css("z-index", "-1");
			$("#modal_realizacao").modal("hide");
		}

	}

	function listagem_anexo_ficha_clinica(codigo_item_pedido,mostra) {
		if(mostra) {
			
			var div = jQuery("div#modal_realizacao");
			bloquearDiv(div);
			div.load(baseUrl + "consultas_agendas/modal_anexo_ficha_clinica/" + codigo_item_pedido + "/" + Math.random());
	
			$("#modal_realizacao").css("z-index", "1050");
			$("#modal_realizacao").modal("show");

		} else {
			$(".modal").css("z-index", "-1");
			$("#modal_realizacao").modal("hide");
		}

	}

	function atualizaLista(){
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "consultas_agendas/listagem2/" + Math.random());
	}

	function window_log(codigo_item,codigo_item_pedido){
		var janela = window_sizes();
		window.open(baseUrl + "consultas_agendas/listagem_log_item/" + codigo_item + "/" + codigo_item_pedido + "/" + Math.random(), janela, "scrollbars=yes,menubar=no,height="+(janela.height-200)+",width="+(janela.width-80)+",resizable=yes,toolbar=no,status=no");
	}

'); ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#modal_recusa_exame > form").on('submit', function(e){
            e.preventDefault();
            jQuery.ajax({
                url: '/portal/consultas_agendas/modal_recusa_exame',
                method: 'POST',
                data: jQuery(this).serialize(),
                dataType: 'json',
                beforeSend: function(){
                    if(jQuery("select", jQuery("#modal_recusa_exame > form")).val() == ''){
                        swal("Atenção", "Selecione um motivo", "warning");
                        return false;
                    }
                    jQuery("#modal_recusa_exame > form > div.modal-footer > input[type='submit']").val("Aguarde..");
                    jQuery("#modal_recusa_exame > form > div.modal-footer > input[type='submit']").attr("disabled", "disabled");
                },
                success: function(data){
                    swal("Atenção", data.message, data.status);

                    if(data.status == 'success')
                        jQuery("form#AgendamentoExameFiltrarForm").trigger("submit");
                },
                error: function(jqXHR, textStatus, errorThrow){
                    swal("Atenção", 'error', textStatus);
                },
                complete: function(){
                    jQuery("#modal_recusa_exame").modal('hide');
                }
            });
        });

        jQuery("#modal_recusa_exame").on('hidden', function(){
            jQuery("#modal_recusa_exame > form > div.modal-body > input:eq(0)").val("");
            jQuery("#modal_recusa_exame > form > div.modal-body > div:eq(0) > span").empty();
            jQuery("#modal_recusa_exame > form > div.modal-body > div:eq(1) > span").empty();
            jQuery("#modal_recusa_exame > form > div.modal-footer > input[type='submit']").val("Confirmar");
            jQuery("#modal_recusa_exame > form > div.modal-footer > input[type='submit']").removeAttr("disabled");
            jQuery("#modal_recusa_exame > form > div.modal-body textarea").empty().removeAttr("disabled");
            jQuery("#modal_recusa_exame > form > div.modal-body select").val("").removeAttr("disabled");
        });
    });

    function fnc_modal_recusa_pedido_exame(codigo_pedido_exame, exame, codigo_item_pedido_exame){
        jQuery("#modal_recusa_exame > form > div.modal-body > input:eq(0)").val(codigo_item_pedido_exame);
        jQuery("#modal_recusa_exame > form > div.modal-body > div:eq(0) > span").empty().html(codigo_pedido_exame);
        jQuery("#modal_recusa_exame > form > div.modal-body > div:eq(1) > span").empty().html(exame);
        jQuery("#modal_recusa_exame").css("z-index", "1050").modal('show');
    }

    function fnc_modal_recusa_pedido_exame_exibe(codigo_iper, codigo_item_pedido_exame, exame){
        jQuery.get('/portal/consultas_agendas/modal_recusa_exame_exibe', {codigo: codigo_iper}, function(data){
                jQuery("#modal_recusa_exame > form > div.modal-body > div:eq(0) > span").empty().html(codigo_item_pedido_exame);
                jQuery("#modal_recusa_exame > form > div.modal-body > div:eq(1) > span").empty().html(exame);
                jQuery("#modal_recusa_exame > form > div.modal-body select").val(data[0].codigo_motivo_recusa_exame).attr("disabled", "disabled");
                jQuery("#modal_recusa_exame > form > div.modal-body textarea").empty().html(data[0].descricao).attr("disabled", "disabled");
                jQuery("#modal_recusa_exame > form > div.modal-footer > input[type='submit']").attr("disabled", "disabled");
                jQuery("#modal_recusa_exame").css("z-index", "1050").modal('show');
            }, "json")
            .fail(function(jqXHR, textStatus, errorThrow){
                swal("Atenção", textStatus, 'error');
            });
    }
</script>

<style>
	.waiting {
		border: 1px solid;
		text-decoration: none; 
		border-radius: 100%; 
		background-position: -20px -21px; 
		padding: 3px; 
		background-color: #ffd859;
	}

	.danger {
		border: 1px solid;
		text-decoration: none; 
		border-radius: 100%; 
		background-position: -20px -21px; 
		padding: 3px; 
		background-color: #fc1414;
	}
</style>

	
