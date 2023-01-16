<div class = 'form-procurar'><?php echo $this->element('/filtros/profissionais') ?></div>
    <div class='actionbar-right'><?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir_profissional'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Profissional'));?></div>
    <div class='lista'></div>
<?php echo $this->Javascript->codeBlock("
$(document).ready(function() {
    setup_mascaras();
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