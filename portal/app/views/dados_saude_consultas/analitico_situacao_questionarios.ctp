<div class="margin-top-30">
	
	<ul class="nav nav-tabs" id="myTab">
		<li class="active"><a href="#respondido">respondido</a></li>
		<li><a href="#incompletos">incompletos</a></li>
		<li><a href="#nao_respondidos">não respondidos</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="respondido">
			<table class="table">
				<thead>
					<tr class=" background-color-yellowgreen color-white">
						<th>Nome</th>
						<th>Empresa</th>
						<th>Unidade</th>
						<th>Setor</th>
						<th>Cargo</th>
						<th>Questionário</th>
						<!-- <th style="width: 80px;"></th> -->
					</tr>
				</thead>

				<tbody>
					<?php foreach ($respondidos as $key => $respondido) { ?>
					<tr class="background-color-whitesmoke ">
						<td><?php echo $this->Html->link($respondido[0]['nome_funcionario'], array('action' => 'informacao_adicional_colaborador', $respondido[0]['codigo_cliente_funcionario']), array('target' => '_blank')) ?></td>
						<td><?php echo $respondido[0]['cliente_razao_social'] ?></td>
						<td><?php echo $respondido[0]['unidade_razao_social'] ?></td>
						<td><?php echo $respondido[0]['setor'] ?></td>
						<td><?php echo $respondido[0]['cargo'] ?></td>
						<td><?php echo $respondido[0]['descricao_questionario'] ?></td>
						<!--<td>
							<?php echo $this->Html->image('todosbem/dashboard/portal-empresa-24.png', array('style' => 'width: 24px', 'class' => 'margin-right-20')); ?>
							<?php echo $this->Html->image('todosbem/dashboard/portal-empresa-25.png', array('')); ?>
						</td>-->
					</tr>
					<?php } ?>
				</tbody>
			</table>			

		</div>
		<div class="tab-pane" id="incompletos">
			<table class="table">
				<thead>
					<tr style="background: #429cbe" class="color-white">
						<th>Nome</th>
						<th>Empresa</th>
						<th>Unidade</th>
						<th>Setor</th>
						<th>Cargo</th>
						<th style="width: 80px;"></th>
					</tr>
				</thead>

				<tbody>
					<?php foreach ($incompletos as $key => $incompleto) { ?>
					<tr class="background-color-whitesmoke ">
						<td><?php echo $this->Html->link($incompleto[0]['nome_funcionario'], array('action' => 'informacao_adicional_colaborador', $incompleto[0]['codigo_cliente_funcionario']), array('target' => '_blank')) ?></td>
						<td><?php echo $incompleto[0]['cliente_razao_social'] ?></td>
						<td><?php echo $incompleto[0]['unidade_razao_social'] ?></td>
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
		<div class="tab-pane" id="nao_respondidos">
			<table class="table">
				<thead>
					<tr class=" background-color-firebrick color-white">
						<th>nome</th>
						<th>empresa</th>
						<th>unidade</th>
						<th>setor</th>
						<th>cargo</th>
						<th>Questionário</th>
						<!-- <th style="width: 40px;"></th> -->
					</tr>
				</thead>

				<tbody>
					<?php foreach ($nao_respondidos as $key => $nao_respondido) { ?>
					<tr class="background-color-whitesmoke ">
						<td><?php echo $this->Html->link($nao_respondido[0]['nome_funcionario'], array('action' => 'informacao_adicional_colaborador', $nao_respondido[0]['codigo_cliente_funcionario']), array('target' => '_blank')) ?></td>
						<td><?php echo $nao_respondido[0]['cliente_razao_social'] ?></td>
						<td><?php echo $nao_respondido[0]['unidade_razao_social'] ?></td>
						<td><?php echo $nao_respondido[0]['setor'] ?></td>
						<td><?php echo $nao_respondido[0]['cargo'] ?></td>
						<td><?php echo $nao_respondido[0]['descricao_questionario'] ?></td>
						<!-- <td style="text-align: center !important">
							<?php echo $this->Html->image('todosbem/dashboard/portal-empresa-25.png', array('')); ?>
						</td> -->
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
		background-color: yellowgreen;
	}
	.nav-tabs>li:nth-child(2)>a, .nav-tabs>li:nth-child(2)>a:hover, .nav-tabs>li:nth-child(2)>a:focus{
		background-color: #429cbe;
	}
	.nav-tabs>li:last-child>a, .nav-tabs>li:last-child>a:hover, .nav-tabs>li:last-child>a:focus{
		background-color: firebrick  ;
	}
	.table thead th, .table tbody td{
		border: 3px solid #fff;
	}
</style>