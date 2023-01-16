<?php if(!empty($medicos)):?>
    <?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th class="input-mini">Código</th>
			<th>Nome</th>
			<th>Conselho</th>
			<th class="input-mini">Ações</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($medicos as $key => $dados): ?>
			<tr class="resultado-tr" codigo="<?php echo $dados['Medico']['codigo'] ?>" nome="<?php echo $dados['Medico']['nome'];?>" >
				<td class="input-mini"><?php echo $dados['Medico']['codigo'] ?></td>
				<td><?php echo $dados['Medico']['nome'] ?></td>
				<td><?php echo $dados['ConselhoProfissional']['descricao']." - ".$dados['Medico']['numero_conselho']."/".$dados['Medico']['conselho_uf'];?></td>
				<td class="action-icon"><?php echo $this->Html->link('', 'javascript:void(0)',array('onclick' => 'insereFornecedorMedico('.$codigo_fornecedor.','.$dados['Medico']['codigo'].')', 'class' => 'icon-plus ', 'title' => 'Incluir Médico')); ?></td>
			</tr>
		<?php endforeach ?>	 
	</tbody>
	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Medico']['count']; ?></td>
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

    function insereFornecedorMedico(codigo_fornecedor, codigo_medico){
    	$.ajax({

            type: "POST",        
            url: "/portal/fornecedores_medicos/incluir",        
            dataType : "json",
            data: {
            	"codigo_fornecedor": codigo_fornecedor, 
            	"codigo_medico": codigo_medico
            },
            success : function(data){ 
                close_dialog();
                atualizaFornecedorMedico();       
          	},
          	error : function(error){
                console.log(error);
          	}
        }); 
    }

    function atualizaFornecedorMedico(){
        var div = jQuery("#fornecedor-medico-lista");
        bloquearDiv(div);
        div.load(baseUrl + "fornecedores_medicos/listagem/'.$codigo_fornecedor.'/" + Math.random());
    }
');
?>
<?php echo $this->Js->writeBuffer(); ?>
