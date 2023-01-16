<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>

<?php if(!empty($dados)):?>

<table class="table table-striped" style='width:1800px;max-width:none;'>
	<thead>
		<tr>
			<th style="width:95px;">Ações</th>
			<th class="input-medium">Arquivo</th>
			<th class="input-medium">Usuário Inclusão</th>
			<th class="input-medium">Data Inclusão</th>
			<th style="width:35px;">Pedido</th>
			<th class="input-large">Exame</th>
			<th class="input-large">Cliente</th>
			<th class="input-large">Funcionário</th>
			<th class="input-large">Prestador</th>
			<th class="input-mini">Status</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($dados as $item => $dado ): ?>
			<tr>
	            <td>
				 <?php 
				 	$caminho_arquivo = '/files/anexos_exames/'.$dado[0]['caminho_arquivo'];
                    //quando tiver no fileserver
                    if(strstr($dado[0]['caminho_arquivo'],'https://api.rhhealth.com.br')) {
                        $caminho_arquivo = $dado[0]['caminho_arquivo'];
                    }
                    
					echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $caminho_arquivo, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do Exame'));
				?>

				 <a href="javascript:void(0);" onclick="avaliacao_anexo('<?php echo $dado[0]['codigo_anexo']; ?>',<?php echo $dado[0]['ficha_clinica']; ?>,1);"><i class="icon-edit" title="Avaliação do documento"></i></a>
				<?php 
				/*$codigo_log =  $dado[0]['codigo_item_pedido_exame'];
				if($dado[0]['ficha_clinica'] == 1){
					$codigo_log = $dado[0]['codigo_ficha']; 
				}*/
				?>

				 <a href="javascript:void(0);" onclick="window_log('<?= ($dado[0]['ficha_clinica'] == 1) ? $dado[0]['codigo_ficha'] : $dado[0]['codigo_item_pedido_exame']; ?>','<?php echo $dado[0]['ficha_clinica']; ?>');"><i class="icon-eye-open" title="Log dos Prestadores"></i></a>

	            </td>
				<td><?php echo basename($dado[0]['caminho_arquivo']); ?></td>
	        	<td><?php echo  $this->Buonny->leiamais($dado[0]['usuario_inclusao'],30)  ?></td>
	        	<td><?php echo Comum::formataData($dado[0]['data_inclusao'],'timestamp','dmyhms'); ?></td>
	        	<td><?php echo $dado[0]['codigo_pedido']  ?></td>
				<td><?php echo $this->Buonny->leiamais($dado[0]['nome_exame'],30) ?></td>
				<td><?php echo $this->Buonny->leiamais($dado[0]['cliente_razao_social'],30) ?></td>
				<td><?php echo $this->Buonny->leiamais($dado[0]['funcionario_nome'],30) ?></td>
	       		<td><?php echo $this->Buonny->leiamais($dado[0]['fornecedor_razao_social'],30) ?></td>
	       		<td><?php echo ($dado[0]['status_arquivo']) == 1 ? 'Aprovado' : 'Pendente'; ?></td>

			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<div class="modal fade" id="modal_avaliacao_anexo" data-backdrop="static"></div>

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
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>
 
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		setup_mascaras(); setup_time();
		$(".modal").css("z-index", "-1");
		$(".modal").css("width", "43%");
	});


	function atualizaLista(){
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "consultas_agendas/moderacao_anexos_listagem/" + Math.random());
	}

	function avaliacao_anexo(codigo_anexo,ficha_clinica,mostra) {
		if(mostra) {
			
			var div = jQuery("div#modal_avaliacao_anexo");
			bloquearDiv(div);
			div.load(baseUrl + "consultas_agendas/modal_avaliacao_anexo/" + codigo_anexo + "/" + ficha_clinica + "/"+Math.random());
	
			$("#modal_avaliacao_anexo").css("z-index", "1050");
			$("#modal_avaliacao_anexo").modal("show");

		} else {
			$(".modal_avaliacao_anexo").css("z-index", "-1");
			$("#modal_avaliacao_anexo").modal("hide");
		}

	}

    function window_log(codigo, ficha_clinica)
    {
        var janela = window_sizes();
     	if(ficha_clinica == 1){
     	   window.open(baseUrl + "consultas_agendas/log_moderacao_ficha/" + codigo + "/" + Math.random(), janela, "scrollbars=yes,menubar=no,height="+(janela.height-200)+",width="+(janela.width-80)+",resizable=yes,toolbar=no,status=no");
    	} else {
    		window.open(baseUrl +  "consultas_agendas/log_moderacao_anexo/" + codigo + "/" + Math.random(), janela, "scrollbars=yes,menubar=no,height="+(janela.height-200)+",width="+(janela.width-80)+",resizable=yes,toolbar=no,status=no");
    	}
    }

'); ?>	