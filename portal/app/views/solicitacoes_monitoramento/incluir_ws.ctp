<?php echo $retorno = $this->BForm->error_menssage($retorno) ?>

<?php echo $this->BForm->create('SolicitacaoMonitoramento', array('url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'incluir_ws')));?>
<h4>Cliente</h4>
<div class='row-fluid inline'>  
	<?php echo $this->BForm->input('usuario', array('label' => false, 'placeHolder' => 'Usuário', 'class' => 'input-small')); ?>
	<?php echo $this->BForm->input('senha', array('label' => false, 'placeHolder' => 'Senha', 'class' => 'input-small', 'type' => 'password')); ?>
</div>
<h4>Solicitante</h4>
<div class='row-fluid inline'>  
	<?php echo $this->BForm->input('Solicitante.nome', array('label' => false, 'placeHolder' => 'Solicitante', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('Solicitante.telefone', array('label' => false, 'placeHolder' => 'Telefone', 'class' => 'input-xlarge')); ?>
</div>
<h4>Viagem</h4>
<div class='row-fluid inline'>  
    <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeHolder' => 'Início Viagem', 'class' => 'data input-medium')); ?>
    <?php echo $this->BForm->input('data_fim', array('label' => false, 'placeHolder' => 'Final Viagem', 'class' => 'data input-medium')); ?>
    <?php echo $this->BForm->input('transportadora', array('label' => false, 'placeHolder' => 'Transportadora', 'class' => 'input-xlarge')); ?>
</div>
<h4>Origem</h4>
<div class='row-fluid inline'>  
    <?php echo $this->BForm->input('Origem.cep', array('label' => false, 'placeHolder' => 'Origem', 'class' => 'input-small')); ?>
</div>
<h4>Destino</h4>
<div class='row-fluid inline'>  
    <?php echo $this->BForm->input('Destino.cep', array('label' => false, 'placeHolder' => 'Destino', 'class' => 'input-small')); ?>
</div>
<h4>Profissional</h4>
<div class='row-fluid inline'>  
    <?php echo $this->BForm->input('Profissional.codigo_documento', array('label' => false, 'placeHolder' => 'CPF/CNPJ', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('Profissional.numero_liberacao', array('label' => false, 'placeHolder' => 'Liberação Nº', 'class' => 'input-small')); ?>
</div>
<h4>Veículo</h4>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('Veiculo.placa', array('label' => false, 'placeHolder' => 'Placa', 'class' => 'input-small')); ?>
</div>
<h4>Itinerário</h4>
<div class="itinerario">
	<h5>Ponto</h5>
	<div class='row-fluid inline'>
	    <?php echo $this->BForm->input('Atendimento.0.rota', array('name' => 'data[Atendimento][0][rota]', 'label' => false, 'placeHolder' => 'Rota', 'class' => 'input-medium')); ?>
	</div>
	<h5>Localidade</h5>
	<div class='row-fluid inline'>
	    <?php echo $this->BForm->input('Atendimento.0.cep', array('name' => 'data[Atendimento][0][cep]', 'label' => false, 'placeHolder' => 'Cep', 'class' => 'input-small')); ?>
	</div>
	<h5>Empresa</h5>
	<div class='row-fluid inline'>
	    <?php echo $this->BForm->input('Atendimento.0.codigo_documento', array('name' => 'data[Atendimento][0][codigo_documento]', 'label' => false, 'placeHolder' => 'CPF/CNPJ', 'class' => 'input-medium')); ?>
	    <?php echo $this->BForm->input('Atendimento.0.nome', array('name' => 'data[Atendimento][0][nome]', 'label' => false, 'placeHolder' => 'Nome', 'class' => 'input-xlarge')); ?>
	</div>
	<h5>Nota Fiscal</h5>
	<div class='row-fluid inline'>
	    <?php echo $this->BForm->input('Atendimento.0.nota_fiscal', array('name' => 'data[Atendimento][0][nota_fiscal]', 'label' => false, 'placeHolder' => 'Nota Fiscal Nº', 'class' => 'input-small')); ?>
	    <?php echo $this->BForm->input('Atendimento.0.valor', array('name' => 'data[Atendimento][0][valor]', 'label' => false, 'placeHolder' => 'Valor', 'class' => 'input-small')); ?>
	</div>
	<h5>Carga</h5>
	<div class='row-fluid inline'>
	    <?php echo $this->BForm->input('Atendimento.0.peso', array('name' => 'data[Atendimento][0][peso]', 'label' => false, 'placeHolder' => 'Peso', 'class' => 'input-small')); ?>
	    <?php echo $this->BForm->input('Atendimento.0.volume', array('name' => 'data[Atendimento][0][volume]', 'label' => false, 'placeHolder' => 'Volume', 'class' => 'input-small')); ?>
	</div>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?php //echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>