<?php	
	if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
		$session->delete('Message.flash');
		echo $javascript->codeBlock("window.location = baseUrl+'Veiculos/adicionar_veiculo';");
		exit;
	}
?>
<?php echo $this->BForm->error_menssage($menssagem) ?>

<?php echo $this->Bajax->form('TVeicVeiculo', array('url' => array('controller' => 'Veiculos','action' => 'formulario_veiculo',$cliente['Cliente']['codigo'],$placa),'divupdate' => 'div#form-filho' ));?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->hidden('alterar',array('value' => '1')) ?>
		<?php echo $this->BForm->hidden('veic_placa') ?>
		<?php echo $this->BForm->hidden('esta_codigo') ?>
		<?php echo $this->BForm->hidden('mvei_codigo') ?>
		<?php echo $this->BForm->hidden('codigo_cliente') ?>
		<?php echo $this->BForm->hidden('term_vtec_codigo') ?>
		<?php echo $this->BForm->hidden('veic_mvec_codigo') ?>
		<?php echo $this->BForm->hidden('veic_tvei_codigo') ?>
		<?php echo $this->BForm->hidden('veic_cida_codigo_emplacamento') ?>
		

		<?php echo $this->BForm->input('tvei_descricao', array('label' => 'Tipo de Veiculo','class' => 'input-medium', 'readonly' => true)) ?>
		<?php echo $this->BForm->input('mvei_descricao', array('label' => 'Fabricante','class' => 'input-large fabricante', 'readonly' => true)) ?>
		<?php echo $this->BForm->input('mvec_descricao', array('label' => 'Modelo','class' => 'input-large modelo', 'readonly' => true)) ?>
		<?php echo $this->BForm->input('veic_cor', array('label' => 'Cor','class' => 'input-medium' ,'empty' => 'Selecione uma cor', 'options' => $veiculos_cor)) ?>
	</div>
	<div class='row-fluid inline'> 
		<?php echo $this->BForm->input('esta_descricao', array('label' => 'Estado','class' => 'uf input-small', 'readonly' => true)) ?>
		<?php echo $this->BForm->input('cida_descricao', array('label' => 'Cidade Emplacamento','class' => 'cidade input-medium', 'readonly' => true)) ?>
		<?php echo $this->BForm->input('veic_chassi', array('label' => 'Chassi', 'type' => 'text', 'class' => 'input-large','readonly' => true)) ?>
		<?php echo $this->BForm->input('veic_renavam', array('label' => 'Renavam', 'type' => 'text', 'class' => 'input-medium','readonly' => true)) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('veic_ano_fabricacao', array('label' => 'Ano Fabricacao', 'empty' => 'Ano Fabricacao','class' => 'input-medium', 'readonly' => true)) ?>
		<?php echo $this->BForm->input('veic_ano_modelo', array('label' => 'Ano Modelo', 'empty' => 'Ano Modelo','class' => 'input-medium', 'readonly' => true)) ?>
		<?php echo $this->BForm->input('tecn_codigo', array('label' => 'Tecnologia', 'empty' => 'Selecione uma Tecnologia','class' => 'tecnologia input-large', 'options' => $veiculos_tecnologia)) ?>
		<?php echo $this->BForm->input('term_numero_terminal', array('label' => '* Numero de Série', 'class' => 'input-medium','type' => 'text', 'maxlength' => 29)) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('veic_telefone', array('label' => 'Telefone', 'class' => 'input-medium telefone','type' => 'text')) ?>
		<?php echo $this->BForm->input('veic_radio', array('label' => 'Radio', 'class' => 'input-medium','type' => 'text', 'maxlength' => 15)) ?>
		<?php echo $this->BForm->input('veic_status', array('label' => 'Status', 'empty' => false, 'options' => $veiculos_status)) ?>
	</div>
	<div class='row-fluid inline'>
	
	<table data-index = "0" >
		<tr>
			<td>
				<?php echo $this->BForm->input('tip_cliente', array('label' => 'Tipo de Veículo do Cliente', 'class' => 'input-medium', 'type' => 'text')) ?>
			</td>
			<td>
				<?php echo $this->Buonny->input_referencia($this, '#TVeicVeiculoCodigoCliente', 'TVeicVeiculo', 'refe_codigo', false, 'Alvo Origem', true) ?>
			</td>
		</tr>
	</table>
		
	</div>
	<div class='row-fluid inline padrao'>
		<?php echo $this->BForm->input('codigo_transportador', array('label' => 'Transportador Padrão','class' => 'input-xlarge', 'empty' => 'Selecione um Transportador', 'options' => $transportadoras)) ?>
		<?php echo $this->BForm->input('motorista', array('label' => 'Motorista Padrão','class' => 'input-medium formata-cpf','type' => 'text')) ?>
		<?php echo $this->BForm->hidden('codigo_motorista') ?>
		<?php echo $this->BForm->input('nome_motorista', array('label' => 'Nome Motorista','class' => 'input-large','type' => 'text','readonly' => true)) ?>
	</div>
	<?php echo $this->BForm->error_menssage('ATENÇÃO! * Campo obrigatório com risco de comprometimento do rastreamento do veículo, em caso de informação incorreta.', array('class' => 'help-block alert-error form-actions well veiculo-error')) ?>
	<br />
	<h4>Atuadores</h4>
	
	<div class='atuadores'>
		<div>
			<?= $this->Html->link('Desmarcar todas', '#', array('id' => 'marca_n')) ?> | 
			<?= $this->Html->link('Marcar todas', '#', array('id' => 'marca_y')) ?>
		</div>
		<?php foreach ($veiculos_atuadores as $key => $valor): ?>
		<?php $flag = false ?>
			<label class="checkbox inline input-large" for="atuadores">
				<?php if ($atuadores): ?>
					<?php foreach ($atuadores as $veiculo_key => $veiculo_valor): ?>
						<?php 
							if(trim($key) == trim($veiculo_valor)){
								$flag = true; 
								break;
							}
						?>
					<?php endforeach; ?>
				<?php endif ?>
				<?php if($flag): ?>
				<input id="<?php echo 'Atuadores'.$key ?>" type="checkbox" value="<?php echo $key ?> " name="data[TVeicVeiculo][atuadores][]" checked="checked" >
				<?php else: ?>
				<input id="<?php echo 'Atuadores'.$key ?>" type="checkbox" value="<?php echo $key ?> " name="data[TVeicVeiculo][atuadores][]" >
				<?php endif; ?>

			<?php echo $valor ?>
		</label>
		
		<?php endforeach; ?>
	</div>

