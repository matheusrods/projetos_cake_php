<span class="background-greenblue color-white font-size-22 padding-5">Gênero</span>
<div class="background-greenblue margin-top-10" style="height:3px"></div>
<div class="padding-top-30 relative text-center">
	<div class="masculino pointer" data-toggle="tooltip" data-html="true" title="<span class='font-size-16'><?php echo $dados['quantidade_masculino'] ?><br>funcionários</span>"><?php echo $dados['percentual_masculino'] ?>%</div>
	<div class="feminino pointer" data-toggle="tooltip" data-html="true" title="<span class='font-size-16'><?php echo $dados['quantidade_feminino'] ?><br>funcionários</span>"><?php echo $dados['percentual_feminino'] ?>%</div>
	<?php echo $this->Html->image('todosbem/dashboard/fem_masc.png') ?>
</div>
<style type="text/css">
	.feminino, .masculino{
		position: absolute;
		left: 50%;
		top: 88px;
		font-size: 22px;
		font-weight: bold;
		color: #fff;
	}
	.feminino{
		margin-left: 56px;
	}
	.masculino{
		margin-left: -99px;
	}
</style>

<script type="text/javascript">
	$(document).ready(function() {
			$('[data-toggle="tooltip"]').tooltip();
	});
</script>