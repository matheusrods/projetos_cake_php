<?php if(isset($grupo_economico) && $grupo_economico) : ?>
	<?php echo $this->BForm->hidden('GrupoEconomicoCliente.codigo_grupo_economico', array('value' => $grupo_economico)); ?>
<?php endif; ?>
<div class="row-fluid">
	<span class="span9">
		<div class="row-fluid inline">
			<?php if($edit_mode): ?>
				<?php echo $this->BForm->input('codigo',array('class' => 'input-mini', 'type' => 'text', 'label' => 'Código', 'readonly' => true)); ?>
			<?php else: ?>
				<?php echo $this->BForm->hidden('codigo'); ?>
			<?php endif; ?>
			<?php echo $this->BForm->hidden('data_inclusao', array('readonly' => $edit_mode)); ?>
			<?php echo $this->Form->hidden('codigo_documento'); ?>
			<?php echo $this->BForm->input('codigo_documento_real', array('class' => 'input-medium cnpj', 'label' => 'CNPJ / CPF', 'readonly' => $edit_mode));?>
			<?php echo $this->BForm->input('inscricao_estadual', array('class' => 'input-medium', 'label' => 'RG / Inscrição Estadual', 'value' =>'ISENTO', 'readonly' => true)); ?>
			<?php echo $this->BForm->input('ccm', array('label' => 'Inscrição Municipal', 'class' => 'input-small', 'value' =>'ISENTO', 'readonly' => true)); ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('codigo_regime_tributario', array('class' => 'input-medium', 'options' => array('1' => 'Simples Nacional'),'label' => 'Regime Tributário', )); ?>
			<?php 
			?>

			<?php if(isset($codigo_matriz) && !empty($codigo_matriz)) {
				echo $this->Form->hidden('codigo_cliente', array('value' => $codigo_matriz));
			} ?>
			<?php if($edit_mode) : ?>
				<?php echo $this->BForm->input('ativo', array('class' => 'input-mini', 'options' => array('Inativo', 'Ativo'))); ?>
			<?php endif; ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_cnae($this, 'cnae', 'CNAE','CNAE', null, (isset($this->data['Cliente']['cnae']) ? $this->data['Cliente']['cnae'] : ''), (isset($this->data['Cnae']['descricao']) ? $this->data['Cnae']['descricao'] : ''));?>
			<?php echo $this->BForm->input('grau_risco', array('label' => 'Grau de Risco', 'class' => 'input-mini', 'readonly' => true, 'value' => (isset($this->data['Cnae'])) ? $this->data['Cnae']['grau_risco'] : '')); ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_medico_readonly($this, 'codigo_medico_pcmso', 'Coord PCMSO', 'Coord PCMSO','Cliente', null, 'numero_conselho_pcmso', 'uf_conselho_pcmso', 'nome_medico_pcmso', 'cpf_medico_pcmso'); ?>

			<?php echo $this->BForm->input('numero_conselho_pcmso', array('style' => 'width: 80px;', 'label' => 'CRM', 'title' => ('CRM'), 'readonly' => true, 'value' => (isset($medico['Medico']['numero_conselho'])) ? $medico['Medico']['numero_conselho'] : '')); ?>

			<?php echo $this->BForm->input('uf_conselho_pcmso', array('style' => 'width: 50px;', 'label' => 'UF', 'title' => ('UF'), 'readonly' => true, 'value' => (isset($medico['Medico'])) ? $medico['Medico']['conselho_uf']  : '')); ?>

			<?php echo $this->BForm->input('nome_medico_pcmso', array('style' => 'width: 260px;', 'label' => 'Nome do Médico', 'title' => ('NOME'), 'readonly' => true, 'value' => (isset($medico['Medico'])) ? $medico['Medico']['nome']  : '')); ?>

			<?php echo $this->BForm->input('cpf_medico_pcmso', array('style' => 'width: 260px;', 'label' => 'CPF do Médico', 'title' => ('CPF'), 'readonly' => true, 'value' => (isset($medico['Medico'])) ? $medico['Medico']['cpf']  : '')); ?>
    	</div>	
	</span>
	<?php if($edit_mode): ?>
		<span class="span3 well">
			<ul class="unstyled">
				<li>
					<span class="label label-info">Data Inclusão</span>
					<ul class="unstyled">
						<li><?= $this->data['Cliente']['data_inclusao']; ?></li>
					</ul>
				</li>
				<li>
					<span class="label label-info">Data Atualização</span>
					<ul class="unstyled">
						<li><?= $html->link($ultimo_log['ClienteLog']['data_inclusao'], array('controller' => 'clientes_log', 'action' => 'listagem_tomador_servico', $this->data['Cliente']['codigo']), array('onclick' => "return open_dialog(this, '6 últimas alterações', 950, 500)", 'title' => 'Exibir últimas alterações'));?></li>
						<li><?= $ultimo_log['UsuarioAlteracao']['apelido'] ?></li>
					</ul>
				</li>
			</ul>
		</span>
	<?php endif; ?>
