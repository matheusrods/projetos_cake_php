<div class="well">
	<div class="row-fluid">
		<div class="span6 font-size-18"><strong> <?php echo $dados_funcionario[0]['nome'] ?></strong></div>
		<div class="span6"><strong>Empresa:</strong> <?php 	echo $dados_funcionario[0]['empresa_nome_fantasia'] ?></div>
	</div>
	<div class="row-fluid">
		<div class="span6"><strong>Idade:</strong> <?php echo $dados_funcionario[0]['idade'] ?> ano<?php echo (($dados_funcionario[0]['idade'] > 1)? 's' : '')?> (<?php echo $dados_funcionario[0]['data_nascimento'] ?>)</div>
		<div class="span6"><strong>Unidade:</strong> <?php echo $dados_funcionario[0]['unidade_nome_fantasia'] ?></div>
	</div>
	<div class="row-fluid">
		<div class="span6"><strong>Sexo:</strong> <?php echo ($dados_funcionario[0]['sexo'] == 'M')? 'masculino' : 'feminino' ?></div>
		<div class="span6"><strong>Setor:</strong> <?php echo $dados_funcionario[0]['setor'] ?></div>
	</div>
	<div class="row-fluid">
		<div class="span6"><strong>Estado civil:</strong> <?php echo (!empty($dados_funcionario[0]['estado_civil']))? $estados_civis[$dados_funcionario[0]['estado_civil']] : 'Outros' ?></div>
		<div class="span6"><strong>Cargo:</strong> <?php echo $dados_funcionario[0]['cargo'] ?></div>
	</div>
</div>

<div class="text-center margin-top-30">
	<span class="background-greenblue color-white font-size-22 padding-5">Informações adicionais</span>
</div>
<div class="background-greenblue margin-top-10" style="height:3px"></div>
<div class="padding-top-30"></div>

<div class="row-fluid">
	<div class="span3">
		<div class="font-size-18"><strong>Última visita ao médico ocupacional:</strong></div>
		<div class="font-size-18 color-gray margin-top-10"><strong><?php $data = end($visitas_medicos); echo (!empty($data[0]['data_realizacao_exame']) ? $data[0]['data_realizacao_exame']:"Não informado"); ?></strong></div>
	</div>
	<div class="span3 padding-right-30">
		<div class="font-size-18 text-right"><strong>Próxima visita ao médico ocupacional:</strong></div>
		<div class="font-size-18 color-gray margin-top-10 text-right">
		<strong>
		<?php 
		if($exames_ocupacionais){
			$arr = array();
			foreach ($exames_ocupacionais as $v){
				if($v[0]['data_validade']){
					$arr[] = $v[0]['data_validade'];
				}
			}
			if(!empty($arr)){
				echo max($arr);
			}else{
				echo "Não informado";
			}
		}else{
			echo "Não informado";
		}
		?>
		</strong>
		</div>
	</div>
	<div class="span6 background-color-superlightgray radius-5">
		<div class="background-color-orangered color-white font-size-18 radius-5 padding-4">Dados de emergência</div>
		<div class="row-fluid padding-bottom-30">
			<?php //print_r($contato_emergencia);
			if($contato_emergencia){
				$v = $contato_emergencia[0]['UsuarioContatoEmergencia'];
				//foreach($contato_emergencia as $key => $v){ $v = $v['UsuarioContatoEmergencia'];?>
					<div class="span6 margin-top-30">
						<div class="row-fluid">
							<div class="span3">
								<div class="pull-right background-color-orangered padding-7 font-size-20 color-white margin-top-3"><strong>#1</strong></div>
							</div>
							<div class="span9 font-size-16">
								<div><?php echo $v['nome'];?></div>
								<div><?php echo $v['grau_parentesco'];?></div>
								<div><?php echo $v['telefone'].(!empty($v['celular']) ? " / ".$v['celular']:"");?></div>
							</div>
						</div>
					</div>
				<?php //}
			}else{
				echo "<div class='padding-4'>Nenhum</div>";
			} ?>			
		</div>
	</div>
</div>

