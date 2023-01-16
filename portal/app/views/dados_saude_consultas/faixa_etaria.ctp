<div class="text-center">
	<span class="background-greenblue color-white font-size-22 padding-5">Faixa etária</span>
</div>
<div class="background-greenblue margin-top-10" style="height:3px"></div>
<div class="padding-top-60 margin-bottom-60">
	<div id="barras">
		<div class="row-fluid">
			<div class="span12">
				<?php if($dados['idade_18_25_percentual'] > 0) { ?>
				<div class="barra-verde text-center" style="width: <?php echo $dados['idade_18_25_percentual']?>%">
					<strong class="font-size-22 color-verde block" style="margin-top: -30px;">
						<?php echo $dados['idade_18_25_percentual']?>%	
					</strong>
					<div class="text-center relative margin-top-72">
						<div class="seta-cima verde"></div>
						<span class="idade pointer font-size-18 color-white padding-5 barra-verde">
							<span>18 - 25 anos</span>
							<span class="hide"><?php echo $dados['idade_18_25']?> funcionários</span>
						</span>
					</div>
				</div>
				<?php } ?>
				<?php if($dados['idade_26_35_percentual'] > 0) { ?>
				<div class="barra-cinza-escuro text-center" style="width: <?php echo $dados['idade_26_35_percentual']?>%">
					<strong class="font-size-25 color-cinza-escuro block" style="margin-top: -30px;">
						<?php echo $dados['idade_26_35_percentual']?>%
					</strong>
					<div class="text-center relative margin-top-72">
						<div class="seta-cima cinza-escuro"></div>
						<span class="idade pointer font-size-18 color-white padding-5 barra-cinza-escuro">
							<span>26 - 35 anos</span>
							<span class="hide"><?php echo $dados['idade_26_35']?> funcionários</span>
						</span>
					</div>
				</div>
				<?php } ?>
				<?php if($dados['idade_36_45_percentual'] > 0) { ?>
				<div class="barra-azul text-center" style="width: <?php echo $dados['idade_36_45_percentual']?>%">
					<strong class="font-size-29 color-azul block" style="margin-top: -30px;">
						<?php echo $dados['idade_36_45_percentual']?>%
					</strong>
					<div class="text-center relative margin-top-72">
						<div class="seta-cima azul"></div>
						<span class="idade pointer font-size-18 color-white padding-5 barra-azul">
							<span>36 - 45 anos</span>
							<span class="hide"><?php echo $dados['idade_36_45']?> funcionários</span>
						</span>
					</div>
				</div>
				<?php } ?>
				<?php if($dados['idade_acima_46_percentual'] > 0) { ?>
				<div class="barra-cinza text-center" style="width: <?php echo $dados['idade_acima_46_percentual']?>%">
					<strong class="font-size-32 color-cinza block" style="margin-top: -30px;">
						<?php echo $dados['idade_acima_46_percentual']?>%
					</strong>
					<div class="text-center relative margin-top-72">
						<div class="seta-cima cinza"></div>
						<span class="idade pointer font-size-18 color-white padding-5 barra-cinza">
							<span>acima de 46</span>
							<span class="hide"><?php echo $dados['idade_acima_46']?> funcionários</span>
						</span>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('.idade').hover(function() {
			var este = $(this);
			var width = este.find('span:first').outerWidth();
			este.stop(true, false).find('span:first').fadeOut('fast', function() {
				este.find('span:last').css('width', width).fadeIn('fast');
			});
		}, function() {
			var este = $(this);
			setTimeout(function() {
				este.stop(true, false).find('span:last').fadeOut('fast', function() {
					este.find('span:first').fadeIn('fast');
				});
			}, 3000);
		});
	});
</script>

<style type="text/css">
	[class^="barra-"] {
		float: left;
		height: 18px;
		white-space: nowrap;
	}
	.barra-verde{
		background: #8eb844;
	}
	.barra-cinza-escuro{
		background: #4a5e72;
	}
	.barra-azul{
		background: #429cbe;
	}
	.barra-cinza{
		background: #92a1ae;
	}
	.color-verde {
		color: #8eb844;
	}
	.color-cinza-escuro {
		color: #4a5e72;
	}
	.color-azul {
		color: #429cbe;
	}	
	.color-cinza {
		color: #92a1ae;
	}
	.seta-cima{
		position: absolute;
		right: 50%;
		top: -20px;
		margin-right: -17px;
	}
	.seta-cima:before {
		content: "";
		display: inline-block;
		border-left: 15px solid transparent;
		border-right: 15px solid transparent;
	}
	.verde.seta-cima:before {
		border-bottom: 15px solid #8eb844;
	}
	.cinza-escuro.seta-cima:before {
		border-bottom: 15px solid #4a5e72;
	}
	.azul.seta-cima:before {
		border-bottom: 15px solid #429cbe;
	}
	.cinza.seta-cima:before {
		border-bottom: 15px solid #92a1ae;
	}
	.hide{
		display: none;
	}
	.block{
		display: block;
	}

</style>