<?php if(!empty($dados_clientes)):?>
    <?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>
<table class="table table-striped clientes-table-subperfil_list">
	<thead>
		<tr>
            <th><input type="checkbox" id="select_all_clientes_subperfil_list"></th>
            <th class="input-mini">Códigossss</th>
            <th>Razão Social</th>
            <th>CNPJ</th>
            <th>Cidade</th>
            <th class="input-mini">Estado</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($dados_clientes as $key => $dados): ?>
			<tr style="font-size:12px;">
                <td><input type="checkbox" class="selecionado_checkbox" id="tr_<?= $dados['Cliente']['codigo'] ?>"></td>
				<td class="input-mini"><?php echo $dados['Cliente']['codigo'] ?></td>
                <td><?php echo $dados['Cliente']['razao_social'] ?></td>
                <td><?php echo $buonny->documento($dados['Cliente']['codigo_documento']);?></td>
                <td><?php echo $dados['ClienteEndereco']['cidade'] ?></td>
				<td class="input-mini"><?php echo $dados['ClienteEndereco']['estado_abreviacao'] ?></td>
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

    <div class="form-actions">
        <button class="btn btn-primary" id="addSelecionados">Salvar</button>
        <button class="btn btn-warning" id="reseteSelecionadosModal">Resetar</button>
    </div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock('

    
');
?>
<?php echo $this->Js->writeBuffer(); ?>

<script>

    $(function(){

        $("[data-toggle=\"tooltip\"]").tooltip();

        function insereClienteUsuario(codigo_usuario, codigo_cliente){

            $.ajax({

                type: "POST",
                url: "/portal/usuarios_multi_cliente/incluir",
                dataType : "json",
                data: {
                    "codigo_usuario": codigo_usuario,
                    "codigo_cliente": codigo_cliente
                },
                success : function(retorno){
                    if(retorno == 0){
                        alert("Erro! Não é possível cadastrar o Cliente!");
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
                        alert("Cliente já cadastrado!");
                    }
                },
                error : function(error){
                    alert("Erro! Não é possível cadastrar o Cliente!");
                }
            });
        }

        function atualizaLista() {
            var div = jQuery("#cliente-usuario-lista");

            // var div = jQuery("#cliente-usuario-lista table");
            bloquearDiv(div);
            div.load(baseUrl + "usuarios_multi_cliente/listagem/<?= $codigo_usuario; ?>/" + Math.random());
        }

        function atualizaListaBusca(){
            var div = jQuery("#busca-lista");
            bloquearDiv(div);
            div.load(baseUrl + "usuarios_multi_cliente/buscar_listagem_cliente_usuario/<?= $codigo_usuario; ?>/" + Math.random());
        }

        $("#select_all_clientes_subperfil_list").on("change", function() {
            var aplicaCheck = $('.clientes-table-subperfil_list tbody').find('input:checkbox');

            if (this.checked) {
                aplicaCheck.prop('checked','checked');
            } else {
                aplicaCheck.removeAttr('checked');
            }
        })

        $("#reseteSelecionadosModal").on("click", function(e){
            e.preventDefault();

            $('.clientes-table-subperfil_list input:checkbox').removeAttr('checked');

        })

        $("#addSelecionados").on("click", function(e){

            e.preventDefault();
            var codigo_usuario = <?= $codigo_usuario; ?>;
            var multi_cliente = [];

            $(".clientes-table-subperfil_list .selecionado_checkbox").each(function(){

                if ($(this).is(":checked")) {

                    var codigo_cliente = $(this).attr("id");
                    codigo_cliente = codigo_cliente.substring(3);

                    var mult_cliente = {
                        codigo_usuario: codigo_usuario,
                        codigo_cliente: codigo_cliente
                    };

                    multi_cliente.push(mult_cliente);
                }
            });

            var cliente = {
                "codigo_usuario": codigo_usuario
            }

            cliente['multi_clientes'] = multi_cliente

            $.ajax({
                type: "POST",
                data: {data: cliente},
                url: "/portal/usuarios_multi_cliente/incluir_multi_clientes",
                success: function(retorno){
                    console.log(retorno);
                    if(retorno == 0){
                        alert("Erro! Não é possível cadastrar o Cliente!");
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
                        alert("Cliente já cadastrado!");
                    }
                },
                error : function(error){
                    alert("Erro! Não é possível cadastrar o Cliente!");
                }
            });

            return;
        });

    })
</script>
