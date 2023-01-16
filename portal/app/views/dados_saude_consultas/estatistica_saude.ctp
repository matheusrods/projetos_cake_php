<div class="text-center">
	<span class="background-greenblue color-white font-size-22 padding-5">Estatísticas do perfil de saúde
		da população</span>
	</div>
	<div class="background-greenblue margin-top-10" style="height:3px"></div>
		<div class="row-fluid margin-top-30">
		<?php 
		$i = 0;
		$view = '';

		if(!empty($questionarios)) {

			foreach ($questionarios as $key => $questionario) { 
				if($i == 4) { echo '</div><div class="row-fluid margin-top-30">'; $i = 0; }
				?>
				<div class="span3">
					<div class="font-size-18 text-center">
						<strong>	
							<?php echo $questionario[0]['descricao']; ?>
						</strong>
					</div>
					<div class="margin-top-10"> 					
						<div class="ponteiro-background" style="background-image: url('/portal/img/todosbem/dashboard/<?php echo $questionario[0]['imagem']?>')"></div>
						
						<div class="blocked color-gray text-center font-size-18 margin-bottom-10">Total: <?php echo $questionario[0]['quantidade_total'] ?></div>
						<?php 
						$label = '';
						$block = '';
						$valores = array();
						$soma = 0;
						if(!empty($questionario['TodosResultados'])) {
							foreach ($questionario['TodosResultados'] as $key2 => $value) {
								$label = strtoupper($value['Resultado']['descricao']);
								$qtd_colaboradores = 0;
								$valor = 0;
								foreach ($questionario['Resultado'] as $key3 => $value3) {
									if($value3['resultado'] == $value['Resultado']['descricao']) {
										$qtd_colaboradores = $value3['quantidade_questionarios'];
										$valor = $value3['quantidade_questionarios'];
									} 
								}
								$block .= '<div class="blocked" data-toggle="tooltip" data-html="true" title="<span class=\'font-size-16\'>Quantidade de colaboradores<br>'.$qtd_colaboradores.'</span>">';
								$block .= '<div class="pull-left font-size-16 '.$classes[$key2][0].' color-white padding-10 text-center" style="width: 15%%"><strong>%'.($key2+1).'$s%%</strong></div>';
								$block .= '<div class="pull-left '.$classes[$key2][1].' color-white padding-10" style="width: 70%%"><strong>'.$label.'</strong></div>';
								$block .= '</div>';
								$valores[] = $valor;
								$soma += $valor;
							}
							foreach ($valores as $key3 => $valor) {
								if($soma > 0) {
									if($key3 == 2){
									 $valores[$key3] = 100 - ($valores[0] + $valores[1]);
									} else {
										$valores[$key3] = ROUND($valor / $soma * 100, 0);
									}
								} else {
									$valores[$key3] = 0;
								}
							}
						}
						echo vsprintf($block, $valores);
						?>
						<div class="blocked text-center color-gray margin-top-10">
						<?php if($soma > 0):?>
							<?php //echo $this->Html->link('relatório', array('controller' => 'dados_saude_consultas', 'action' => 'analitico_resultado', $questionario[0]['codigo_questionario']), array('target' => '_blank')); ?>	
						<?php endif;?>
						</div>
					</div>
				</div>
				<?php $i++; 
			} 
		}//fim questionarios
		else {
		?>
			<div class="span12">
				<div class="font-size-18 text-center">
					<strong>Não existem dados para serem medidos.</strong>
				</div>
			</div>
		<?php
		}//fim else
		?>
		</div>

		<div class="row-fluid margin-top-50">
			<div class="span3">
				<div class="font-size-18 text-center">
					<strong>	
						Dependência nicotina
					</strong>
				</div>
				<div class="margin-top-10"> 
					<div class="blocked color-gray text-center font-size-18">Total: <?= $dependencias['total'] ?></div>
					<div class="blocked margin-top-30">
						<div class="span4 text-center">
							<?php echo $this->Html->image('todosbem/dashboard/cigarro-verm.png', $options = array()); ?>
						</div>	
						<div class="span8">
							<div class="color-red font-size-30"><strong><?=$dependencias['AltaDepen'].'%'
							?></strong></div>
							<div class="color-gray margin-top-10"><strong>tem alta<br>dependência</strong></div>
						</div>		
					</div>
					<div class="blocked margin-top-30">
						<div class="span4 text-center">
							<?php echo $this->Html->image('todosbem/dashboard/cigarro-laranja.png', $options = array()); ?>
						</div>	
						<div class="span8">
							<div class="color-orange font-size-30"><strong><?= $dependencias['MediaDepen'].'%'
							?></strong></div>
							<div class="color-gray margin-top-10"><strong>tem média<br>dependência</strong></div>
						</div>		
					</div>
					<div class="blocked margin-top-30">
						<div class="span4 text-center">
							<?php echo $this->Html->image('todosbem/dashboard/cigarro-amarelo.png', $options = array()); ?>
						</div>	
						<div class="span8">
							<div class="color-gold font-size-30"><strong><?=$dependencias['BaixaDepen'].'%'
							?></strong></div>
							<div class="color-gray margin-top-10"><strong>tem baixa<br>dependência</strong></div>
						</div>		
					</div>
					<div class="blocked margin-top-30">
						<div class="span4 text-center">
							<?php echo $this->Html->image('todosbem/dashboard/cigarro-verde.png', $options = array()); ?>
						</div>	
						<div class="span8">
							<div class="color-green font-size-30"><strong><?=$dependencias['SemDepen'].'%'
							?></strong></div>
							<div class="color-gray margin-top-10"><strong>não tem<br>dependência</strong></div>
						</div>		
					</div>
				</div>
			</div>

			<div class="span3">
				<div class="font-size-18 text-center">
					<strong>	
						IMC
					</strong>
				</div>
				<div class="margin-top-20"> 
					<div class="blocked">
						<div class="span3 text-center">
							<?php echo $this->Html->image('todosbem/dashboard/gg.png', $options = array()); ?>
						</div>
						<div class="span3 text-center">
							<?php echo $this->Html->image('todosbem/dashboard/g.png', $options = array()); ?>
						</div>
						<div class="span3 text-center">
							<?php echo $this->Html->image('todosbem/dashboard/m.png', $options = array()); ?>
						</div>
						<div class="span3 text-center">
							<?php echo $this->Html->image('todosbem/dashboard/p.png', $options = array()); ?>
						</div>
					</div>	
					<div class="blocked color-gray text-center font-size-18 margin-top-20">Total: <?php echo $imc['total']; ?></div>
					<div class="blocked margin-top-10">
						<div class="pull-left font-size-16 background-color-goldenrod color-white padding-10 text-center" style="width: 15%"><strong><?php echo $imc['percentual_qtd_abaixo_do_peso']; ?>%</strong></div>
						<div class="pull-left background-color-gold color-white padding-10" style="width: 70%"><strong>ABAIXO DO PESO</strong></div>
					</div>
					<div class="blocked">
						<div class="pull-left font-size-16 background-greenblue color-white padding-10 text-center" style="width: 15%"><strong><?php echo $imc['percentual_qtd_normal']; ?>%</strong></div>
						<div class="pull-left background-color-skyblue color-white padding-10" style="width: 70%"><strong>NORMAL</strong></div>
					</div>
					<div class="blocked">
						<div class="pull-left font-size-16 background-color-orangered color-white padding-10 text-center" style="width: 15%"><strong><?php echo $imc['percentual_qtd_sobrepeso']; ?>%</strong></div>
						<div class="pull-left background-color-salmon color-white padding-10" style="width: 70%"><strong>SOBREPESO</strong></div>
					</div>
					<div class="blocked">
						<div class="pull-left font-size-16 background-color-red color-white padding-10 text-center" style="width: 15%"><strong><?php echo $imc['percentual_qtd_acima_do_peso']; ?>%</strong></div>
						<div class="pull-left background-color-indianred color-white padding-10" style="width: 70%"><strong>ACIMA DO PESO</strong></div>
					</div>
				</div>
			</div>
		</div>
		<style type="text/css">
			.ponteiro-background{
				background-size: 100%;
				background-repeat: no-repeat;
				background-position: center;
				height: 190px;
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				$('[data-toggle="tooltip"]').tooltip();
			});
		</script>