<div class="row-fluid">

	<!-- VISITAS AO MEDICO -->
	<div class="blocked background-color-mediumorchid color-white font-size-16 margin-top-20 radius-5">
		<div class="pull-left padding-15">	
			<strong>Visitas ao médico do trabalho</strong>
		</div>
		<div class="pull-right font-size-22 radius-5 background-color-darkorchid padding-15"><?php echo (strlen((String)count($visitas_medicos)) > 1 )? count($visitas_medicos) : '0'.(String)count($visitas_medicos) ?></div>
	</div>
	<div class="blocked">
		<div class=" background-color-superlightgray radius-5 padding-15">
			<table class="simple-table">
				<thead>
					<tr>
						<!-- <th>data</th> -->
						<th>clinica</th>
						<!-- <th>consulta</th> -->
						<!-- <th>vencimento</th> -->
						<!-- <th>médico</th> -->
					</tr>
				</thead>
				<tbody>
					<?php foreach ($visitas_medicos as $key => $visitas_medico) { ?>
					<tr>
						<!-- <td><?php echo $visitas_medico[0]['data_realizacao_exame'] ?></td> -->
						<td><?php echo $visitas_medico[0]['nome_fornecedor'] ?></td>
						<!-- <td><?php echo $visitas_medico[0]['tipo_exame'] ?></td> -->
						<!-- <td><?php echo $visitas_medico[0]['data_validade'] ?></td> -->
						<!-- <td><?php echo $visitas_medico[0]['responsavel_tecnico'] ?></td> -->
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<!-- EXAMES OCUPACIONAIS -->
	<div class="blocked background-color-crimson color-white font-size-16 margin-top-20 radius-5">
		<div class="pull-left padding-15">	
			<strong>Exames ocupacionais</strong>
		</div>
		<div class="pull-right font-size-22 radius-5 background-color-brown padding-15"><?php echo (strlen((String)count($exames_ocupacionais)) > 1 )? count($exames_ocupacionais) : '0'.(String)count($exames_ocupacionais) ?></div>
	</div>
	<div class="blocked">
		<div class=" background-color-superlightgray radius-5 padding-15">
			<table class="simple-table">
				<!--<thead>
					<tr>
						<th>tipo</th>
						<th>clinica</th>
						<th>data</th>
						<th>validade</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($exames_ocupacionais as $key => $exame_ocupacional) { ?>
					<tr>
						<td><?php echo $exame_ocupacional[0]['descricao'] ?></td>
						<td><?php echo $exame_ocupacional[0]['nome_fornecedor'] ?></td>
						<td><?php echo $exame_ocupacional[0]['data_realizacao_exame'] ?></td>
						<td><?php echo $exame_ocupacional[0]['data_validade'] ?></td>
					</tr>
					<?php } ?>
				</tbody>-->
			</table>
		</div>
	</div>


	<!-- PLANO DE SAÚDE -->
	<div class="blocked background-color-darkorange color-white font-size-16 margin-top-20 radius-5">
		<div class="pull-left padding-15">	
			<strong>Plano de saúde</strong>
		</div>
		<div class="pull-right font-size-22 radius-5 background-color-darkgoldenrod  padding-15"><?php echo (strlen((String)count($planos_saude)) > 1 )? count($planos_saude) : '0'.(String)count($planos_saude) ?></div>
	</div>
	<div class="blocked">
		<div class=" background-color-superlightgray radius-5 padding-15">
			<table class="simple-table">
				<thead>
					<tr>
						<th>plano</th>
						<th>n° da carteirinha</th>
						<th>validade</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($planos_saude as $key => $plano_saude) { ?>
					<tr>
						<td><?php echo $plano_saude[0]['descricao'] ?></td>
						<td></td>
						<td></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<!-- ATESTADOS -->
	<div class="blocked background-color-seagreen color-white font-size-16 margin-top-20 radius-5">
		<div class="pull-left padding-15">	
			<strong>Atestados</strong>
		</div>
		<div class="pull-right font-size-22 radius-5 background-color-darkslategray  padding-15"><?php echo (strlen((String)count($atestados)) > 1 )? count($atestados) : '0'.(String)count($atestados) ?></div>
	</div>
	<div class="blocked">
		<div class=" background-color-superlightgray radius-5 padding-15">
			<table class="simple-table">
				<!--<thead>
					<tr>
						<th>data</th>
						<th>local</th>
						<th>horas</th>
						<th>médico</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($atestados as $key => $atestado) { ?>
					<tr>
						<td><?php echo $atestado[0]['data_afastamento'] ?></td>
						<td><?php echo $atestado[0]['local'] ?></td>
						<td><?php echo $atestado[0]['afastamento_em_horas'] ?> horas</td>
						<td><?php echo $atestado[0]['nome_medico'] ?></td>
					</tr>
					<?php } ?>
				</tbody>-->
			</table>
		</div>
	</div>
</div>
<div class="row-fluid padding-top-50">
	<div class="span12">
		<div class="font-size-22 color-gray">Questionários de saúde completados</div>
		<div id="barra" class="margin-top-80">
			<div class="progresso relative">
				<div class="barra" style="width: <?php echo $qnt_quest_preenchidos['percentual_preenchido'] ?>%;"></div>
				<div class="campo" style="left: <?php echo $qnt_quest_preenchidos['percentual_preenchido'] ?>%"><?php echo $qnt_quest_preenchidos['percentual_preenchido'] ?>%
					<div class="seta-baixo"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row-fluid margin-top-30" style="min-height: 350px;">
	<div id="myCarousel" class="carousel slide">
		<!-- Carousel items -->
		<div class="carousel-inner">
			<div class="item active">
				<?php 
				foreach ($questionarios as $key => $questionario) { 
					if($key > 0 && $key % 4 == 0) {
						echo '</div><div class="item">';
					}
					?>
					<div class="col3">	
						<div class="font-size-18 text-center">
							<strong>	
								<?php echo $questionario[0]['descricao'] ?>
							</strong>
						</div>
						<div class="margin-top-10"> 
							<?php if($questionario[0]['percentual_respondido'] == 100) { ?>
								<div class="image-background" style="background-image: url('https://api.rhhealth.com.br<?php echo  $questionario[0]['background'] ?>')">
									
								</div>
								<div class="blocked">
									<div class="font-size-15 background-leafgreen color-white padding-10 text-center"><strong>COMPLETO</strong></div>
									<div class="margin-top-10"><?php echo $this->Html->image('todosbem/dashboard/portal-empresa-25.png', array('')); ?>&nbsp;
										<span><strong>ENVIAR FEEDBACK</strong></span>
									</div>
								</div>
							<?php } elseif($questionario[0]['percentual_respondido'] > 0 && $questionario[0]['percentual_respondido'] < 100) { ?>
								<div class="image-background" style="background-image: url('https://api.rhhealth.com.br<?php echo  $questionario[0]['background'] ?>')"></div>
								<div class="blocked">
									<div class="font-size-15 background-color-red color-white padding-10 text-center"><strong>INCOMPLETO</strong></div>
									<div class="progress progress-danger progress-striped margin-top-10" style="height: 20px;margin-bottom: 10px;">
										<div class="bar" style="width: <?php echo $questionario[0]['percentual_respondido'] ?>%;"></div>
									</div>
									<div class="text-center color-red"><strong><?php echo $questionario[0]['percentual_respondido'] ?>%</strong></div>
									<div class="margin-top-10"><?php echo $this->Html->image('todosbem/dashboard/portal-empresa-25.png', array('')); ?>&nbsp;
										<span><strong>LEMBRAR POR E-MAIL</strong></span>
									</div>
								</div>
							<?php } else { ?>
								<div class="image-background-opaca" style="background-image: url('https://api.rhhealth.com.br<?php echo  $questionario[0]['background'] ?>')">
									<div>Não respondido</div>
								</div>
								
								<div class="blocked">
									<div class="margin-top-10"><?php echo $this->Html->image('todosbem/dashboard/portal-empresa-25.png', array('')); ?>&nbsp;
										<span><strong>LEMBRAR POR E-MAIL</strong></span>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
			<!-- Carousel nav -->
			<a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
			<a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.carousel').carousel({
				interval: false
			});
		});
	</script>
	<style type="text/css">
		table.simple-table{
			width: 100%;
		}
		table.simple-table thead tr{
			background: #f5f5f5;
			border: inherit;
			border-radius: inherit;
			box-shadow: inherit;
		}
		table.simple-table thead tr th{
			text-align: left;
		}
		table.simple-table td, table.simple-table th {
			padding: 5px;
			padding-left: 20px;
		}
		table.simple-table tbody tr{
			border-bottom: 1px solid #ccc;
		}
		table.simple-table tbody tr:last-child{
			border-bottom: inherit;
		}
		.progress{
			height: 40px;
			border-radius: 15px;
		}
		.campo {
			position: absolute;
			left: 100%;
		}
		.progresso{
			width: 100%;
			float: left;
			height: 41px;
			background: #ccc;
			border-radius: 20px;
		}
		.progresso > .barra{
			width: 60%;
			background-color: green;
			height: 40px;
			border-radius: 20px;
		}
		.progresso > .campo{
			position: absolute;
			left: 60%;
			top: -55px;
			margin-left: -40px;
			left: 95%;
			background: #dbdce2;
			font-size: 22px;
			font-weight: 800;
			color: #333;
			padding: 8px;
			border-radius: 12px;
			box-shadow: 1px 1px 1px;
		}
		.progresso .seta-baixo{
			position: absolute;
			right: 50%;
			top: 31px;
			margin-right: -17px;

		}
		.progresso .seta-baixo:before {
			content: "";
			display: inline-block;
			border-left: 15px solid transparent;
			border-right: 15px solid transparent;
			border-top: 15px solid #dbdce2;
		}
		.col3{
			float: left;;
			width: 23%;
			padding-left: 15px;
			padding-right: 15px;
		}
		.col3:first-child {
			padding-left: 0;
		}
		.col3:last-child {
			padding-right: 0;
		}
		.image-background{
			border-top-left-radius: 25px;
			border-top-right-radius: 25px;
			background-size: cover;
			height: 190px;
			border-top: 1px solid #ccc;
			border-left: 1px solid #ccc;
			border-right: 1px solid #ccc;
		}
		.image-background-opaca{
			height: 190px;
			border-radius: 25px;
		}
		.image-background-opaca>div{
			padding-top: 90px;
			background: rgba(255,255,255,0.7);
			text-align: center;
			height: 190px;
			font-size: 16px;
		}
		.carousel-control {
			font-size: 90px;
			color: #908d8d !important;
			background: inherit;
		}
		.carousel-control:hover{
			color: #908d8d;
		}
		.carousel-control.left{
			left: -45px;
		}
		.carousel-control.right{
			right: -45px;
		}
	</style>