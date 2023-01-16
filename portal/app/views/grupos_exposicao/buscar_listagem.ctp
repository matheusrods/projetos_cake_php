<?php if(!empty($grupos_exposicao)):?>
    <?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>
<table class="table table-striped" id="grupos_exposicao">
	<thead>
		<tr>
			<th class="input-mini">Código</th>
			<th>Setor</th>
            <th>Cargo</th>
			<th>Risco</th>
			<th class="input-mini">Ações</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($grupos_exposicao as $key => $dados): ?>
			<tr>
				<td class="input-mini" id="codigo_<?=$key?>"><?php echo $dados['GrupoExposicao']['codigo'] ?></td>
                <td id="setor_<?=$key?>"><?php echo $dados['Setor']['descricao'] ?></td>
                <td id="cargo_<?=$key?>"><?php echo $dados['Cargo']['descricao'] ?></td>
				<td id="risco_<?=$key?>"><?php echo $dados['Risco']['nome_agente'] ?></td>
				<td class="action-icon"><?php echo $this->Html->link('', 'javascript:void(0)',array('onclick' => 'insereGrupoExposicao('.$key.','.$dados['GrupoExposicao']['codigo'].')', 'class' => 'icon-plus ', 'title' => 'Incluir Grupo de Exposição')); ?></td>
			</tr>
		<?php endforeach ?>	 
	</tbody>
	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['GrupoExposicao']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
    <div class='row-fluid'>
        <div class='numbers span7'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span4'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 
<?php echo $this->Javascript->codeBlock('

    function insereGrupoExposicao(chave,codigo_grupo_exposicao){
                
        var codigo = $("#codigo_"+chave).html();
        var setor = $("#setor_"+chave).html();
        var cargo = $("#cargo_"+chave).html();
        var risco = $("#risco_"+chave).html();

        var input_codigo = $("#AplicacaoExameCodigoGrupoExposicao");
        input_codigo.val(codigo).change();

        var input_setor = $("#AplicacaoExameSetor");
        input_setor.val(setor).change();

        var input_cargo = $("#AplicacaoExameCargo");
        input_cargo.val(cargo).change();

        var input_risco = $("#AplicacaoExameRisco");
        input_risco.val(risco).change();
        close_dialog();

    }

    function atualizaLista(destino, codigo_cliente) {
        var div = jQuery("div#busca-lista");
        bloquearDiv(div);
        div.load(baseUrl + "grupos_exposicao/buscar_listagem/" + codigo_cliente + "/" + Math.random());
    }
');
?>
<?php echo $this->Js->writeBuffer(); ?>
