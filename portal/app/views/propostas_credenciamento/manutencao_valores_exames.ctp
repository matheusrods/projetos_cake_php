<?php echo $this->BForm->create('PropostaCredExame', array('type' => 'post', 'url' => array('controller' => 'propostas_credenciamento', 'action' => 'editar', $this->passedArgs[0]))); ?>
	<h3 >Relação de Exames:</h3>
	
	<table>
		<?php $flag_envio_form = false; ?>
		<?php foreach( $exames as $key => $exame ) : ?>
			<tr>
    			<td><?php echo $this->BForm->input('Servico.' . $exame['PropostaCredExame']['codigo'] . '.descricao', array('value' => $exame['Servico']['descricao'], 'class' => 'form-control', 'label' => 'Exame:', 'style' => 'float: left; width: 385px;', 'disabled' => 'disabled')); ?></td>
    			<?php if($exame['PropostaCredExame']['valor_base']) : ?>
    				<td><?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_base', array('value' => $exame['PropostaCredExame']['valor_base'], 'class' => 'form-control moeda', 'label' => 'Máximo: (R$)', 'style' => "float: left; width: 100px; text-align: right; border: 2px solid #000;", 'disabled' => 'disabled')); ?></td>
    			<?php else : ?>
    				<td><?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_base', array('value' => 'SEM VALOR', 'class' => 'form-control', 'label' => 'Máximo: (R$)', 'style' => "border: 2px solid #000;text-align: center; width: 100px;", 'disabled' => 'disabled', 'title' => 'Este produto não esta cadastrado na tabela de preço padrão!')); ?></td>
    			<?php endif; ?>
    			<td><?php echo $this->BForm->input('media_' . $key, array('value' => isset($media[$exame['PropostaCredExame']['codigo_exame']]) ? $media[$exame['PropostaCredExame']['codigo_exame']] : '-', 'class' => 'form-control moeda', 'label' => 'Média Cidade:', 'style' => "float: left; width: 100px; text-align: right; border: 2px solid #000;", 'disabled' => 'disabled')); ?></td>
    			<td><?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor', array('value' => trim($exame['PropostaCredExame']['valor']) ? $exame['PropostaCredExame']['valor'] : '0,00', 'class' => 'form-control moeda', 'label' => 'Valor Proposto:', 'style' => "float: left; width: 100px; text-align: right; {$exame['Style']['valor_1']}", 'disabled' => 'disabled')); ?></td>
    			
    			<td>
    				<?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_contra_proposta', array('value' => ($exame['PropostaCredExame']['valor_contra_proposta'] ? $exame['PropostaCredExame']['valor_contra_proposta'] : ''), 'class' => 'form-control moeda contra_proposta', 'label' => 'Contra Proposta:', 'style' => "float: left; width: 100px; text-align: right; {$exame['Style']['valor_2']}", 'onblur' => 'verificaContra(this, "'.$exame['PropostaCredExame']['codigo'].'", "'.$exame['PropostaCredExame']['valor'].'")', 'disabled' => is_null($exame['PropostaCredExame']['aceito']) && is_null($exame['PropostaCredExame']['valor_minimo']) && ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES) ? 'false' : 'true')); ?>
    			</td>
    			
    			<td>
    				<?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_minimo', array('value' => (($exame['PropostaCredExame']['valor_minimo'] && $status != StatusPropostaCred::AGUARDANDO_AVALIACAO_CONTRA_PROPOSTA) ? $exame['PropostaCredExame']['valor_minimo'] : '-'), 'class' => 'form-control moeda valor_minimo', 'label' => 'Valor Mínimo:', 'style' => "float: left; width: 100px; text-align: right; {$exame['Style']['valor_3']}", 'disabled' => ((($exame['PropostaCredExame']['aceito'] == '1') || ($status != StatusPropostaCred::RENEGOCIAR_VALOR_MINIMO)) ? 'disabled' : ''))); ?>
    			</td>
    			
    			<td id="exame_<?php echo $exame['PropostaCredExame']['codigo']; ?>" style="text-align: center; padding: 15px 5px 0;">
    				<?php if($exame['PropostaCredExame']['aceito'] == '1') : ?>
    				
    					<?php if(is_null($exame['PropostaCredExame']['valor_contra_proposta'])) : ?>
    						<a href="javascript:void(0);" class="label label-success"><i class="icon-white icon-ok-sign"></i> Aprovado: <?php echo $exame['Usuario']['nome']; ?></a>
    						<?php if(($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES)) : ?>
    							<a href="javascript:void(0);" onclick="volta_status_exame(<?php echo $exame['PropostaCredExame']['codigo']; ?>, <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);">Reverter</a>
    						<?php endif; ?>
    					<?php elseif(!is_null($exame['PropostaCredExame']['valor_minimo'])) : ?>
    						<a href="javascript:void(0);" class="label label-success"><i class="icon-white icon-ok-sign"></i> Aprovado Mínimo: <?php echo $exame['PropostaCredExame']['valor_minimo']; ?></a>
    						<?php if(($status == StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES) || ($status == StatusPropostaCred::VALOR_MINIMO_NEGOCIADO) ) : ?>
    							<br />
    							<a href="javascript:void(0);" onclick="voltar_valida_valor_minimo(<?php echo $exame['PropostaCredExame']['codigo']; ?>, '<?php echo $exame['PropostaCredExame']['valor_minimo']; ?>', <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>)">Reverter</a>
    						<?php endif; ?>
    					<?php else : ?>
    						<a href="javascript:void(0);" class="label label-success"><i class="icon-white icon-ok-sign"></i> Contra Proposta Aprovada: Cliente</a>
    					<?php endif; ?>
    					
    				<?php elseif($exame['PropostaCredExame']['aceito'] == '0') : ?>
    				
    					<?php if(is_null($exame['PropostaCredExame']['valor_minimo'])) : ?>
    						<a href="javascript:void(0);" class="label label-inverse" style="cursor: default;"><i class="icon-white icon-thumbs-down"></i> Reprovado!</a>
    						<br />
    						<a href="javascript:void(0);" onclick="volta_status_exame(<?php echo $exame['PropostaCredExame']['codigo']; ?>, <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);">Reverter</a>
    					<?php else : ?>
    						<a href="javascript:void(0);" class="label label-inverse" style="cursor: default;"><i class="icon-white icon-remove-sign"></i> Reprovado Mínimo: R$ <?php echo $exame['PropostaCredExame']['valor_minimo']; ?></a>
    						<br />
    						<a href="javascript:void(0);" onclick="voltar_valida_valor_minimo(<?php echo $exame['PropostaCredExame']['codigo']; ?>, '<?php echo str_replace(",", ".", $exame['PropostaCredExame']['valor_minimo']); ?>', <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);">Reverter</a>
    					<?php endif; ?>
    					
    				<?php elseif((is_null($exame['PropostaCredExame']['aceito']) && is_null($exame['PropostaCredExame']['valor_contra_proposta'])) && (($status == StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES))) : ?>
    				
    					<a href="javascript:void(0);" onclick="aprovar_exame(<?php echo $exame['PropostaCredExame']['codigo']; ?>, '1', <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>)" class="label label-info"><i class="icon-white icon-thumbs-up"></i> Aceitar!</a>
    					<a href="javascript:void(0);" onclick="aprovar_exame(<?php echo $exame['PropostaCredExame']['codigo']; ?>, '0', <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>)" class="label label-inverse"><i class="icon-white icon-thumbs-up"></i> Reprovar!</a>
    					
					<?php elseif((isset($exame['PropostaCredExame']['valor_minimo']) && $exame['PropostaCredExame']['valor_minimo'] != "") && (($status == StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES) || ($status == StatusPropostaCred::RENEGOCIAR_VALOR_MINIMO) || ($status == StatusPropostaCred::VALOR_MINIMO_NEGOCIADO))) : ?>

						<?php if(is_null($exame['PropostaCredExame']['aceito']) && ($status != StatusPropostaCred::RENEGOCIAR_VALOR_MINIMO)) : ?>
			    			<a href="javascript:void(0);" class="label label-success" onclick="valida_valor_minimo('<?php echo $exame['PropostaCredExame']['codigo']; ?>', '1', '<?php echo str_replace(",", ".", $exame['PropostaCredExame']['valor_minimo']); ?>', <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);"><i class="icon-white icon-thumbs-up"></i> APROVAR!</a>
			    			<a href="javascript:void(0);" class="label label-important" onclick="valida_valor_minimo('<?php echo $exame['PropostaCredExame']['codigo']; ?>', '0', '<?php echo str_replace(",", ".", $exame['PropostaCredExame']['valor_minimo']); ?>', <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);"><i class="icon-white icon-thumbs-down"></i> REPROVAR!</a>
    					<?php elseif($exame['PropostaCredExame']['aceito'] == "1") : ?>
    						<a id="resultado_<?php echo $key; ?>" href="javascript:void(0);" class="label label-success">APROVADO: <?php echo $exame['PropostaCredExame']['valor_minimo']; ?></a>
    					<?php elseif($exame['PropostaCredExame']['aceito'] == "0") : ?>
    						<a id="resultado_<?php echo $key; ?>" href="javascript:void(0);" class="label label-inverse">REPROVADO: <?php echo $exame['PropostaCredExame']['valor_minimo']; ?></a>
    					<?php elseif($exame['PropostaCredExame']['aceito'] == "2") : ?>
			    			<a href="javascript:void(0);" class="label label-danger" onclick="valida_valor_minimo('<?php echo $exame['PropostaCredExame']['codigo']; ?>', '0', $('#PropostaCredExame<?php echo $exame['PropostaCredExame']['codigo']; ?>ValorMinimo').val(), <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);">X</a>		    						
    					<?php endif; ?>
    					
    				<?php elseif((is_null($exame['PropostaCredExame']['aceito']) && !is_null($exame['PropostaCredExame']['valor_contra_proposta'])) || (($status == StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES))) : ?>
    					<a href="javascript:void(0);" class="label" style="border: 1px solid #666; padding: 2px; cursor: default; font-size: 12px; font-weight: normal;"><i class="icon-white icon-remove-sign"></i> Não foi Avaliado!</a>								
	    			<?php endif; ?>
	    			
	    			<?php if(is_null($exame['PropostaCredExame']['aceito']) && (($status == StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES))) : ?>
 								<a href="javascript:void(0);" class="label label-alert" title="Remover este exame" onclick="remove_exame(this, '<?php echo $exame['PropostaCredExame']['codigo_exame']; ?>', '<?php echo $this->data['PropostaCredenciamento']['codigo']; ?>');">X</a>
					<?php endif; ?>
								    			
				</td>
    			<td id="carregando_<?php echo $exame['PropostaCredExame']['codigo']; ?>" style="display: none; text-align: center;">
    				<img src="/portal/img/hourglass.gif">
    			</td>
			</tr>
			
			<?php if(!is_null($exame['PropostaCredExame']['valor_minimo'])) : ?>
				<?php $flag_tem_valor_minimo = true; ?>
			<?php endif; ?>
			
			<?php if(!isset($flag_envio_form) || !$flag_envio_form) : ?>
				<?php $flag_envio_form = (is_null($exame['PropostaCredExame']['valor_contra_proposta']) && is_null($exame['PropostaCredExame']['aceito'])); ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</table>
	
	<div class='form-actions' id="form-actions" style="display: <?php echo isset($flag_envio_form) && $flag_envio_form ? 'block' : 'none'; ?>;">
		<a href="javascript:void(0);" class="btn btn-success" onclick="verifica_preenchimento_contraproposta(<?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);">Enviar Contra Proposta</a>
		(Preencher os campos de Contra Proposta para cada valor fora da política da RHHealth e Enviar a Contra Proposta!)
	</div>
