<div class='row-fluid inline'>
<div class="gerenciadora"> 
	<div class='span4'>
		<h4>Gerenciadora de Risco</h4>
		<?php echo $this->BForm->input('gerenciadora', array('label' => false, 'empty' => 'Selecione uma Gerenciadora', 'options' => $gerenciadoras,'class'=>'input-xlarge')) ?>
		<?php echo $this->BForm->input('liberacao', array('label' => false, 'class' => 'input-medium', 'placeholder' => 'Nº Liberação', 'maxlength' => 15)) ?>
	</div>
</div>

<div class="veiculo"> 
	<h4>Veículo</h4>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('veic_codigo_externo', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Codigo do cliente',
				   'after'=>$this->Html->link('<i class="icon-search"></i>', 'javascript:void(0)', array('escape' => false, 'title' => 'Procurar veiculo por codigo_externo', 'onclick'=>'javascript: consulta_codigo_externo_veiculo();')))
		) ?>
		<?php echo $this->BForm->input('Recebsm.placa', array('label' => false, 'class' => 'input-small placa-veiculo','placeholder' => 'Placa' , 'onblur'=>"consulta_tipo_placa_express(jQuery(this).parent().parent().parent(),$('#RecebsmCodigoCliente'))", 'value'=>$this->data['Recebsm']['placa'])) ?>
		<?php echo $this->BForm->input('Recebsm.tipo', array('label' => false, 'class' => 'input-medium tipo','placeholder' => 'Tipo do Veículo', 'readonly' => true, 'value'=>$this->data['Recebsm']['tipo'])) ?>
		<?php echo $this->BForm->input('Recebsm.tecnologia', array('label' => false, 'class' => 'input-medium','placeholder' => 'Tecnologia', 'readonly' => true, 'value'=>$this->data['Recebsm']['tecnologia'])) ?>
	</div>
</div>
</div>
<div class="emba_tran">
	<h4>Embarcador / Transportador</h4>
	<div class='row-fluid inline'>
		<?php 
			if($embarcador_read)
				$emb_readonly = array('label' => 'Embarcador','readonly' => $embarcador_read , 'options' => $embarcadores, 'class' => 'input-xlarge');
			else
				$emb_readonly = array('label' => 'Embarcador','readonly' => $embarcador_read ,'empty' => 'Selecione um Embarcador', 'options' => $embarcadores, 'class' => 'input-xlarge');

			echo $this->BForm->input('Recebsm.embarcador', $emb_readonly);
		?>
		<?php 
			if($transportador_read)
				$tra_readonly = array('label' => 'Transportador','readonly' => $transportador_read, 'options' => $transportadores, 'class' => 'input-xlarge', 'error'=>array('escape'=>false));
			else
				$tra_readonly = array('label' => 'Transportador','readonly' => $transportador_read, 'empty' => 'Selecione um Transportador', 'options' => $transportadores, 'class' => 'input-xlarge', 'error'=>array('escape'=>false));

			echo $this->BForm->input('Recebsm.transportador', $tra_readonly);
		?>
	</div>
</div>

<h4>Motorista</h4>
<div class='row-fluid inline motorista-data'>
	<?php echo $this->BForm->hidden('Profissional.codigo') ?>
	<?php echo $this->BForm->hidden('Profissional.estrangeiro') ?>
	<?php echo $this->BForm->input('Recebsm.codigo_documento', array('label' => false, 'class' => 'input-medium formata-rne', 'placeholder' => 'CPF', 'div'=>array('class'=>'control-group input text documento'), 'error'=>array('escape'=>false))) ?>
	<?php echo $this->BForm->input('Recebsm.nome', array('label' => false, 'class' => 'input-large','placeholder' => 'Nome', 'readonly'=>true)) ?>
	<?php echo $this->BForm->input('Recebsm.telefone', array('label' => false, 'class' => 'input-medium telefone','placeholder' => 'Telefone')) ?>
	<?php echo $this->BForm->input('Recebsm.radio', array('label' => false, 'class' => 'input-medium','placeholder' => 'Radio')) ?>

</div>

