<div class="row-fluid margin-top-30">
	<?php if(!empty($resultado)) { ?>		
	<div class="risco <?php echo str_replace(' ', '_', strtolower($resultado['resultado'])) ?> blocked">
		<strong><?php echo $resultado['percentual'] ?>%</strong> <?php echo $resultado['resultado'] ?>
	</div>
	<?php } ?>
	<div class="text-center font-size-16 blocked margin-top-20">
		Como chegamos a este resultado?
	</div>
</div>

<?php foreach ($caracteristicas_populacao as $key => $value) { ?>
<div class="row-fluid margin-top-50">
	<div class="span12">
		<div id="barra">
			<div class="progresso_<?php echo $value[0]['risco'] ?> relative">
				<div class="barra" style="width: <?php echo $value[0]['percentual'] ?>%"></div>
			</div>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="span3">
		<div class="font-size-22 color-firebrick margin-top-10">
			<strong>
				<?php echo $value[0]['percentual'] ?>%	
			</strong>
		</div>
		<div class="margin-top-5">
			dessa população,
		</div>
		<div class="font-size-16 margin-top-5">
			<?php echo $value[0]['alerta'] ?>
		</div>
	</div>
	<div class="span9">
		<div class="well">
			<?php echo $value[0]['descricao'] ?>
		</div>
	</div>
</div>
<?php } ?>

<style type="text/css">
	.risco{
		color: #fff;
		text-align: center;
		font-size: 18px;
		padding: 20px 0;
	}
	.risco_elevado{
		background: firebrick;
	}
	.risco_moderado{
		background: gold ;
	}
	.baixo_risco{
		background: green;
	}
	[class^="progresso_"]{
		width: 100%;
		float: left;
		height: 16px;
		background: #ccc;
		border-radius: 20px;
	}
	.progresso_baixo_risco > .barra{
		background-color: green;
		height: 15px;
		border-radius: 20px;
	}
	.progresso_medio_risco > .barra{
		background-color: gold;
		height: 15px;
		border-radius: 20px;
	}
	.progresso_alto_risco > .barra{
		background-color: firebrick;
		height: 15px;
		border-radius: 20px;
	}
</style>
