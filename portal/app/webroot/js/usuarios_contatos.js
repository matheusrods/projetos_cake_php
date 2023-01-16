jQuery(document).ready(function(){
    codigo_usuario = jQuery("#UsuarioCodigo").val();
    if (window.vizualizar) {
        carrega_contatos_usuario_visualizar(codigo_usuario);
    } else {
        carrega_contatos_usuario(codigo_usuario);
    }

     $(document).on("click", ".dialog", function(e) {
        e.preventDefault();
        open_dialog(this, "Contato", 960);
    });
});

function carrega_contatos_usuario(codigo_usuario) {
    var div = jQuery("#contato-usuario");
    bloquearDiv(div);
    div.load(baseUrl + 'usuarios_contatos/contatos_por_usuario/' + codigo_usuario + '/' + Math.random() );
}

function carrega_contatos_usuario_visualizar(codigo_usuario) {
    var div = jQuery("#contato-usuario");
    bloquearDiv(div);
    div.load(baseUrl + 'usuarios_contatos/contatos_por_usuario_visualizar/' + codigo_usuario + '/' + Math.random() );
}

function excluir_usuario_contato(codigo_usuario_contato, codigo_usuario) {
    if (confirm('Deseja realmente excluir ?'))
        jQuery.ajax({
            type: 'POST',
            url: baseUrl + 'usuarios_contatos/excluir/' + codigo_usuario_contato + '/' + Math.random()
            ,success: function(data) {
                carrega_contatos_usuario(codigo_usuario);
            }
        });
    }