</div>
<?php echo $this->element('clientes_tomadores_enderecos/fields_tomadores') ?>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function() {
		tipo_filial_cliente();
	});

	function tipo_filial_cliente(){
		var tipo_unidade = $('#ClienteTipoUnidade');
		if(tipo_unidade.val() == 'O'){
			$('#fornecedor_matriz_filial').show();
			$('#fornecedor_matriz').hide();   
			$('#ClienteCodigoDocumento').attr('readonly', true);
		}
		else{
			$('#fornecedor_matriz').show();
			$('#fornecedor_matriz_filial').hide();   
			$('#ClienteCodigoDocumento').attr('readonly', false);
		}
	}	
"); ?>  
<script type="text/javascript">
$(document).ready(function(){
	 $('.js-example-basic-single').select2();
	var i = 1;
	$('body').on('click', '.js-add-cid', function() {
		var html = $(this).parents('.js-encapsulado').find('.js-memory').html().replace(/xx/g, i).replace(/Xx/g, i).replace(/disabled="disabled"/g, '');
		$(this).parents('.js-encapsulado').append(html).find('.inputs-config.hide').show();
		$(this).removeClass('js-add-cid').addClass('js-remove-cid').attr('data-original-title', 'Remover doença').children('i').removeClass('icon-plus').addClass('icon-minus');
		$('[data-toggle="tooltip"]').tooltip();
		i++;
	});//FINAL CLICK js-add-cid
	
	$('body').on('click', '.js-remove-cid', function() {
		$(this).parents('.inputs-config').remove();
	});//FINAL CLICK js-remove-cid
	// modulo CID
	var timer;
	$("body").on('keyup', '.js-medicos', function() {
		var este = $(this);
		var string = this.value;
		if(string != '') {
			este.parent().css('position', 'relative');
			$('.loader-gif').remove();
			este.parent().append(' <img src="'+baseUrl+'img/default.gif" style="margin-top: -10px;" class="loader-gif">');
			$('.seleciona-cid-10').remove();
			clearTimeout(timer); 
			timer = setTimeout(function() {
				$.ajax({
					url: baseUrl + 'medicos/carrega_medicos_para_ajax/',
					type: 'POST',
					dataType: 'json',
					data: {string: string},
				})
				.done(function(response) {
					if(response) {
						var canvas = $('<div>', {class: 'seleciona-cid-10'}).html(response);
						este.parent().append(canvas);
					}
				})
				.always(function() {
					$('.loader-gif').remove();
				});
			}, 500);
		} else {
			$('.seleciona-cid-10').remove();
			$('.loader-gif').remove();
		}
	});//FINAL keyup CLASSE js-cid-10

	$('body').on('click', '.js-medico-click', function() {
		
		$(this).parents('.checkbox-canvas').find('.js-medicos').val($(this).find('td:first-child').text());
		$('#ClienteCodigoMedicoResponsavel').val($(this).attr('data-codigo'));

		$('.seleciona-cid-10').remove();
	});//FINAL click CLASSE js-cid-click

	$('body').click(function(event) {
		$('.seleciona-cid-10').remove();
	});
	// ===============
	$('#ClienteCnaeDescricao').change(function(){
		$.ajax({
			  type: 'GET',  
              url: baseUrl + 'Clientes/carrega_grau_risco/' + $('#ClienteCnae').val(),
              dataType: 'json',
              beforeSend: function() {
                  bloquearDiv($('.form-procurar'));
              },
             
              success: function(dados_cnae) {
                    $('#ClienteGrauRisco').val(dados_cnae);
                      
              },
              complete: function(dados_cnae){
                  ($('.form-procurar')).unblock();
              }
          });
      });
});
</script>