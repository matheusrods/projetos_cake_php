
    <?php if(!empty($clientes)):?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="input-mini">Código</th>
                    <th><input type="checkbox" id="select_all_clientes_subperfil_receive"></th>
                    <th>Razão Social</th>
                    <th>CNPJ</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                    <th class='input-mini'>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><input type="checkbox" ></td>
                    <td class="input-mini"><?= $cliente['Cliente']['codigo'] ?></td>
                    <td><?php echo $cliente['Cliente']['razao_social'] ?></td>
                    <td><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>
                    <td><?php echo $cliente['ClienteEndereco']['cidade'] ?></td>
                    <td><?php echo $cliente['ClienteEndereco']['estado_abreviacao'] ?></td>
                    
                    <td>
                        <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-trash', 'escape' => false, 'title'=>'Remover Cliente','onclick' => "excluir('{$cliente['UsuarioMultiCliente']['codigo']}')")); ?>
                    </td>
                </tr>
                <?php endforeach; ?>        
            </tbody>
        </table>
    <?php else:?>
        <div id="clientes_selecionados_subperfil_receive">

            <table class="table table-striped">
                <thead>
                <tr>
                    <th><input type="checkbox" id="select_all_clientes_subperfil_receive"></th>
                    <th class="input-mini">Código</th>
                    <th>Razão Social</th>
                    <th>CNPJ</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>
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
    div.load(baseUrl + "usuarios_multi_cliente/listagem_subperfil/'.$codigo_usuario.'/" + Math.random());
}   
');
?>

<script>
    $(function(){

        $("#select_all_clientes_subperfil_receive").on("change", function() {

            if ($(this).is(":checked")) {
                $('#clientes_selecionados_subperfil_receive table tbody tr input:checkbox').prop('checked','checked');
            } else {
                $('#clientes_selecionados_subperfil_receive table tbody tr input:checkbox').removeProp('checked');
            }
        })
    })
</script>
