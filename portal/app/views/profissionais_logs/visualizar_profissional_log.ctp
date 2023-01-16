<legend>Profissional Log</legend>
<div class="row-fluid inline">
	<?php echo $this->BForm->input("ProfissionalLog.codigo_documento", array('label' => 'CPF', 'class' => 'input-small cpf', 'readonly'=>true)) ?>
	<?php echo $this->BForm->input("ProfissionalLog.nome", array('label' => 'Nome do ProfissionalLog', 'class' => 'input-large just-letters', 'readonly'=>true)) ?>
	<?php echo $this->BForm->input("ProfissionalLog.nome_pai", array('label' => 'Nome do Pai', 'class' => 'input-large just-letters', 'readonly'=>true)) ?>
	<?php echo $this->BForm->input("ProfissionalLog.nome_mae", array('label' => 'Nome da Mãe', 'class' => 'input-large just-letters', 'readonly'=>true)) ?>
</div>

<div class="row-fluid inline">
	<div class="span5">
		<h5>RG - Registro Geral</h5>
		<?php echo $this->BForm->input("ProfissionalLog.rg", array('label' => 'RG', 'class' => 'input-small', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input("ProfissionalLog.codigo_estado_rg", array('label' => 'UF', 'class' => 'input-mini', 'options'=>$endereco_estado, 'empty'=>'UF', 'disabled'=>true)) ?>
		<?php echo $this->BForm->input("ProfissionalLog.rg_data_emissao", array('label' => 'Data Emissão', 'class' => 'input-small', 'type'=>'text', 'readonly'=>true)) ?> 
	</div>
	<div class="span7">
		<h5>Naturalidade</h5>
		<?php echo $this->BForm->input("ProfissionalLog.codigo_estado_naturalidade", array('label' => 'Estado', 'class' => 'input-mini estado', 'empty' => 'Estado', 'options'=>$endereco_estado, 'disabled'=> true ));?>
		<?php echo $this->BForm->input("ProfissionalLog.codigo_endereco_cidade_naturalidade", array('label' => 'Cidade', 'class' => 'input-large cidade', 'empty' => 'Cidade', 'options'=>$cidades_profissional_log, 'disabled'=> true));?>
		<?php echo $this->BForm->input("ProfissionalLog.data_nascimento", array('label' => 'Data Nascimento', 'class' => 'input-small', 'type'=>'text', 'disabled'=>true)) ?>
	</div>
</div>

<div class="row-fluid inline">
	<div class="span12">
		<h5>CNH - Carteira Nacional de Habilitação</h5>
		<?php echo $this->BForm->input("ProfissionalLog.cnh", array('label' => 'Número de registro', 'class' => 'input-medium just-number cnh', 'size' => 11, 'maxlength' => 11, 'disabled'=>true)) ?>
		<?php echo $this->BForm->input("ProfissionalLog.codigo_tipo_cnh", array('label' => 'Categoria', 'class' => 'input-mini', 'options'=>$tipo_cnh, 'empty'=>'Categoria', 'disabled'=>true)) ?>
		<?php echo $this->BForm->input("ProfissionalLog.cnh_vencimento", array('label' => 'Vencimento', 'class' => 'input-small', 'type'=>'text', 'disabled'=>true)) ?>
		<?php echo $this->BForm->input("ProfissionalLog.codigo_endereco_estado_emissao_cnh", array('label' => 'UF de emissão', 'class' => 'input-mini', 'options'=>$endereco_estado, 'empty'=>'UF', 'disabled'=>true)) ?>
		<?php echo $this->BForm->input("ProfissionalLog.data_primeira_cnh", array('label' => 'Data da primeira CNH', 'class' => 'input-small', 'type'=>'text', 'disabled'=>true)) ?>
		<?php echo $this->BForm->input("ProfissionalLog.codigo_seguranca_cnh", array('label' => 'Código de segurança <i class="icon-question-sign"></i>', 'class' => 'input-medium just-number codigo-seguranca', 'type'=>'text', 'size' => 11, 'maxlength' => 11, 'disabled'=>true)) ?>
	</div>
</div>
<div class="row-fluid inline mop-data">
	<?php echo $this->BForm->input("ProfissionalLog.possui_mopp", array('label' => 'Possui MOPP', 'class' => 'input-mini', 'options'=>array('1'=>'Sim','0'=>'Não'), 'empty'=>'', 'disabled'=>true)) ?>
	<span id="divMopp" style="display:<?=(!empty($this->data['ProfissionalLog']['possui_mopp']) ? '' : 'none')?>;">
		<?php echo $this->BForm->input("ProfissionalLog.data_inicio_mopp", array('label' => 'Data Início MOPP', 'class' => 'input-small', 'type'=>'text', 'disabled'=>true)) ?>
	<span>
</div>


<div class="cep-data" id="cep-profissionalLog">
	<div class="row-fluid inline">
		<h5>Endereço</h5>
		<?php echo $this->BForm->input("ProfissionalEnderecoLog.endereco_cep", array('class' => 'evt-endereco-cep input-mini formata-cep', 'label' => 'CEP', 'disabled'=> true ));?>		
		<?php echo $this->BForm->input("ProfissionalEnderecoLog.codigo_endereco", array('label' => 'CEP', 'class' => 'evt-endereco-cep formata-cep', 'options'=>$profissional_enderecos_log, 'disabled'=>true)) ?>
	    
	    <div class="clear">
	        <?php echo $this->BForm->input('ProfissionalEnderecoLog.numero', array('class' => 'input-mini evt-endereco-numero just-number', 'size' => 6, 'maxlength' => 6, 'label' => 'Número', 'disabled'=> true)); ?>
	        <?php echo $this->BForm->input('ProfissionalEnderecoLog.complemento', array('class' => 'input-small complemento', 'label' => 'Complemento', 'disabled'=> true)); ?>
	    </div>
	</div>
</div>

<?php echo $this->element('fichas_scorecard/lista_contatos', array(
	'titulo'=>'Contatos do ProfissionalLog', 
	'listaContatos'=>isset($this->data['ProfissionalContatoLog']) ? $this->data['ProfissionalContatoLog'] : array(), 
	'tipo'=>'profissionalLog', 
	'index'=>'profissional_contatoLog', 
	'model'=>'ProfissionalContatoLog',
	'tipo_retorno'=> $tipo_retorno,
	'disabled' => true
))?>
<div class='form-actions'>
    <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		setup_categoria();
		setup_codigo_seguranca();
		setup_mascaras();		
});', false);?>