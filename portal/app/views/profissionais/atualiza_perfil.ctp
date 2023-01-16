<div class = 'form-procurar'><?php echo $this->element('/filtros/altera_perfil') ?></div>

    <div class='lista'></div>
<?php echo $this->Javascript->codeBlock("
$(document).ready(function() {
    $(document).on('click', '.evt-excluir-usuario', function(e) {
        e.preventDefault();
        var confirmation = window.confirm('Deseja excluir este usuário?');
        if (confirmation === true) {
            var link = $(this).prop('href');

            $.ajax({
                url: link,
                type: 'get',
                success: function(data) {
                    atualizaListaUsuarios();
                },
                error: function(erro) {
                    alert('Não foi possível excluir, tente novamente.');
                }
            });
        }
    });
});", false); ?>