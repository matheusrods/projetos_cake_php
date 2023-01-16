
<?php if(!empty($dados)):?>

	<?php 
	    echo $paginator->options(array('update' => 'div.lista')); 
	    $total_paginas = $this->Paginator->numbers();
	?>

	<table class="table table-striped">

		<thead>
			<tr>
				<th>Codigo</th>
				<th>Cliente</th>
				<th>Arquivo</th>
				<th>Sistema</th>
				<th>Usuário Inclusão</th>
				<th>Data Inclusão</th>
				<th>Descrição</th>
				<th>Ações</th>
			</tr>
		</thead>

		<tbody>

			<?php foreach ($dados as $dado): ?>
				<tr>
					<td><?= $dado['LogIntegracao']['codigo']?></td>
					<td><?= $dado['Cliente']['nome_fantasia']?></td>
					<td><?= $dado['LogIntegracao']['arquivo']?></td>
					<td><?= $dado['LogIntegracao']['sistema_origem']?></td>
					<td><?= $dado['UsuarioInclusao']['nome']?></td>
					<td><?= $dado['LogIntegracao']['data_inclusao']?></td>
					<td><?= $this->Buonny->leiamais($dado['LogIntegracao']['descricao']) ?></td>
					<td>	            		
						<a href="javascript:void(0);" onclick="modal_exibicao_integracao('<?php echo $dado['LogIntegracao']['codigo']; ?>','conteudo', 1);"><i class="icon-eye-open" title="Visualizar Conteúdo"></i></a>
						<a href="javascript:void(0);" onclick="modal_exibicao_integracao('<?php echo $dado['LogIntegracao']['codigo']; ?>','retorno', 1);"><i class="icon-eye-open" title="Visualizar Retorno"></i></a>
					</td>
				</tr>
			<?php endforeach; ?>

		</tbody>

	</table>

	<div class="modal fade" id="modal_info_integracao" data-backdrop="static"></div>

	<div class='row-fluid'>
		<div class='numbers span6'>
			<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
		  <?php echo $this->Paginator->numbers(); ?>
			<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
		</div>
		<div class='counter span6'>
			<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
		</div>
	</div>
	
	<?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		setup_mascaras(); setup_time(); setup_datepicker();
		$(".modal").css("z-index", "-1");
		$(".modal").css("width", "90%");
		$(".modal").css("left", "26%");
		$(".modal").css("top", "13%");
	});

	function modal_exibicao_integracao(codigo_integracao,campo,mostra) {
		if(mostra) {
			
			var div = jQuery("div#modal_info_integracao");
			bloquearDiv(div);
			div.load(baseUrl + "logs_integracoes/visualiza_informacoes_integracao/" + codigo_integracao + "/" + campo + "/" + Math.random());

			$("#modal_info_integracao").css("z-index", "1050");
			$("#modal_info_integracao").modal("show");

		} else {
			$(".modal").css("z-index", "-1");
			$("#modal_info_integracao").modal("hide");
		}

	}
');
?>