<?php echo $this->BForm->end(); ?>

    
<div id="aprovado" style="display:none;">
	<a href="javascript:void(0);" class="label label-success" style="cursor: default; display: block;"><i class="icon-white icon-ok-sign"></i> Aprovado</a>
</div>
<div id="reprovado" style="display:none;">
	<a href="javascript:void(0);" class="label label-inverse" style="cursor: default; display: block;"><i class="icon-white icon-remove-sign"></i> Reprovado</a>
</div>


<div class="modal fade" id="modal_tabela_padrao">
	<div class="modal-dialog modal-md" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Tabela de Preços Padrão:</h4>
				<label><a href="javascript:void(0);" onclick="adicionaExames(<?php echo $this->passedArgs[0]; ?>); $('#modal_tabela_padrao').modal('hide');" class="btn btn-success btn-sm right" title="Incluir">Incluir na Proposta!</a></label>
				<div class="clear"></div>
			</div>
	    	<div class="modal-body" style="height: 600px; overflow: scroll;">
				<table style="width: 100%" class="table-striped">
					<tr>
						<td class="center" style="width: 110px;"></td>
						<td>Exame</td>
						<td style="text-align: right;">Valor Base</td>
					</tr>			
					<?php foreach($tabela_padrao as $key => $campo) : ?>
						<tr>
							<td style="width: 110px; text-align: center;"><input class="checkbox_exames" type="checkbox" value="<?php echo $campo['codigo']; ?>" name="tabela.<?php echo $key; ?>.exame"></td>
							<td><?php echo utf8_encode(strtoupper($campo['nome'])); ?></td>
							<td style="text-align: right;">R$ <?php echo $campo['valor']; ?></td>
						</tr>				
					<?php endforeach; ?>
				</table>
	    	</div>
	    </div>
	</div>
</div>
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

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		$(".modal").css("z-index", "-1");
	});
		
	function manipula_modal(id, mostra) {
		if(mostra) {
			$("#" + id).css("z-index", "1050");
			$("#" + id).modal("show");
		} else {
			$(".modal").css("z-index", "-1");
			$("#" + id).modal("hide");
		}
	}
'); ?>