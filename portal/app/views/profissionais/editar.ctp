<legend>Profissional</legend>
<?php echo $this->BForm->create('Profissional', array('url' => array('controller' => 'profissionais', 'action' => 'editar')));?>
<div class="row-fluid inline">
	<?php echo $this->BForm->hidden("Profissional.codigo") ?>
	<?php echo $this->BForm->input("Profissional.codigo_documento", array('label' => 'CPF', 'class' => 'input-small cpf', 'readonly'=>true)) ?>
	<?php echo $this->BForm->input("Profissional.nome", array('label' => 'Nome do Profissional', 'class' => 'input-large just-letters')) ?>
	<?php echo $this->BForm->input("Profissional.nome_pai", array('label' => 'Nome do Pai', 'class' => 'input-large just-letters')) ?>
	<?php echo $this->BForm->input("Profissional.nome_mae", array('label' => 'Nome da Mãe', 'class' => 'input-large just-letters')) ?>
</div>

<div class="row-fluid inline">
	<div class="span5">
		<h5>RG - Registro Geral</h5>
		<?php echo $this->BForm->input("Profissional.rg", array('label' => 'RG', 'class' => 'input-small')) ?>
		<?php echo $this->BForm->input("Profissional.codigo_estado_rg", array('label' => 'UF', 'class' => 'input-mini', 'options'=>$endereco_estado, 'empty'=>'UF')) ?>
		<?php echo $this->BForm->input("Profissional.rg_data_emissao", array('label' => 'Data Emissão', 'class' => 'input-small data', 'type'=>'text')) ?> 
	</div>
	<div class="span7">
		<h5>Naturalidade</h5>
		<?php echo $this->Buonny->combo_estado_cidade($this, "Profissional.codigo_estado_naturalidade", "Profissional.codigo_endereco_cidade_naturalidade", $endereco_estado, $cidades_profissional)?>
		<?php echo $this->BForm->input("Profissional.data_nascimento", array('label' => 'Data Nascimento', 'class' => 'input-small data', 'type'=>'text')) ?>
	</div>
</div>

<div class="row-fluid inline">
	<div class="span12">
		<h5>CNH - Carteira Nacional de Habilitação</h5>
		<?php echo $this->BForm->input("Profissional.cnh", array('label' => 'Número de registro', 'class' => 'input-medium just-number cnh', 'size' => 11, 'maxlength' => 11)) ?>
		<?php echo $this->BForm->input("Profissional.codigo_tipo_cnh", array('label' => 'Categoria', 'class' => 'input-mini', 'options'=>$tipo_cnh, 'empty'=>'Categoria')) ?>
		<?php echo $this->BForm->input("Profissional.cnh_vencimento", array('label' => 'Vencimento', 'class' => 'input-small data', 'type'=>'text')) ?>
		<?php echo $this->BForm->input("Profissional.codigo_endereco_estado_emissao_cnh", array('label' => 'UF de emissão', 'class' => 'input-mini', 'options'=>$endereco_estado, 'empty'=>'UF')) ?>
		<?php echo $this->BForm->input("Profissional.data_primeira_cnh", array('label' => 'Data da primeira CNH', 'class' => 'input-small data', 'type'=>'text')) ?>
		<?php echo $this->BForm->input("Profissional.codigo_seguranca_cnh", array('label' => 'Código de segurança <i class="icon-question-sign"></i>', 'class' => 'input-medium just-number codigo-seguranca', 'type'=>'text', 'size' => 11, 'maxlength' => 11)) ?>
	</div>
</div>
<div class="row-fluid inline">
	<h5>Endereço</h5>
	<?php echo $this->Buonny->combo_cep_endereco($this, 'ProfissionalEndereco.endereco_cep', 'ProfissionalEndereco.codigo_endereco', $profissional_enderecos); ?>
    <div class="clear">
        <?php echo $this->BForm->input('ProfissionalEndereco.numero', array('class' => 'input-mini evt-endereco-numero just-number', 'size' => 6, 'maxlength' => 6, 'label' => 'Número')); ?>
        <?php echo $this->BForm->input('ProfissionalEndereco.complemento', array('class' => 'input-small complemento', 'label' => 'Complemento')); ?>
    </div>
</div>

<?php echo $this->element('fichas_scorecard/lista_contatos', array(
	'titulo'=>'Contatos do Profissional', 
	'listaContatos'=>isset($this->data['ProfissionalContato']) ? $this->data['ProfissionalContato'] : array(), 
	'tipo'=>'profissional', 
	'index'=>'profissional_contato', 
	'model'=>'ProfissionalContato',
	'tipo_retorno'=> $tipo_retorno_profissional
))?>

<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		// setup_codigo_documento_profissional();
		setup_categoria();
		setup_codigo_seguranca();
		setup_mascaras();
		setup_datepicker();
		$(".limpar-filtro").click(function(){
			$("#ProfissionalIncluirForm").each( function() { 
				this.reset(); 
			});
		});
	});', false);?>