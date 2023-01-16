    <h3>Clientes liberados para exibição:</h3>
    
    <?php if(!empty($clientes)):?>
    <div>
        <table class="table table-striped" id="clientes-table-subperfil_edit">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select_all_clientes_subperfil_edit"></th>
                    <th class="input-mini">Código</th>
                    <th>Razão Social</th>
                    <th>CNPJ</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><input type="checkbox" class="selecionado_checkbox" id="tr_<?= $cliente['Cliente']['codigo'] ?>"></td>
                        <td class="input-mini"><?= $cliente['Cliente']['codigo'] ?></td>
                        <td><?php echo $cliente['Cliente']['razao_social'] ?></td>
                        <td><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>
                        <td><?php echo $cliente['ClienteEndereco']['cidade'] ?></td>
                        <td><?php echo $cliente['ClienteEndereco']['estado_abreviacao'] ?></td>
                    </tr>
                <?php endforeach; ?>        
            </tbody>
        </table>

        <div class="form-actions">
            <button class="btn btn-primary" id="removeSelecionados">Remover selecionados</button>
            <button class="btn btn-warning" id="resetSelecionados">Resetar</button>
        </div>
    </div>
    <?php else:?>
        <div class="alert">Nenhum dado foi encontrado.</div>
    <?php endif;?> 

<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
    $("[data-toggle=\"tooltip\"]").tooltip();
    $(document).on("click", ".dialog_cliente_usuario", function(e) {
        e.preventDefault();
        open_dialog(this, "Clientes", 990);
    });
});

		
		
function excluir(codigo){
		
	$.ajax({
		type: "POST",        
        url: "/portal/usuarios_multi_cliente/excluir",
        dataType : "json",
        data: {
        	"codigo": codigo 
		},
		success : function(retorno){
			if(retorno) {
				atualizaLista();
			}
        },
		error : function(error){
        	alert("Erro! Não é possível cadastrar o Cliente!");
    	}
	});		
}		
		
function atualizaLista() {		
    var div = jQuery("div#cliente-usuario-lista");
		
    bloquearDiv(div);
    div.load(baseUrl + "usuarios_multi_cliente/listagem/'.$codigo_usuario.'/" + Math.random());
}   
');
?>

<script>
    $(function(){

        $("#select_all_clientes_subperfil_edit").on("change", function() {
console.log('lll')
            if ($(this).is(":checked")) {
                $('#clientes-table-subperfil_edit input:checkbox').prop('checked','checked');
            } else {
                $('#clientes-table-subperfil_edit  input:checkbox').removeProp('checked');
            }
        });

        $("#removeSelecionados").on("click", function(e){

            e.preventDefault();
            var codigo_usuario = <?= $codigo_usuario; ?>;
            var multi_cliente = [];

            $("#clientes-table-subperfil_edit .selecionado_checkbox").each(function(){

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
                url: "/portal/usuarios_multi_cliente/remove_multi_clientes",
                success: function(retorno){
                    console.log(retorno);

                    if(retorno == 1){

                        if($("#cliente-usuario-lista div.alert").length > 0){
                            $("#cliente-usuario-lista div.alert").remove();
                        }

                        atualizaLista();
                    } else {
                        alert("Erro! Não é possível remover o Cliente!");
                    }
                },
                error : function(error){
                    alert("Erro! Não é possível cadastrar o Cliente!");
                }
            });

            return;

        })

        $("#resetSelecionados").on("click", function(e){
            e.preventDefault();

            $('table input:checkbox').removeAttr('checked');
        });
    })
</script>