<div class="form-actions">
	  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	  <?php echo $html->link('Voltar', 'adicionar_veiculo', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>

<?php echo $this->BForm->end(); ?>
<?php  echo $this->Javascript->codeBlock('
	$(function(){

		setup_mascaras();

		busca_tipos();
		busca_profissional("#TVeicVeiculoMotorista","#TVeicVeiculoNomeMotorista","#TVeicVeiculoCodigoMotorista");
		
		$("a#marca_y").click(function(){
			select_all(true,".checkbox input");
			return false;
		});

		$("a#marca_n").click(function(){
			select_all(false,".checkbox input");
			return false;
		});
		
		function select_all(yn,element_itens){
			$(element_itens).prop("checked",yn);
		}

		function busca_tipos(){
			var cam_carr 	= $("#TVeicVeiculoVeicTveiCodigo");
			var tecnologia 	= $("#TVeicVeiculoTecnCodigo");
			var serie 		= $("#TVeicVeiculoTermNumeroTerminal");
			var atuadores	= $("div.atuadores");
			var erro		= $("div.veiculo-error");
			var padrao		= $("div.padrao");

			if(cam_carr.val() == 1){
				tecnologia.children(":eq(0)").attr("selected","selected");
				tecnologia.children("option+option").css({"display":"none"});
				tecnologia.attr("readonly",true);
				serie.val("");
				serie.attr("readonly",true);
				atuadores.find("input").prop("checked",false);
				atuadores.css({"display":"none"});
				erro.css({"display":"none"});
				padrao.css({"display":"none"});
				
			} else {
				tecnologia.children("option+option").css({"display":"block"});
				tecnologia.attr("readonly",false);
				serie.attr("readonly",false);
				atuadores.css({"display":"block"});
				erro.css({"display":"block"});
				padrao.css({"display":"block"});
			}
		}

		
	});', false);
	
?>