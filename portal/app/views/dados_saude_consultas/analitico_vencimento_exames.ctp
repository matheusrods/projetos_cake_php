<div class="margin-top-30">
	
	<ul class="nav nav-tabs" id="myTab">
		<li class="active"><a href="#vencidos">vencidos</a></li>
		<li><a href="#30_dias">30 dias</a></li>
		<li><a href="#60_dias">60 dias</a></li>
		<li><a href="#90_dias">90 dias</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="vencidos">
			<table class="table">
				<thead>
					<tr class=" background-color-firebrick color-white">
						<th>nome</th>
						<th>empresa</th>
						<th>unidade</th>
						<th>setor</th>
						<th>cargo</th>
						<th style="width: 80px;"></th>
					</tr>
				</thead>

				<tbody>
					<?php foreach ($exames['Vencidos'] as $key => $exame) { ?>
					<tr class="background-color-whitesmoke ">
						<td><?php echo $exame[0]['nome_funcionario'] ?></td>
						<td><?php echo $exame[0]['razao_social_cliente'] ?></td>
						<td><?php echo $exame[0]['razao_social_unidade'] ?></td>
						<td>T.I.</td>
						<td>Analista de sistemas</td>
						<td>
							<?php echo $this->Html->image('todosbem/dashboard/portal-empresa-24.png', array('style' => 'width: 24px', 'class' => 'margin-right-20')); ?>
							<?php echo $this->Html->image('todosbem/dashboard/portal-empresa-25.png', array('')); ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>			

		</div>
		<div class="tab-pane" id="30_dias">
			<table class="table">
				<thead>
					<tr class=" background-color-darkorange color-white">
						<th>nome</th>
						<th>empresa</th>
						<th>unidade</th>
						<th>setor</th>
						<th>cargo</th>
						<th style="width: 80px;"></th>
					</tr>
				</thead>

				<tbody>
					<?php foreach ($exames['Vence_em_30_dias'] as $key => $exame) { ?>
					<tr class="background-color-whitesmoke ">
						<td><?php echo $exame[0]['nome_funcionario'] ?></td>
						<td><?php echo $exame[0]['razao_social_cliente'] ?></td>
						<td><?php echo $exame[0]['razao_social_unidade'] ?></td>
						<td>T.I.</td>
						<td>Analista de sistemas</td>
						<td>
							<?php echo $this->Html->image('todosbem/dashboard/portal-empresa-24.png', array('style' => 'width: 24px', 'class' => 'margin-right-20')); ?>
							<?php echo $this->Html->image('todosbem/dashboard/portal-empresa-25.png', array('')); ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>

		</div>
		<div class="tab-pane" id="60_dias">
			<table class="table">
				<thead>
					<tr class=" background-color-goldenrod color-white">
						<th>nome</th>
						<th>empresa</th>
						<th>unidade</th>
						<th>setor</th>
						<th>cargo</th>
						<th style="width: 80px;"></th>
					</tr>
				</thead>

				<tbody>
					<?php foreach ($exames['Vence_em_60_dias'] as $key => $exame) { ?>
					<tr class="background-color-whitesmoke ">
						<td><?php echo $exame[0]['nome_funcionario'] ?></td>
						<td><?php echo $exame[0]['razao_social_cliente'] ?></td>
						<td><?php echo $exame[0]['razao_social_unidade'] ?></td>
						<td>T.I.</td>
						<td>Analista de sistemas</td>
						<td>
							<?php echo $this->Html->image('todosbem/dashboard/portal-empresa-24.png', array('style' => 'width: 24px', 'class' => 'margin-right-20')); ?>
							<?php echo $this->Html->image('todosbem/dashboard/portal-empresa-25.png', array('')); ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>

		</div>
		<div class="tab-pane" id="90_dias">
			<table class="table">
				<thead>
					<tr class=" background-color-yellowgreen color-white">
						<th>nome</th>
						<th>empresa</th>
						<th>unidade</th>
						<th>setor</th>
						<th>cargo</th>
						<th style="width: 80px;"></th>
					</tr>
				</thead>

				<tbody>
					<?php foreach ($exames['Vence_em_90_dias'] as $key => $exame) { ?>
					<tr class="background-color-whitesmoke ">
						<td><?php echo $exame[0]['nome_funcionario'] ?></td>
						<td><?php echo $exame[0]['razao_social_cliente'] ?></td>
						<td><?php echo $exame[0]['razao_social_unidade'] ?></td>
						<td>T.I.</td>
						<td>Analista de sistemas</td>
						<td>
							<?php echo $this->Html->image('todosbem/dashboard/portal-empresa-24.png', array('style' => 'width: 24px', 'class' => 'margin-right-20')); ?>
							<?php echo $this->Html->image('todosbem/dashboard/portal-empresa-25.png', array('')); ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>

		</div>
	</div>

</div>
<script>
	$('#myTab a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	})
</script>


<style type="text/css">
	.nav-tabs>li>a, .nav-tabs>li>a:hover, .nav-tabs>li>a:focus{
		font-size: 18px;
		font-weight: 600;
		color: #fff !important;
		padding: 15px 70px;
		border-radius: 8px 8px 0 0;
	}
	.nav-tabs>li:first-child>a, .nav-tabs>li:first-child>a:hover, .nav-tabs>li:first-child>a:focus{
		background-color: firebrick ;
	}
	.nav-tabs>li:nth-child(2)>a, .nav-tabs>li:nth-child(2)>a:hover, .nav-tabs>li:nth-child(2)>a:focus{
		background-color: darkorange;
	}
	.nav-tabs>li:nth-child(3)>a, .nav-tabs>li:nth-child(3)>a:hover, .nav-tabs>li:nth-child(3)>a:focus{
		background-color: goldenrod;
	}
	.nav-tabs>li:last-child>a, .nav-tabs>li:last-child>a:hover, .nav-tabs>li:last-child>a:focus{
		background-color: yellowgreen ;
	}
	.table thead th, .table tbody td{
		border: 3px solid #fff;
	}
</style>