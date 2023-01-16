<?php if(!empty($cids)):?>
    <?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th class="input-mini">CID 10</th>
			<th>Nome</th>
			<th class="input-mini">Ações</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($cids as $key => $dados): ?>
			<tr class="resultado-tr" codigo="<?php echo $dados['Cid']['codigo'] ?>" nome="<?php echo $dados['Cid']['descricao'];?>" >
				<td class="input-small"><?php echo $dados['Cid']['codigo_cid10'] ?></td>
				<td><?php echo $dados['Cid']['descricao'] ?></td>
				<td class="action-icon"><?php echo $this->Html->link('', 'javascript:void(0)',array('onclick' => 'insereAtestadoCid('.$codigo_atestado.','.$dados['Cid']['codigo'].')', 'class' => 'icon-plus ', 'title' => 'Incluir CID')); ?></td>
			</tr>
		<?php endforeach ?>	 
	</tbody>
	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Cid']['count']; ?></td>
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

    function insereAtestadoCid(codigo_atestado, codigo_cid){
    	$.ajax({
            type: "POST",        
            url: "/portal/atestados_cid/incluir",        
            dataType : "json",
            data: {
            	"codigo_atestado": codigo_atestado, 
            	"codigo_cid": codigo_cid
            },
            success : function(data){ 
                close_dialog();
                atualizaAtestadoCid();       
          	},
          	error : function(error){
                console.log(error);
          	}
        }); 
    }

    function atualizaAtestadoCid(){
        var div = jQuery("#atestado-cid-lista");
        bloquearDiv(div);
        div.load(baseUrl + "atestados_cid/listagem/'.$codigo_atestado.'/" + Math.random());
    }
');
?>
<?php echo $this->Js->writeBuffer(); ?>
