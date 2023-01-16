
<?php echo $this->BForm->create('Cliente', array('type' => 'file', 'enctype' => 'multipart/form-data','url' => array('controller' => 'clientes','action' => 'configuracao_grupo_economico_padrao', $codigo_matriz,$referencia, $terceiros_implantacao), 'type' => 'post')); ?>
	
	<div class='well row-fluid inline'>
        <strong>Código: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['codigo']); ?>
        <strong>Cliente: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['razao_social']); ?>
    </div>
	
	<div class="well row-fluid inline">

		<h4>Médico PCMSO Padrão:</h4>
		<?php echo $this->Buonny->input_codigo_medico_readonly($this, 'codigo_medico_pcmso', 'Coord PCMSO', 'Coord PCMSO','Cliente', null, 'numero_conselho_pcmso', 'uf_conselho_pcmso', 'nome_medico_pcmso', 'cpf_medico_pcmso'); ?>
		
		<?php echo $this->BForm->input('numero_conselho_pcmso', array('style' => 'width: 80px;', 'label' => 'CRM', 'title' => ('CRM'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['numero_conselho'] : '')); ?>
		<?php echo $this->BForm->input('uf_conselho_pcmso', array('style' => 'width: 50px;', 'label' => 'UF', 'title' => ('UF'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['conselho_uf']  : '')); ?>
		<?php echo $this->BForm->input('nome_medico_pcmso', array('style' => 'width: 260px;', 'label' => 'Nome do Médico', 'title' => ('NOME'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['nome']  : '')); ?>
		<?php echo $this->BForm->input('cpf_medico_pcmso', array( 'class' => 'input-medium cpf', 'label' => 'CPF do Médico', 'title' => ('CPF'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['cpf']  : '')); ?>
	</div>

	<div class="well row-fluid inline">

		<h4>Exames quantidade de dias a vencer:</h4>
		
		<?php echo $this->BForm->input('exames_dias_a_vencer', array('label' => 'Quantidade Dias', 'title' => ('Qtd. Dias'), 'value' => (isset($this->data['exames_dias_a_vencer'])) ? $this->data['exames_dias_a_vencer'] : '')); ?>		
	</div>

	<div class="well row-fluid inline">

		<h4>Lyn:</h4>

		<div class='row-fluid'>
			<?php echo $this->BForm->input('codigo_nina_validacao', array('label' => 'Codigo Vínculo Lyn', 'title' => ('Codigo Vínculo Lyn'), 'after'=>$html->link('', 'javascript:void(0)', array('class' => 'icon-refresh novoCodigo', 'title' => 'Gerar codigo', 'style' => 'position: relative;top: -5px;right: -5px;')), 'value' => (isset($this->data['codigo_nina_validacao'])) ? $this->data['codigo_nina_validacao'] : '')); ?>
		</div>

		<div class='row-fluid'>
			<div class='control-group input checkbox'>
				<?php echo $this->BForm->input('exame_atraves_lyn', array('label' => 'Exame através do Lyn','type' => 'checkbox', 'div' => false)) ?>
			</div>
		</div>

		<div class='row-fluid row-servico'>
			<?php echo $this->BForm->input('codigo_exame', array('label' => 'Exame(*)', 'class' => 'input-xxlarge','options' => $exames, 'default' => '', 'empty' => 'Selecione')); ?>

			<?php echo $this->BForm->input('codigo_medico', array('label' => 'Médico(*)', 'class' => 'input-xxlarge','options' => $medicos, 'default' => '', 'empty' => 'Selecione')); ?>
		</div>		

		<div class='row-fluid'>
			<label>Menu Lyn <b>*Caso não selecionado nenhum, todos os menus serão apresentados</b>:</label>
			<?php
			foreach($this->data['lyn_menu'] AS $lyn_menu) {

				$checked = '';
				if(in_array($lyn_menu['LynMenu']['codigo'],$this->data['lyn_menu_sel'])) {
					$checked = 'checked="checked"';
				}
				
				echo '<div class="control-group input clear checkbox"> 
						<input type="checkbox" name="data[Cliente][lyn_menu][]" value="'.$lyn_menu['LynMenu']['codigo'].'" class="input-large" id="ClienteLynMenu_'.$lyn_menu['LynMenu']['codigo'].'" '.$checked.'   >
						<label for="ClienteLynMenu_'.$lyn_menu['LynMenu']['codigo'].'">'.$lyn_menu['LynMenu']['descricao'].'</label>
					</div>';
				
			}
			?>
		</div>
	</div>

	<div class="well row-fluid inline">

		<h4>Thermal Care:</h4>
		
		<label>Menu Thermal Care <b>*Caso não selecionado nenhum, todos os menus serão apresentados</b>:</label>
		<div class="span12">
		<div class="row-fluid">

		<?php
		foreach($this->data['therma_menu'] AS $therma_menu) {

			$checked = '';
			if(in_array($therma_menu['LynMenu']['codigo'],$this->data['therma_menu_sel'])) {
				$checked = 'checked="checked"';
			}
			
			echo '<div class="control-group input clear checkbox"> 
					<input type="checkbox" name="data[Cliente][therma_menu][]" value="'.$therma_menu['LynMenu']['codigo'].'" class="input-large" id="ClienteThermaMenu_'.$therma_menu['LynMenu']['codigo'].'" '.$checked.'   >
					<label for="ClienteThermaMenu_'.$therma_menu['LynMenu']['codigo'].'">'.$therma_menu['LynMenu']['descricao'].'</label>
				</div>';
			
		}
		?>
		
		</div>
		</div>
		
		<?php
		// PC-95 ONBOARDING
		// obter onboarding do therma care	?>		

		<h5>Onboarding </h5>
		<p>Configure qual Onboarding deverá aparecer.</p>
		
		<hr />

		<?php
		
		foreach($this->data['therma_onboarding'] AS $onboarding) {
		?>
			<div class="row-fluid">
				<div class="span12">
				
				<?php
					$checked = $onboarding['ativo'] == 1 ? 'checked="checked"' : '';
					
					echo '<div class="control-group input clear checkbox">
						<input type="checkbox" name="data[Cliente][therma_onboarding][]" value="'.$onboarding['codigo'].'" class="input-large" id="ClienteThermaOnboarding_'.$onboarding['codigo'].'" '.$checked.'   >
						<label for="ClienteThermaOnboarding_'.$onboarding['codigo'].'">'.$onboarding['titulo'].'</label>
						</div>';
				?>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span4">

					<?php echo $this->BForm->input('therma_onboarding_titulo_'.$onboarding['codigo'], array('label' => 'Titulo', 'title' => ('Título'), 'value' => $onboarding['titulo'])); ?>

				</div>
				<div class="span4">

					<?php echo $this->BForm->input('therma_onboarding_texto_'.$onboarding['codigo'], array('label' => 'Texto', 'title' => ('Texto'), 'value' => $onboarding['texto'])); ?>

				</div>

			</div>
			<div class="row-fluid">
				<div class="span3">

							<?php echo $this->BForm->input('Cliente.therma_onboarding_imagem_'.$onboarding['codigo'], array('type' => 'file', 'class' => 'input-xlarge', 'label' => '')); ?>
							<i>Dimensões mínimas do background '1200px' por '800px'</i>
							<p style="color:red;font-style: italic;font-size: 11px;">As imagens são limitadas às extensões png, jpg e jpeg. O tamanho limite é de 2MB. </p>

						</div>
						<div class="span6">
							<div style="width:80%;height:80%; border:1px solid #ccc; padding:5px;background-align:center;">
							<?php if(isset($onboarding['imagem']) && !empty($onboarding['imagem'])) : 

									$imagem = (strpos($onboarding['imagem'], 'https://api.rhhealth.com.br') !== false) ? $onboarding['imagem'] : 'https://api.rhhealth.com.br' . $onboarding['imagem'];
							?>
									<img src="<?php echo $imagem; ?>" />
							<?php else: ?>		
									<p style="margin:30px">Imagem não encontrada.</p>
							<?php endif; ?>
							</div>				
						</div>
					
				</div>


		<?php }?>

	</div>

    <div class="well row-fluid inline">

        <h4>Exibir Centro de Custo no demonstrativo per capita:</h4>

        <?php echo $this->BForm->input('exibir_centro_custo_per_capita', array('type' => 'radio', 'options' => array('0' => 'Não', '1' => 'Sim'), 'legend' => false, 'title' => 'Exibir centro de custo no demonstrativo per capita', 'label' => array('class' => 'radio inline input-xsmall'))) ?>

    </div>

	<div class="well row-fluid inline">

        <h4>Utilizar códigos externos para definição de GHE:</h4>

        <?php echo $this->BForm->input('utilizar_codigos_externos_ghe', array('type' => 'radio', 'options' => array('0' => 'Não', '1' => 'Sim'), 'legend' => false, 'title' => 'Utilizar códigos externos para definição de GHE', 'label' => array('class' => 'radio inline input-xsmall'))) ?>
    
	</div>

    <div class="well row-fluid inline">

        <h4>Exibir Nome Fantasia no ASO:</h4>

        <?php echo $this->BForm->input('exibir_nome_fantasia_aso', array('type' => 'radio', 'options' => array('0' => 'Não', '1' => 'Sim'), 'legend' => false, 'title' => 'Exibir Nome Fantasia no relatório do ASO', 'label' => array('class' => 'radio inline input-xsmall'))) ?>

    </div>

    <div class="well row-fluid inline">

        <h4>Exibir RQE no ASO:</h4>

        <?php echo $this->BForm->input('exibir_rqe_aso', array('type' => 'radio', 'options' => array('0' => 'Não', '1' => 'Sim'), 'legend' => false, 'title' => 'Exibir RQE no relatório do ASO', 'label' => array('class' => 'radio inline input-xsmall'))) ?>
    </div>

	<div class="well row-fluid inline">

		<h4>Imprimir ASO em:</h4>
		
		<?php
		foreach($this->data['idiomas_aso'] AS $idiomas_aso) { 

			$checked = '';
			if(in_array($idiomas_aso['IdiomasAso']['codigo'], explode(",",$this->data['idiomas_aso_sel']))) {
				$checked = 'checked="checked"';
			}
			
			echo '<div class="control-group input clear checkbox" style="float:none;"> 
					<input type="checkbox" name="data[Cliente][idiomas_aso][]" value="'.$idiomas_aso['IdiomasAso']['codigo'].'" class="input-large" id="ClienteIdiomasAso_'.$idiomas_aso['IdiomasAso']['codigo'].'" '.$checked.' >
					<label for="ClienteIdiomasAso_'.$idiomas_aso['IdiomasAso']['codigo'].'">'.$idiomas_aso['IdiomasAso']['descricao'].'</label>
				</div>';
			
		}
		?>
		
		<?php echo $this->Form->input('descricao_idioma', array('type' => 'textarea', 'class' => 'input-large', 'maxlength'=>525, 'rows'=>5, 'onkeyup'=>'formatText(this)','value' => (isset($this->data['descricao_idioma'])) ? $this->data['descricao_idioma'] : '', 'label' => 'Texto adicional:', 'style'=>'resize:none; display:block; width:650px;')); ?>
		<br>
		<div class="input clear"><span id="rchars">525</span> caracteres restantes</div>

		<div class="control-group input clear checkbox" style="float:none;"> 
			<input type="checkbox" name="data[GrupoEconomico][aso_exames_linha]" value="1" class="input-large" id="GrupoEconomicoAsoExamesLinha" 
				<?php if(!empty($this->data['aso_exames_linha'])){ echo 'checked="checked"'; } ?> >
			<label for="GrupoEconomicoAsoExamesLinha">Exibir no Aso os exames em linha.</label>
		</div>

	</div>

	<div class="well row-fluid inline">

        <h4>Exibir ASO Embarcado:</h4>

        <?php echo $this->BForm->input('aso_embarcado', array('type' => 'radio', 'options' => array('0' => 'Não', '1' => 'Sim'), 'legend' => false, 'title' => 'Exibir ASO Embarcado na emissão de pedidos de exame', 'label' => array('class' => 'radio inline input-xsmall'))) ?>
    </div>

	<div class="well row-fluid inline">
        <h4>Grupo Empresa eSocial:</h4>
        <?php echo $this->BForm->input('codigo_grupo_empresa', array('options' => $combo_grupo_empresas, 'empty' => 'Selecione o Grupo Empresa', 'class' => 'input-xxlarge', 'legend' => false, 'label' => false)) ?>
	</div>

	<div class="form-actions">
	    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>

	    <?php if(!empty($referencia)): ?>
	        <?php if($referencia == 'implantacao_terceiros'): ?>
	            <?php echo $html->link('Voltar', array('action' => 'listagem_terceiros_unidades', $codigo_matriz, $referencia), array('class' => 'btn')); ?>
	        <?php else:?>
	        	<?php if($terceiros_implantacao == 'terceiros_implantacao'): ?>
	            	<?php echo $html->link('Voltar', array('action' => 'index_unidades', $codigo_matriz, $referencia, 'null', $terceiros_implantacao), array('class' => 'btn')); ?>
	        	<?php else: ?>
	            	<?php echo $html->link('Voltar', array('action' => 'index_unidades', $codigo_matriz, $referencia), array('class' => 'btn')); ?>
	        	<?php endif; ?>
	        <?php endif;?>
	    <?php else:?>
	        <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
	    <?php endif;?>
	</div>    
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Buonny->link_js('clientes.js')); ?>


<?php echo $javascript->codeBlock("
    var number=Math.floor((Math.random()*999999)+1);    
    $('.novoCodigo').click(function(){
        $.ajax({
            url: baseUrl + 'clientes/gera_codigo_lyn',
            dataType: 'json',
            success: function(data){
                $('#ClienteCodigoNinaValidacao').val(data);
                $('#ClienteCodigoNinaValidacao').parent().append('<span style=\"color: #b94a48;margin-left: 10px;\">O codigo deve ser atualizado para os usuarios que irá se vincular</span>');
            }
        });
    });
");
?>

<script>
	$(document).ready(function(){
		formatText($('textarea')[0]);

		if($('#ClienteExameAtravesLyn').is(':checked')) {
			$('.row-servico').show();
		}else{
			$('.row-servico').hide();
			$('#ClienteCodigoExame').val('');
			$('#ClienteCodigoMedico').val('');
		}

		$('#ClienteExameAtravesLyn').change(function(event) {
			if($('#ClienteExameAtravesLyn').is(':checked')) {
				$('.row-servico').show();
			}else{
				$('.row-servico').hide();
				$('#ClienteCodigoExame').val('');
				$('#ClienteCodigoMedico').val('');
			}
		});		 
	});

	function formatText(myArea){
		console.log(myArea);	
		var maxLength = 525;

		var str = myArea.value;
		//console.log(str.length);
		
		//var texto = str.replace(/(\r\n|\n|\n)/gm, "").trim();
		//var texto = str.replace(/\s+/g, "").trim();
		var texto = str.replace(/\r?\n?/g, "");
		
		//console.log(texto.length);

		var textlen = maxLength - texto.length;
		$('#rchars').text(textlen);

		myArea.value = texto;
	} 
</script>