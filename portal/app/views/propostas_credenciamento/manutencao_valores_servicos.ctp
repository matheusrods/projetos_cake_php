<style>
	legend {font-size: 13px; margin-bottom: 0;}
	.control-group {padding:0; margin: 0}
</style>

<div class="well">
	<div class="span5">
		<b>Razão Social: </b><?php echo $dadosProposta['PropostaCredenciamento']['razao_social']; ?><br />
		<b>Nome Fantasia: </b><?php echo $dadosProposta['PropostaCredenciamento']['nome_fantasia']; ?><br />
		<b>CNPJ: </b><?php echo Comum::formatarDocumento($dadosProposta['PropostaCredenciamento']['codigo_documento']); ?></span>
	</div>
	<div class="span6">
		<b>Número da Proposta: </b><?php echo $dadosProposta['PropostaCredenciamento']['codigo']; ?> (<a href="/portal/propostas_credenciamento/editar/<?php echo $dadosProposta['PropostaCredenciamento']['codigo']; ?>">Visualizar</a>) <br /> 
		<b>Status Atual: </b><?php echo $lista_status[$dadosProposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento']]?><br />
			
	</div>	
	<div style="clear: both;"></div>
</div>

<?php echo $this->BForm->create('PropostaCredExame', array('type' => 'post', 'url' => array('controller' => 'propostas_credenciamento', 'action' => 'manutencao_valores_servicos', $this->passedArgs[0]))); ?>
	<?php echo $this->BForm->hidden('codigo', array('value' => $codigo)); ?>

	<table>
		<?php foreach( $exames as $key => $exame ) : ?>
			<tr>
    			<td><?php echo $this->BForm->input('Servico.' . $exame['PropostaCredExame']['codigo'] . '.descricao', array('value' => $exame['Servico']['descricao'], 'class' => 'form-control', 'label' => 'Exame:', 'style' => 'float: left; width: 385px;', 'disabled' => 'disabled')); ?></td>
    			
    			<?php if($exame['PropostaCredExame']['valor_base']) : ?>
    				<td><?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_base', array('value' => $exame['PropostaCredExame']['valor_base'], 'class' => 'form-control moeda', 'label' => 'Máximo: (R$)', 'style' => "float: left; width: 100px; text-align: right; border: 2px solid #000;", 'disabled' => 'disabled')); ?></td>
    			<?php else : ?>
    				<td><?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_base', array('value' => 'SEM VALOR', 'class' => 'form-control', 'label' => 'Máximo: (R$)', 'style' => "border: 2px solid #000;text-align: center; width: 100px;", 'disabled' => 'disabled', 'title' => 'Este produto não esta cadastrado na tabela de preço padrão!')); ?></td>
    			<?php endif; ?>
    			
    			<td><?php echo $this->BForm->input('media_' . $key, array('value' => isset($media[$exame['PropostaCredExame']['codigo_exame']]) ? $media[$exame['PropostaCredExame']['codigo_exame']] : '-', 'class' => 'form-control moeda', 'label' => 'Média Cidade:', 'style' => "float: left; width: 100px; text-align: right; border: 2px solid #000;", 'disabled' => 'disabled')); ?></td>
    			<td>
    				<?php $valor_original = number_format(str_replace(",", ".", $exame['PropostaCredExame']['valor']), 2, ',', '.'); ?>
    				<?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor', array('value' => trim($exame['PropostaCredExame']['valor']) ? $exame['PropostaCredExame']['valor'] : '0,00', 'class' => 'form-control moeda', 'label' => 'Valor Proposto:', 'style' => "float: left; width: 100px; text-align: right; {$exame['Style']['valor_1']}", 'disabled' => trim($exame['PropostaCredExame']['valor']) ? '' : 'disabled', 'onchange' => "verifica_diferente('{$valor_original}', this);")); ?>
    			</td>
    			<td>
    				<?php $valor_contra_proposta_original = $exame['PropostaCredExame']['valor_contra_proposta'] ? number_format(str_replace(",", ".", $exame['PropostaCredExame']['valor_contra_proposta']), 2, ',', '.') : '-'; ?>
    				<?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_contra_proposta', array('value' => ($exame['PropostaCredExame']['valor_contra_proposta'] ? $exame['PropostaCredExame']['valor_contra_proposta'] : '-'), 'class' => 'form-control moeda contra_proposta', 'label' => 'Contra Proposta:', 'style' => "float: left; width: 100px; text-align: right; {$exame['Style']['valor_2']}", 'disabled' => (trim($exame['PropostaCredExame']['valor_contra_proposta']) ? '' : 'disabled'), 'onchange' => "verifica_diferente('{$valor_contra_proposta_original}', this);")); ?>
    			</td>
    			<td>
    				<?php $valor_minimo_original = $exame['PropostaCredExame']['valor_contra_proposta'] ? number_format(str_replace(",", ".", $exame['PropostaCredExame']['valor_contra_proposta']), 2, ',', '.') : '-'; ?>
    				<?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_minimo', array('value' => (trim($exame['PropostaCredExame']['valor_minimo']) ? $exame['PropostaCredExame']['valor_minimo'] : '-'), 'class' => 'form-control moeda valor_minimo', 'label' => 'Valor Mínimo:', 'style' => "float: left; width: 100px; text-align: right; {$exame['Style']['valor_3']}", 'disabled' => trim($exame['PropostaCredExame']['valor_minimo']) ? '' : 'disabled', 'onchange' => "verifica_diferente('{$valor_minimo_original}', this);")); ?>
    			</td>
    			<td style="padding: 25px 5px;">
    				<i class="icon-eye-open" style="cursor: pointer;" onclick="manipula_modal('log_<?php echo $exame['PropostaCredExame']['codigo']; ?>', 1);" title="Visualizar Logs"></i>
    			</td>
			</tr>
		<?php endforeach; ?>
	</table>
	
	<div class="form-actions right">
		<button type=submit class="btn btn-primary btn-lg"><i class="glyphicon glyphicon-share"></i> Salvar</button>
		<a href="/portal/propostas_credenciamento/alteracao_valores_exames" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-fast-backward"></i> Voltar</a>
	</div>
	
<?php echo $this->BForm->end(); ?>

<div id="aprovado" style="display:none;">
	<a href="javascript:void(0);" class="label label-success" style="cursor: default; display: block;"><i class="icon-white icon-ok-sign"></i> Aprovado</a>
</div>
<div id="reprovado" style="display:none;">
	<a href="javascript:void(0);" class="label label-inverse" style="cursor: default; display: block;"><i class="icon-white icon-remove-sign"></i> Reprovado</a>
</div>



<?php foreach( $exames as $key => $exame ) : ?>
	<div class="modal fade" id="log_<?php echo $exame['PropostaCredExame']['codigo']; ?>">
		<div class="modal-dialog modal-lg" style="position: static;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="gridSystemModalLabel"><?php echo $exame['Servico']['descricao']; ?></h4>
				</div>
		    	<div class="modal-body">
		    	
		    		<?php if(isset($exame['logs']) && count($exame['logs'])) : ?>
		    		
			    		<table class="table-striped" width="100%" style="font-size: 12px;">
			    			<thead>
				    			<tr>
				    				<td style="font-weight: bold;">Data:</td>
				    				<td style="text-align: right; font-weight: bold;">Valor:</td>
				    				<td style="text-align: right; font-weight: bold;">Contra  Proposta:</td>
				    				<td style="text-align: right; font-weight: bold;">Valor Mínimo:</td>
				    				<td style="text-align: right; font-weight: bold;">Aceito:</td>
				    				<td style="text-align: right; font-weight: bold;">Usuario:</td>
				    			</tr>
				    		</thead>
				    		
				    		<tbody>
				    		<?php foreach($exame['logs'] as $k => $item) : ?>
				    			<?php $item['PropostaCredExameLog']['aceito'] = ($item['PropostaCredExameLog']['aceito'] === 1) ? "SIM" : $item['PropostaCredExameLog']['aceito']; ?>
				    			<?php $item['PropostaCredExameLog']['aceito'] = ($item['PropostaCredExameLog']['aceito'] === 0) ? "NÃO" : $item['PropostaCredExameLog']['aceito']; ?>				    		
				    			<?php $item['PropostaCredExameLog']['aceito'] = ($item['PropostaCredExameLog']['aceito'] == '') ? "-" : $item['PropostaCredExameLog']['aceito']; ?>
				    			
				    			<tr>
				    				<td style=""><?php echo $item['PropostaCredExameLog']['data_inclusao']; ?></td>
				    				<td style="text-align: right; <?php echo (isset($valor) && ($item['PropostaCredExameLog']['valor'] != $valor)) ? "color: red;" : ""; ?>"><?php echo number_format($item['PropostaCredExameLog']['valor'], 2, ',', '.'); ?></td>
				    				<td style="text-align: right; <?php echo (isset($valor_contra_proposta) && ($item['PropostaCredExameLog']['valor_contra_proposta'] != $valor_contra_proposta)) ? "color: red;" : ""; ?>"><?php echo $item['PropostaCredExameLog']['valor_contra_proposta'] ? number_format($item['PropostaCredExameLog']['valor_contra_proposta'], 2, ',', '.') : '-'; ?></td>
				    				<td style="text-align: right; <?php echo (isset($valor_minimo) && ($item['PropostaCredExameLog']['valor_minimo'] != $valor_minimo)) ? "color: red;" : ""; ?>"><?php echo $item['PropostaCredExameLog']['valor_minimo'] ? number_format($item['PropostaCredExameLog']['valor_minimo'], 2, ',', '.') : '-'; ?></td>
				    				<td style="text-align: right; <?php echo (isset($aceito) && ($item['PropostaCredExameLog']['aceito'] != $aceito)) ? "color: red;" : ""; ?>">
				    					<?php 
				    						echo $item['PropostaCredExameLog']['aceito'];
											$aceito = $item['PropostaCredExameLog']['aceito'];			    						
				    					?>
				    				</td>
				    				<td style="text-align: right; <?php echo (isset($usuario) && ($item['Usuario']['nome'] != $usuario)) ? "color: red;" : ""; ?>"><?php echo $item['Usuario']['nome']; ?></td>
				    			</tr>
				    			
				    			<?php
					    			$data_inclusao = $item['PropostaCredExameLog']['data_inclusao'];
					    			$valor = $item['PropostaCredExameLog']['valor'];
					    			$valor_contra_proposta = $item['PropostaCredExameLog']['valor_contra_proposta'] ? $item['PropostaCredExameLog']['valor_contra_proposta'] : '';
					    			$valor_minimo = $item['PropostaCredExameLog']['valor_minimo'] ? $item['PropostaCredExameLog']['valor_minimo'] : '';
					    			$usuario = $item['Usuario']['nome'];
				    			?>
				    		<?php endforeach; ?>
		    				<?php unset($data_inclusao, $valor, $valor_contra_proposta, $valor_minimo, $aceito, $usuario); ?>
		    				
				    		</tbody>
			    		</table>
			    		<br />
			    		<a href="javascript:void(0);" onclick="manipula_modal('log_<?php echo $exame['PropostaCredExame']['codigo']; ?>', 0);" class="btn btn-danger">Fechar</a>
		    		<?php else : ?>
						<div class="alert">Nenhum dado foi encontrado.</div>		    		
		    		<?php endif; ?>
		    	</div>
		    </div>
		</div>
	</div>
<?php endforeach; ?>


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

<div style="clear:both;"></div>

<?php echo $this->Javascript->codeBlock('

	jQuery(document).ready(function() {
		setup_mascaras();
		
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
		
	function verifica_diferente(valor_original, element) {
		var novo_valor = $(element).val();
		
		if(valor_original.trim() != novo_valor.trim()) {
			$(element).css({"border": "2px solid blue"});
		} else {
			$(element).css({"border": "1px solid #CCC"});
		}
	}
		
'); ?>