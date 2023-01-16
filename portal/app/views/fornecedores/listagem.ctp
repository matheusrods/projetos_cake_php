<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<?php if(!empty($fornecedores)):?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini">Código</th>
            <th>Razão Social</th>
            <th>Nome Fantasia</th>
            <th>CNPJ</th>
            <th>Estado</th>
            <th>Cidade</th>
            <?php if($destino != "usuarios"): ?>
                <th style="width:80px;">Documentos da Proposta</th>
            <?php endif ?>
            <th class='input-mini'></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($fornecedores as $fornecedor): ?>
            <?php 
            if($fornecedor['Fornecedor']['qtd_docs_proposta'] == 0):
                $status_proposta = '<span class="badge badge-empty badge-success">Sem Proposta</span>';
            else:
                if($fornecedor['Fornecedor']['qtd_docs_proposta'] == $docs_obrigatorios):
                        $status_proposta = '<span class="badge badge-empty badge-success">OK</span>';
                    else:
                        $status_proposta = '<span class="badge badge-empty badge-important">Pendente</span>';
                    endif;
            endif;?>
        <tr>
            <td class="input-mini"><?= $fornecedor['Fornecedor']['codigo'] ?></td>
            <td><?php echo $fornecedor['Fornecedor']['razao_social'] ?></td>
            <td><?php echo $fornecedor['Fornecedor']['nome'] ?></td>
            <td><?php echo $buonny->documento($fornecedor['Fornecedor']['codigo_documento']) ?></td>
            <td><?php echo $fornecedor['FornecedorEndereco']['estado_descricao'] ?></td>
            <td><?php echo $fornecedor['FornecedorEndereco']['cidade'] ?></td>
            <?php if($destino != "usuarios"): ?>
                <td style="width:80px;"><?php echo $status_proposta?></td>
            <?php endif ?>
            <td>
				<?php if($destino == "usuarios"): ?>
					<?= $html->link('', array('controller' => 'usuarios', 'action' => 'por_fornecedor', $fornecedor['Fornecedor']['codigo']), array('class' => 'icon-wrench', 'title' => 'Usuários da Fornecedor')) ?>
				<?php else: ?>	
					<?= $html->link('', array('action' => 'editar', $fornecedor['Fornecedor']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
				    <?= $html->link('', array('controller' => 'fornecedores_unidades', 'action' => 'index', $fornecedor['Fornecedor']['codigo']), array('class' => 'icon-home', 'title' => 'Unidades')) ?>			
                <?php endif; ?> 
                <a href="javascript:void(0);" onclick="window_log('<?php echo $fornecedor['Fornecedor']['codigo']; ?>');"><i class="icon-eye-open" title="Log dos Prestadores"></i></a>
			</td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>

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
        setup_mascaras(); setup_time(); setup_datepicker();
        $(".modal").css("z-index", "-1");
        $(".modal").css("width", "43%");
    });

    function window_log(codigo_fornecedor)
    {
        var janela = window_sizes();
        window.open(baseUrl + "fornecedores/listagem_log/" + codigo_fornecedor + "/" + Math.random(), janela, "scrollbars=yes,menubar=no,height="+(janela.height-200)+",width="+(janela.width-80)+",resizable=yes,toolbar=no,status=no");
    }

'); ?>  