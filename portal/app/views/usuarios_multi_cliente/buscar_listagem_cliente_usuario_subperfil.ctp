<?php if(!empty($dados_clientes)):?>
    <?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>
<table class="table table-striped clientes-table-subperfil">
	<thead>
		<tr>
            <th><input type="checkbox" id="select_all_clientes_subperfil"></th>
			<th class="input-mini">Código</th>
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

                <input type="hidden"
                       id="<?= $dados['Cliente']['codigo'] ?>"
                       razao_social="<?= $dados['Cliente']['razao_social'] ?>"
                       codigo_documento="<?= $dados['Cliente']['codigo_documento'] ?>"
                       cidade="<?= $dados['ClienteEndereco']['cidade'] ?>"
                       estado_abreviacao="<?= $dados['ClienteEndereco']['estado_abreviacao'] ?>"
                       value="<?= $dados['Cliente']['codigo'] ?>"
                />

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
        <button class="btn btn-primary" id="addSelecionadosIncluir">Salvar</button>
        <button class="btn btn-warning" id="removeSelecionados">Resetar</button>
    </div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock('

    $(document).ready(function() {
        $("[data-toggle=\"tooltip\"]").tooltip();
    });

    function atualizaLista() {
        var div = jQuery("#cliente-usuario-lista");
		
        // var div = jQuery("#cliente-usuario-lista table");
        bloquearDiv(div);
        div.load(baseUrl + "usuarios_multi_cliente/listagem_subperfil/'.$codigo_usuario.'/" + Math.random());
    }

    function atualizaListaBusca(){
        var div = jQuery("#busca-lista");
        bloquearDiv(div);
        div.load(baseUrl + "usuarios_multi_cliente/buscar_listagem_cliente_usuario_subperfil/' . $codigo_usuario . '/" + Math.random());
    }
');
?>
<?php echo $this->Js->writeBuffer(); ?>

<script>

    $(function(){

        $("#select_all_clientes_subperfil").on("change", function() {
            var aplicaCheck = $('.clientes-table-subperfil tbody').find('input:checkbox');

            if (this.checked) {
                aplicaCheck.prop('checked','checked');
            } else {
                aplicaCheck.removeAttr('checked');
            }
        })

        $("#addSelecionadosIncluir").on("click", function(){

            $(".clientes-table-subperfil tbody tr :checkbox").each(function(){

                if ($(this).is(":checked")) {

                    var codigo = $(this).attr("id");
                    codigo = codigo.substring(3);

                    var razao_social = $("#"+codigo).attr("razao_social");
                    var codigo_documento = $("#"+codigo).attr("codigo_documento");
                    var cidade = $("#"+codigo).attr("cidade");
                    var estado_abreviacao = $("#"+codigo).attr("estado_abreviacao");

                    var input = '<tr><td><input type="checkbox" ></td><td class="input-mini">'+codigo+'</td><td>'+razao_social+'</td><td>'+codigo_documento+'</td><td>'+cidade+'</td><td>'+estado_abreviacao+'</td><input type="hidden" name="data[Usuario][clientes][]" value="'+codigo+'" /></tr>';
                    $("#clientes_selecionados_subperfil_receive table tbody").append(input);

                    $(".ui-dialog-titlebar-close").click();
                }
            })
        })

        $("#removeSelecionados").on("click", function(){
            var aplicaCheck = $('.clientes-table-subperfil tbody').find('input:checkbox');

            $("#select_all_clientes_subperfil").removeAttr('checked');
            aplicaCheck.removeAttr('checked');
        })

        $(".inserirClienteUsuario").on("click", function(e){
            e.preventDefault();

            var codigo = jQuery(this).attr("id");
            var razao_social = jQuery(this).attr("razao_social");
            var codigo_documento = jQuery(this).attr("codigo_documento");
            var cidade = jQuery(this).attr("cidade");
            var estado_abreviacao = jQuery(this).attr("estado_abreviacao");

            var input = '<tr><td><input type="checkbox" ></td><td class="input-mini">'+codigo+'</td><td>'+razao_social+'</td><td>'+codigo_documento+'</td><td>'+cidade+'</td><td>'+estado_abreviacao+'</td><td> @</td></tr>';
            $("#clientes_selecionados_subperfil table tbody").append(input);

            jQuery(this).closest('tr').remove();

            return;
        })

    })

</script>
