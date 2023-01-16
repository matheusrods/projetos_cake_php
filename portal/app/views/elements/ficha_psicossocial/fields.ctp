<style type="text/css">
	.adjust-parentesco{
		border: 1px solid #a00b0b;
	}
	.bad{
		color: #D06363 !important;
	}
	.good{
		color: #8bb863 !important;
	}

</style>
<div class='well'>
	<div class="bordered">
		<div class='row-fluid'>	
			<h5 class="text-center">DADOS PRINCIPAIS</h5>
			<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>

			<?php echo $this->BForm->hidden('codigo'); ?>

			<div class="span2 no-margin-left checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('codigo_pedido_exame', array('value' => $dados['PedidoExame']['codigo'], 'label' => 'Cód. ped. exame:',  'style' => 'width: 86%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span4 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('empresa', array('value' => $dados['Empresa']['razao_social'], 'label' => 'Empresa:', 'style' => 'width: 95%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span3 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('unidade', array('value' => $dados['Unidade']['razao_social'], 'label' => 'Unidade:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span3 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('setor', array('value' => $dados['PedidoExame']['setor'], 'label' => 'Setor:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span3 no-margin-left checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('funcionario', array('value' => $dados['Funcionario']['nome'],'label' => 'Funcionário:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span3 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('cpf', array('value' => $dados['Funcionario']['cpf'],'label' => 'CPF:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span2 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('idade', array('value' => $dados[0]['idade'], 'label' => 'Idade:', 'style' => 'width: 86%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span2 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('data_admissao', array('value' => $dados['ClienteFuncionario']['admissao'], 'label' => 'Data de admissão:', 'style' => 'width: 86%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span2 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('sexo', array('value' => $dados[0]['sexo'], 'label' => 'Sexo:', 'style' => 'width: 86%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span3 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('cargo', array('value' => $dados['PedidoExame']['cargo'],'label' => 'Cargo:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>			
		</div>
		<div class="row-fluid">
			<div class="clear"></div>
			<hr>
			<div class="span4 no-margin-left checkbox-canvas padding-left-10">
				<?php
					if($this->data['ItemPedidoExame']['respondido_lyn']){
						echo $this->BForm->hidden('codigo_medico', array('value' => $this->data['Medico']['codigo']));
                        echo $this->BForm->input('medico', array('value' => $this->data['Medico']['nome'],'label' => 'Médico:', 'style' => 'width: 95%; margin-bottom: 0', 'readonly' => true));
					}else{
						echo $this->BForm->input('codigo_medico', array('label' => 'Médico:', 'options' => $dados['Medico'], 'empty' => ((!is_null($this->data))? null : 'Selecione'), 'style' => 'width: 95%; margin-bottom: 0', 'required' => 'required'));
					}
				?>
			</div>
		</div>
		<hr>		
		<div class='row-fluid'>
			<h5 class="text-center">TESTE SRQ 20 - Self Report Questionare</h5>	
			<div class="inputs-config span12 no-margin-left">
				<div class="checkbox-canvas if-booleano">
					<p>Teste que avalia a saúde mental. Por favor, leia estas instruções antes de preencher as questões abaixo.<br>
						É muito importante que todos que estao preenchendo o questionário sigam as mesmas instruções.
					</p>
				</div>
			</div>
		</div>

			<hr>

		<div class='row-fluid'>
			<h5 class="text-center">Instruções</h5>	
			<div class="inputs-config span12 no-margin-left">
				<div class="checkbox-canvas if-booleano">
					<p>Estas questões são relacionadas a certas dores e problemas que podem ter lhe incomodado nos ultimos 30 dias.
						<br>Se você acha que a questão se aplica a você e você teve o problema descrito nos ultimos 30 dias responda SIM. Por outro lado, se a questão não se aplica a você e você não teve o problema nos ultimos 30 dias, responda NÃO.
						<br>OBS. Lembre-se que o diagnostico definitivo só pode ser fornecido por um profissional.
					</p>
				</div>
			</div>
		</div>
		<hr>
		<div class="row-fluid">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Perguntas</th>
						<th>Sim</th>
						<th>Não</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($perguntas as $pergunta): ?>

						<?php
						//variavel auxiliar
						$check_sim = '';
						$check_nao = '';
						//verifica se existe o indice do array
						if(isset($this->data['FichaPsicossocialResposta'][$pergunta['FichaPsicossocialPergunta']['codigo']])) {
							//verifica qual o valor do indice do array caso 1 = sim caso 0 = nao
							if($this->data['FichaPsicossocialResposta'][$pergunta['FichaPsicossocialPergunta']['codigo']] == 1) {
								//seta o valor na variavel auxiliar
								$check_sim = '1';
							}
							else {
								//seta o valor na variavel auxiliar
								$check_nao = '1';
							}//fim if valor
						}//fim if se existe indice
						?>

						<tr>
							<td><strong><?php echo $pergunta['FichaPsicossocialPergunta']['ordem'] .' - '. $pergunta['FichaPsicossocialPergunta']['pergunta'] ?></strong></td>
							<td class="text-center">
								<input type="radio" name="data[FichaPsicossocialPergunta][<?php echo $pergunta['FichaPsicossocialPergunta']['codigo']; ?>]" id="FichaPsicossocialPergunta<?php echo $pergunta['FichaPsicossocialPergunta']['codigo']; ?>1" class="total_sim" value="1" <?php echo ($check_sim == 1) ? 'checked="checked"' : ''; ?> onclick="contador()" >
							</td>
							<td class="text-center">
								<input type="radio" name="data[FichaPsicossocialPergunta][<?php echo $pergunta['FichaPsicossocialPergunta']['codigo']; ?>]" id="FichaPsicossocialPergunta<?php echo $pergunta['FichaPsicossocialPergunta']['codigo']; ?>0" class="total_nao" value="0" <?php echo ($check_nao == 1) ? 'checked="checked"' : ''; ?> onclick="contador()" >
							</td>
						</tr>
					<?php endforeach ?>
					<tr>
						<td class="text-center"><strong>Total</strong></td>
						<td class="text-center">
							<input type="text" name="data[FichaPsicossocial][total_sim]" style="width: 13px;" id="contador_sim" value="0" readonly="readonly">
						</td>
						<td class="text-center">
							<input type="text" name="data[FichaPsicossocial][total_nao]" style="width: 13px;" id="contador_nao" value="0" readonly="readonly">
						</td>
						<hr>
					</tr>
					<tr>
						<td class="text-center"><strong>Status</strong></td>
						<td class="text-center">
							<input type="text" name="data[FichaPsicossocial][msg]" class="center label_status good" style="width: 159px; float:left; margin-right: -176px;" id="msg" value="Improvavel" readonly="readonly">
						</td>	
						<td></td>
					</tr>

					
						
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="form-actions">
	<div>
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		<?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {

		//para popupar o nao como resposta padrao no incluir
		if($("#FichaPsicossocialCodigo").val() == ""){			
			$(".total_nao").each(function(indice){
				var id = $(this).prop('id');				
				$('#'+id).prop('checked', true);
			});
		}//fim pop nao


		contador = function() {
			var contSim = 0;
			var contNao = 0;
			$(".total_sim").each(function(indice){
				var idSim = $(this).prop('id');

				if($('#'+idSim).prop('checked')) {
					// console.log('aqui nao ' + id);
					contSim++;
					$('#contador_sim').val(contSim);

					var label = '';
        			var clas = 'center label_status bad';

					var totalsim = $('#contador_sim').val();

					if((totalsim >= 0)  && (totalsim <= 7) ){
                		label = "Improvavel";
                		clas = 'center label_status good';
            		} else if((totalsim >= 8)  && (totalsim <= 11)){
            			label = "Possível (questionável)";
                		clas = 'center label_status good';
            		} else if((totalsim >= 12)  && (totalsim <= 21)){
            			label = "Provável";
                		clas = 'center label_status bad';
            		}
            		//seta os valores para os campos
            		$('#msg').val(label);
            		$("#msg").removeClass();
            		$("#msg").addClass(clas);
				}
			});	

			$(".total_nao").each(function(indice){
				var idNao = $(this).prop('id');

				if($('#'+idNao).prop('checked')) {
					// console.log('aqui nao ' + id);
					contNao++;
					$('#contador_nao').val(contNao);
				}
			});
		}
		
		contador();
	});
</script>