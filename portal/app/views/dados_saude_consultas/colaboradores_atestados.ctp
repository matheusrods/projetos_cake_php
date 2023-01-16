 <?php echo $paginator->options(array('update' => 'div.lista')); ?>
 <div class="">
 	<div class="text-center">
 		<span class="background-greenblue color-white font-size-22 padding-5">Dados dos colaboradores e atestados médicos</span>
 	</div>
 	<div class="background-greenblue margin-top-10" style="height:3px"></div>
 	<div class="margin-top-30">
 		<table class="table">
 			<thead>
 				<tr class=" background-greenblue color-white">
 					<th>Nome</th>
 					<th>Empresa</th>
 					<th>Unidade</th>
 					<th>Setor</th>
 					<th>Cargo</th>
 					<th>Qnt de Atestados</th>
 					<th>Horas de Atestados</th>

 				</tr>
 			</thead>

 			<tbody>
 				<?php foreach ($atestados as $key => $atestado) { ?>
 				<tr class="background-color-whitesmoke ">
 					<td><?php echo $this->Html->link($atestado[0]['nome_funcionario'], array('action' => 'informacao_adicional_colaborador', $atestado[0]['codigo_cliente_funcionario']), array('target' => '_blank'));  ?></td>
 					<td><?php echo $atestado[0]['nome_empresa'] ?></td>
 					<td><?php echo $atestado[0]['nome_unidade'] ?></td>
 					<td><?php echo $atestado[0]['setor'] ?></td>
 					<td><?php echo $atestado[0]['cargo'] ?></td>
 					<td style="color: <?php echo $atestado[0]['color'] ?>; font-size: 16px;"><strong><?php echo $atestado[0]['qnt_atestados'] ?></strong></td>
 					<td ><?php echo $this->Buonny->moeda($atestado[0]['horas_afastamento']) ?> horas</td>
 				</tr>
 				<?php } ?>
 			</tbody>
 		</table>

 		<div class='row-fluid'>
 			<div class='numbers span6'>
 				<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
 				<?php echo $this->Paginator->numbers(); ?>
 				<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
 			</div>
 			<div class='counter span7'>
 				<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>

 			</div>
 		</div>
 		<?php  echo $this->Js->writeBuffer(); ?>
 	</div>
 </div>
 <style type="text/css">
 	.table thead th, .table tbody td{
 		border: 3px solid #fff;
 	}
 	table.table thead th a{
 		color: #fff;
 	}
 </style>