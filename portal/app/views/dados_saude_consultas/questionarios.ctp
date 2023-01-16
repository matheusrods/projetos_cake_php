<div class="text-center">
	<span class="background-greenblue color-white font-size-22 padding-5">Questionários</span>
</div>
<div class="background-greenblue margin-top-10" style="height:3px"></div>
<div class="row-fluid padding-top-30">
	<div class="span12">
		<div class="font-size-22 color-gray">Questionários de saúde preenchidos</div>
		<div id="barra" class="margin-top-80">
			<div class="progresso relative">
				<div class="barra" style="width: <?php echo $percentual ?>%;"></div>
				<div class="campo" style="left: <?php echo $percentual ?>%"><?php echo $percentual ?>%
					<div class="seta-baixo"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row-fluid margin-top-20">
	<div class="span12">
		<span class="pull-right color-gray"><?php echo $this->Html->link('Ver mais', array('controller' => 'dados_saude_consultas', 'action' => 'analitico_situacao_questionarios'), array('target' => '_blank')); ?></span>
	</div>
</div>
<div class="row-fluid margin-top-30">
	<div id="myCarousel" class="carousel slide">
		<!-- Carousel items -->
		<div class="carousel-inner">
			<div class="item active">
				<?php 
				foreach ($questionarios_ativos as $key => $questionarios_ativo) {
					if($key > 0 && $key % 4 == 0) {
						echo '</div><div class="item">';
					}
					?>
					<div class="col3">	
						<div class="font-size-18 text-center">
							<strong>	
								<?php echo $questionarios_ativo[0]['descricao'] ?>
							</strong>
						</div>
						<div class="margin-top-10"> 
							<div class="image-background" style="background-image: url('https://api.rhhealth.com.br<?php echo  $questionarios_ativo[0]['background'] ?>')"></div>
							<div class="blocked">
								<div class="pull-left font-size-15 background-color-green color-white padding-10 text-center" style="width: 15%"><strong><?php echo $questionarios_ativo[0]['percentual_concluido'] ?>%</strong></div>
								<div class="pull-left font-size-15 background-leafgreen color-white padding-10" style="width: 70%"><strong>RESPONDIDO</strong></div>
							</div>
							<div class="blocked">
								<div class="pull-left font-size-15 background-greenblue color-white padding-10 text-center" style="width: 15%"><strong><?php echo $questionarios_ativo[0]['percentual_em_andamento'] ?>%</strong></div>
								<div class="pull-left font-size-15 background-color-skyblue color-white padding-10" style="width: 70%"><strong>INCOMPLETO</strong></div>
							</div>
							<div class="blocked">
								<div class="pull-left font-size-15 background-color-red color-white padding-10 text-center" style="width: 15%"><strong><?php echo $questionarios_ativo[0]['percentual_nao_respondeu'] ?>%</strong></div>
								<div class="pull-left font-size-15 background-color-indianred color-white padding-10" style="width: 70%"><strong>NÃO RESPONDEU</strong></div>
							</div>
							<div class="blocked text-center color-gray margin-top-10">
							<?php if($questionarios_ativo[0]['percentual_nao_respondeu'] != 100): ?>
								<?php echo $this->Html->link('saiba mais +', array('controller' => 'dados_saude_consultas', 'action' => 'analitico_resultado', $questionarios_ativo[0]['codigo']), array('target' => '_blank')); ?>
							<?php endif;?>
							</div>

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
