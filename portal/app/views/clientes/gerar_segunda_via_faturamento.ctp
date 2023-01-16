<?php if (!$authUsuario['Usuario']['codigo_cliente']):?>
<div class='well'>
	<?php echo $this->BForm->create('RetornoNf', array('url' => array('controller' => 'clientes', 'action' => 'gerar_segunda_via_faturamento'))); ?>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', FALSE, 'RetornoNf' ); ?>
		</div>
		<?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end() ?>
</div>
<?php else: ?>
	<!-- USUARIOS FILIAL -->
	<div class='well'>
		<?php echo $this->BForm->create('RetornoNf', array('url' => array('controller' => 'clientes', 'action' => 'gerar_segunda_via_faturamento'))); ?>
			<div class="row-fluid inline">			
				<div class="row-fluid inline">
					<div class="control-group input select">
						<label for="RetornoNfCodigoCliente">Filiais</label>						
						<select name="data[RetornoNf][codigo_cliente]" title="Multi-Clientes" class="input-xlarge ajax-multiclientes" id="RetornoNfCodigoCliente">
						<option value="">SELECIONE</option>
							<?php 
							
							
							
							foreach ($filiais as $key => $valor) {
								$selected = "";

								if ($key == $select) {
									$selected = 'selected';
								}

									if($key == $matriz){ ?>	
										<option value="<?=$key; ?>" <?=$selected;?> >* <?=$valor; ?></option>
									<?php }else{ ?>
										<option value="<?=$key; ?>" <?=$selected;?> ><?=$valor; ?></option>
									<?php } ?>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
			
			<?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $this->BForm->end() ?>
	</div>


<?php endif; ?>

<?php if(!empty($notas_fiscais)): ?>
	<div class='row-fluid inline'>
		<div id="cliente" class='well'>
			<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
			<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
		</div>
	</div>
	<table class='table tablestriped'>
		<thead>
			<th class='numeric'>Nota Fiscal</th>
			<th class='numeric'>Emissão</th>
			<th class='numeric'>RPS</th>
			<th>NF-e</th>
			<th>Boleto</th>
			<th>Demonstrativo(s)</th> 
			<th>Último Envio</th>
			<th></th>
			<th></th>
		</thead>
		<tbody>
			<?php foreach($notas_fiscais as $nota_fiscal): ?>
				<tr>
					<td class='numeric'><?= $nota_fiscal['RetornoNf']['nota_fiscal'] ?></td>
					<td class='numeric'><?= substr($nota_fiscal['Notafis']['dtemissao'],0,10) ?></td>
					<td class='numeric'><?= $nota_fiscal['Notafis']['numero'] ?></td>
					<td><?= $this->Html->link('Visualizar', $nota_fiscal['links']['nf'], array('target' => '_blank')) ?></td>
					<td id="adiciona_target"><?= $nota_fiscal['links']['boleto'] ?></td>
					
					<td>
						<?php foreach ($nota_fiscal['links']['demonstrativos'] as $demonstrativo): ?>
							<?= $this->Html->link('Visualizar', $demonstrativo, array('onclick' => 'popup_percapita_exames_complementares(event, this, '.$exibe_centro_custo.')')) ?>
						<?php endforeach ?>
					</td>
					
					<td><?= AppModel::dbDateToDate($nota_fiscal['0']['data_ultimo_envio']) ?></td>
					<td><?php echo $this->Html->link('<i class="icon-envelope"></i>', 'javascript:void(0)', array('escape' => false, 'title' =>'Reenviar E-mail', 'onclick' => "reenviar_email('{$nota_fiscal['RetornoNf']['nota_fiscal']}', '{$this->data['RetornoNf']['codigo_cliente']}')"));?></td>
					<td>
						<?php echo $this->Html->link('<i class="icon-tasks"></i>', 'javascript:void(0)', array('escape' => false, 'title' =>'Histórico de Envios', 'onclick' => "historico_envios('{$nota_fiscal['RetornoNf']['nota_fiscal']}', '{$this->data['RetornoNf']['codigo_cliente']}')"));?>
						<div id='envio<?= $nota_fiscal['RetornoNf']['nota_fiscal'] ?>' style='display:none'></div>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
	<?php echo $this->Javascript->codeBlock("
		function reenviar_email(nota_fiscal, codigo_cliente) {
			if (confirm('Deseja reenviar este e-mail?'))
				jQuery.ajax({
					type: 'POST',
					url: baseUrl + 'clientes/reenviar_email_faturamento/' + nota_fiscal + '/' + codigo_cliente
				});
		}
		function historico_envios(nota_fiscal, codigo_cliente) {
			var div = jQuery('#envio'+nota_fiscal);
			if (div.html().length == 0) {
				jQuery.ajax({
					type: 'GET',
					url: baseUrl + 'outbox/envios_por_nota_fiscal/' + nota_fiscal + '/' + codigo_cliente,
					dataType: 'JSON',
					success: function(data) {
						if (data != null) {
							var i;
							var html = '<table class=\'table table-striped\'><thead><th>Data</th><th>Email</th></thead>';
							for (i=0 ; i < data.length ; i++) {
								html = html + '<tr><td>'+data[i]['sent']+'</td><td>'+data[i]['to']+'</td><tr>';
							}
							html = html + '</table>';
							div.html(html);
							if (div.html().length > 0) {
								open_dialog(div.html(),'Envios Nota Fiscal '+nota_fiscal,900,undefined,undefined,true);
							}
						}
					}
				});
			} else {
				open_dialog(div.html(),'Envios Nota Fiscal '+nota_fiscal,900,undefined,undefined,true);
			}
		}
		jQuery(document).ready(function() {
			jQuery('#adiciona_target a').attr('target', '_blank');
			// Chama função para completar lista de emails do financeiro 
			// e também atualiza a mesma na alteração ou inclusão de um novo email
			// na listagem
			atualizarListaEmailsFinanceiros({$this->data['RetornoNf']['codigo_cliente']});
		})
		function popup_percapita_exames_complementares(e, input, ecc){
            e.preventDefault();
            let url = input.href;
            if(input.href.indexOf(\"gera_demonstrativo_percapita\") != -1){
                url = url + \"&centro_custo=\" + (ecc ? true : false);
                window.open(url, '_blank');
            }else{
                window.open(url, '_blank');
            }
        }
		"); ?>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>
<?php if(!empty($this->data['RetornoNf']['codigo_cliente'])): ?>
	<?php $codigo_cliente = $this->data['RetornoNf']['codigo_cliente'] ?>
	<div class="row-fluid">
		<span class="span12 span-right">
			<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir_email_financeiro', $codigo_cliente), array('escape' => false, 'class' => 'btn btn-success btn-modal', 'title' => 'Incluir Email Financeiro', 'onclick' => "return open_dialog(this, 'Incluir Email', 640)")); ?>
		</span>
	</div>
	<div id="endereco-cliente" class="grupo"></div>
	<div class="lista"></div>
<?php endif ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>