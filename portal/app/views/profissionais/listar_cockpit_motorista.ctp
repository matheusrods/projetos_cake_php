<br/>
<div class="row-fluid inline">
	<?= $this->BForm->input('Profissional.data_inclusao', array('type' => 'text', 'label' => 'Data Cadastro', 'readonly' => true,  'class' => 'input-small')); ?>
	<?= $this->BForm->input('Profissional.estrangeiro', array('type' => 'text', 'label' => 'Estrangeiro', 'readonly' => true,  'class' => 'input-small')); ?>
	<?= $this->BForm->input('Profissional.codigo_documento', array('label' => 'CPF / RNE', 'readonly' => true,  'class' => 'input-medium')); ?>
	<?= $this->BForm->input('Profissional.nome', array('label' => 'Nome', 'readonly' => true, 'class' => 'input-xlarge')); ?>
</div>
<div class="row-fluid inline">
	<?= $this->BForm->input('Profissional.cnh', array('label' => 'CNH', 'readonly' => true, 'class' => 'input-small')); ?>
	<?= $this->BForm->input('Profissional.cnh_vencimento', array('type' => 'text', 'label' => 'Validade', 'readonly' => true, 'class' => 'input-small')); ?>
	<?= $this->BForm->input('TipoCnh.descricao', array('label' => 'Tipo CNH', 'readonly' => true,  'class' => 'input-mini')); ?>
	<?= $this->BForm->input('ProfissionalTelefone.descricao', array('label' => 'Telefone', 'readonly' => true, 'class' => 'input-medium telefone format-phone')); ?>
	<?= $this->BForm->input('ProfissionalRadio.descricao', array('label' => 'Rádio', 'readonly' => true, 'class' => 'input-medium')); ?>
</div>	
<div class="row-fluid inline">
	<?= $this->BForm->input('status_teleconsult', array('label' => 'Último Status Teleconsult', 'readonly' => true, 'class' => 'input-large', 'value' => $status_teleconsult['Status']['descricao'])); ?>
	<?= $this->BForm->input('data_status_teleconsult', array('label' => 'Data', 'readonly' => true, 'class' => 'input-small', 'value' => $status_teleconsult['FichaPesquisa']['data_inclusao'])); ?>
</div>	
<div id ='emb_transp'>
	<?php echo $this->element('profissionais/emb_transp') ?>
</div>

<div id ='rma'>
	<?php echo $this->element('profissionais/rma') ?>
</div>

<div id ='treinamento'>
	<?php echo $this->element('profissionais/treinamento') ?>
</div>

<div id ='teleconsult'>
	<?php echo $this->element('profissionais/teleconsult') ?>
</div>

<div id ='sinistro'>
	<?php echo $this->element('profissionais/sinistro') ?>
</div>

<div id ='origem_destino'>
	<?php echo $this->element('profissionais/origem_destino') ?>
</div>

<?php echo $this->Buonny->link_css('tablesorter') ?>
<?php echo $this->Buonny->link_js('jquery.tablesorter.min') ?>