<h4>Origem</h4>
<div class='row-fluid inline'>
	<?php if($alvo_origem_padrao): ?>
		<?php echo $this->BForm->hidden('refe_codigo_origem'); ?>
		<?php echo $this->BForm->input('refe_codigo_origem_visual', array('label' => 'Local Origem','readonly' => true)); ?>
	<?php else: ?>
		<?php echo $this->Buonny->input_referencia($this, '#RecebsmCodigoCliente', 'Recebsm', 'refe_codigo_origem', false, 'Local Origem', true, true, '#RecebsmCodigoCliente2') ?>
	<?php endif; ?>
	<div class="control-group input text inline">
		<?php echo $this->BForm->input('Recebsm.dta_inc', array('label' => 'Data Inicio', 'class' => 'data input-small')) ?>
		<?php echo $this->BForm->input('Recebsm.hora_inc', array('label' => 'Hora', 'class' => 'hora input-mini')) ?>
		<?php echo $this->BForm->error('Recebsm.dta_hora_inc', null, array('style'=>"color:#b94a48; clear:both; margin-top: 60px; position: absolute;")) ?>
	</div>
	<?php echo $this->BForm->input('Recebsm.pedido_cliente', array('class' => 'input-small')) ?>
	<?php echo $this->BForm->input('Recebsm.operacao', array('label' => 'Tipo de Transporte', 'class' => 'input-small','value' => 'DISTRIBUIÇÃO','readonly' => true)) ?>	
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('escolha_temperatura', array('options' => $opcao_temperatura, 'label' => false)) ?>
	<div id="temperatura">
		<?php echo $this->BForm->input('Recebsm.temperatura', array('label' => false, 'class' => 'input-mini numeric temperatura', 'before'=>'faixa de:')) ?>
		<?php echo $this->BForm->input('Recebsm.temperatura2', array('label' => false, 'class' => 'input-mini numeric temperatura', 'before'=>'até:')) ?>
	</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('Recebsm.monitorar_retorno', array('label' => 'Monitorar retorno', 'type' => 'checkbox')) ?>	
	<div id="MonitorarRetorno" style="display:none" class="control-group input text">
		<?php echo $this->BForm->input('Recebsm.dta_fim', array('label' => 'Data Fim', 'class' => 'data input-small')) ?>
		<?php echo $this->BForm->input('Recebsm.hora_fim', array('label' => 'Hora', 'class' => 'hora input-mini')) ?>
		<?php echo $this->BForm->error('Recebsm.dta_hora_fim', null, array('style'=>"color:#b94a48; clear:both; margin-top: 60px; position: absolute;")) ?>
	</div>
</div>
</div>

<h4>Itinerário</h4>
<div id="itinerario">
	<div class="actionbar-right">
		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-destino', 'escape' => false)); ?>
	</div>
	<div class='row-fluid inline destino'>

	<?php for ($key = 0; $key < (isset($this->data['RecebsmAlvoDestino']) ? count($this->data['RecebsmAlvoDestino']) : 1); $key++): ?>
		<table class='table table-striped destino' data-index="<?php echo $key ?>">
			<thead>
				<th>
					<div class="row-fluid inline">
						<div class="refe_codigo_destino_select" style="display:none">
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.refe_codigo_select",array('label' => 'Itinerário Alvo','options' => array())); ?>
						</div>
						<div class="refe_codigo_destino">
							<?php echo $this->Buonny->input_referencia($this, '#RecebsmCodigoCliente', 'RecebsmAlvoDestino', 'refe_codigo', $key, 'Itinerário Alvo', true, true, '#RecebsmCodigoCliente2') ?>
						</div>
						<div class="janela_select" style="display:none">
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.ccja_codigo",array('label' => 'Janela','empty' => 'Janela','options' => (isset($janelas_cliente) ? $janelas_cliente : array()))); ?>
						</div>
						<div class="janela">
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.janela_inicio", array('label' => 'Janela Inicio', 'class' => 'hora input-mini janela-inicio')) ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.janela_fim", array('label' => 'Janela Fim', 'class' => 'hora input-mini janela-fim')) ?>
						</div>					
						
						<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.dataFinal", array('label' => 'Previsão Chegada', 'class' => 'data input-small', 'default' => date('d/m/Y'))) ?>
						<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.horaFinal", array('label' => 'Hora', 'class' => 'hora input-mini hora-final')) ?>
						<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.tipo_parada", array('label' => 'Tipo Itinerario', 'class' => 'input-small', 'value' => 'ENTREGA', 'readonly' => true)) ?>

						<?php if($key > 0): ?>
							<div class="control-group input text">
								<label for="RecebsmDtaFim">&nbsp</label>
								<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-destino-remove', 'escape' => false)); ?>				
							</div>
						<?php endif; ?>
					</div>
				</th>
			</thead>
			<tbody>
				<?php for ($keyNotas = 0; $keyNotas < (isset($this->data['RecebsmAlvoDestino'][$key]['RecebsmNota']) ? count($this->data['RecebsmAlvoDestino'][$key]['RecebsmNota']) : 1); $keyNotas++): ?>
					<tr>
						<td>
							<div class="row-fluid inline">
								<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaNumero", array('class' => 'input-mini', 'label' => 'Nº NF', 'maxlength' => 15)); ?>
								<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.carga", array('class' => 'input-medium carga-produtos','options' => $tipo_carga , 'empty' => 'Produto','label' => 'Produto', 'default' => $unico_produto == NULL ? '' : $unico_produto )) ?>
								<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaValor", array('class' => 'input-small moeda', 'label' => 'Valor da Nota')); ?>
								<label>&nbsp;</label>
								<?php if($keyNotas > 0): ?>
									<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-nota-remove', 'escape' => false)); ?>
								<?php else: ?>
									<?php echo $this->Html->link('<i class="icon-plus icon-white "></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-nota-fiscal', 'escape' => false)); ?>
								<?php endif; ?>
							</div>
						</td>
					</tr>
				<?php endfor ?>
			</tbody>
		</table>
	<?php endfor ?>
	</div>
</div>