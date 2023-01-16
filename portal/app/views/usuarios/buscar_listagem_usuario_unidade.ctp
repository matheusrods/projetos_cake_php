<?php if(!empty($dados_clientes)):?>
    <?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th class="input-mini">Código</th>
            <th>Razão Social</th>
            <th>CNPJ</th>
            <th>Cidade</th>
			<th class="input-mini">Estado</th>
			<th class="action-icon">Ações</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($dados_clientes as $key => $dados): ?>
			<tr style="font-size:12px;">
				<td class="input-mini"><?php echo $dados['Cliente']['codigo'] ?></td>
                <td><?php echo $dados['Cliente']['razao_social'] ?></td>
                <td><?php echo $buonny->documento($dados['Cliente']['codigo_documento']);?></td>
                <td><?php echo $dados['ClienteEndereco']['cidade'] ?></td>
				<td class="input-mini"><?php echo $dados['ClienteEndereco']['estado_abreviacao'] ?></td>
				<td class="action-icon"><?php echo $this->Html->link('', 'javascript:void(0)',array('onclick' => 'insereUsuarioUnidade('.$codigo_usuario.','.$dados['Cliente']['codigo'].')', 'class' => 'icon-plus ', 'title' => 'Incluir')); ?></td>
			</tr>
		<?php endforeach ?>	 
	</tbody>
	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Cliente']['count']; ?></td>
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
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock('

    $(document).ready(function() {
        $("[data-toggle=\"tooltip\"]").tooltip();
    });

    function insereUsuarioUnidade(codigo_usuario, codigo_cliente){
		
    	$.ajax({

            type: "POST",        
            url: "/portal/usuarios/usuario_unidade_incluir",
            dataType : "json",
            data: {
                "codigo_usuario": codigo_usuario, 
            	"codigo_cliente": codigo_cliente
            },
            success : function(retorno){ 
                if(retorno == 0){
                    alert("Erro! Não é possível inlcuir a Unidade!");
                }
                else if(retorno == 1){
                    if($("#cliente-usuario-lista div.alert").length > 0){
                        $("#cliente-usuario-lista div.alert").remove();
                    }
		
                    atualizaLista(); 
                    atualizaListaBusca();      
					$(".ui-dialog-titlebar-close").click();
                }
                else{
                    alert("Unidade já cadastrado!");
                }
          	},
          	error : function(error){
                alert("Erro! Não é possível incluir Unidade!");
          	}
        }); 
    }

    function atualizaLista() {
        var div = jQuery("#usuario_unidades_lista");
		
        // var div = jQuery("#cliente-usuario-lista table");
        bloquearDiv(div);        
        div.load(baseUrl + "usuarios/usuarios_unidades_listagem/'.$codigo_usuario.'/" + Math.random());
    }

    function atualizaListaBusca(){
        var div = jQuery("#busca-lista");
        bloquearDiv(div);
        div.load(baseUrl + "usuarios/buscar_listagem_usuario_unidade/' . $codigo_usuario . '/" + Math.random());
    }
');
?>
<?php echo $this->Js->writeBuffer(); ?>
