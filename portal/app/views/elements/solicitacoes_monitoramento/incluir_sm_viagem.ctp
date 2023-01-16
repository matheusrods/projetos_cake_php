<?php $embarcador_pjur = isset($this->data['Recebsm']['viag_emba_pjur_pess_oras_codigo']) ? $this->data['Recebsm']['viag_emba_pjur_pess_oras_codigo'] : 'false';?>
<?php $transportador_pjur =  $this->data['Recebsm']['viag_tran_pess_oras_codigo'];?>
<h4>Dados da Viagem</h4>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('Recebsm.pvia_codigo', array('type' => 'hidden')) ?>
	<?php echo $this->BForm->input('Recebsm.pedido_cliente', array('class' => 'input-small')) ?>
	<?php if( count($tipo_transporte) > 1 ):?>
		<?php echo $this->BForm->input('Recebsm.operacao', array('label' => 'Tipo de Transporte', 'class' => 'input-medium','options' => $tipo_transporte , 'empty' => 'Selecione um Tipo')) ?>
	<?php else: ?>
		<?php echo $this->BForm->input('Recebsm.operacao', array('label' => 'Tipo de Transporte', 'class' => 'input-medium','options' => $tipo_transporte)) ?>	
	<?php endif ?>
	<div class='row-fluid inline'>
		<div class="control-group input text <?php  echo $this->BForm->error('temperatura2') ? 'error' : '' ?>">
			<label for="RecebsmTemperatura">Controle de Temperatura</label>
			<?php echo $this->BForm->input('Recebsm.temperatura', array('label' => false, 'div'=>false, 'class' => 'input-mini numeric temperatura', 'before'=>'faixa de:')) ?>
			<?php echo $this->BForm->input('Recebsm.temperatura2', array('label' => false, 'div'=>false, 'class' => 'input-mini numeric temperatura', 'before'=>'até:')) ?>
		</div>
	</div>
	<div class='row-fluid inline'>
		<div id="tipo_pgr" style="display:none" class='row-fluid inline'>
			<h4>Tipo de PGR</h4>
			<?php echo $this->BForm->input('Recebsm.tipo_pgr', array('type' => 'radio', 'options' => array(
					'G' => 'GR',
					'L' => 'Logistico'
				), 'default' => 'G', 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
			
		</div>
		<div id="mensagem_tipo_pgr" style="display:none">
			<p style="color:#b94a48">Atenção! Este Veículo terá acompanhamento Logístico, sem ação da GR.</p>
		</div>
	</div>	
</div>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function() { 		
		valida_pgr_logistico_gr({$transportador_pjur},{$embarcador_pjur});
	});

	function valida_pgr_logistico_gr(transportador,embarcador){
		$.ajax({
			'url': baseUrl + 'pgpg_pgs/mostra_mensagem_pgr_logistico/' + transportador + '/' + embarcador + '/' + Math.random(),
			dataType: 'json',	        
			'success': function(data) {
				if(data){
					if(data.mensagem == true){
						$('#tipo_pgr').show();						
						habilita_opcao(data.tipo_pgr);
					}else{
						habilita_opcao(data.tipo_pgr);
						$('#tipo_pgr').hide();
					}
				}
			},
			'error' : function(){
				return false;
			}
		});

		function habilita_opcao(opcao){
			if(opcao == 'G'){
				id = '#RecebsmTipoPgrG';
			}else{
				id = '#RecebsmTipoPgrL';
			}
			$(''+id+'').prop('checked', true);	
		}

		$('.radio').change(function(){				
			if(jQuery('#RecebsmTipoPgrL').is(':checked')){
				$('#mensagem_tipo_pgr').show();
			}else{
				$('#mensagem_tipo_pgr').hide();
			}
		});
	}
");?>