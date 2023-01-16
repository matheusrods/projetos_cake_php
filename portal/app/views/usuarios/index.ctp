<div class = 'form-procurar'><?php echo $this->element('/filtros/usuarios') ?></div>

    <?php if (!isset($minha_configuracao) && $minha_configuracao != "minha_configuracao") : ?>
        <div class='actionbar-right'><?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Usuarios'));?></div>
    <?php endif; ?>

    <div class='lista' style="margin-top: 10px;"></div>
<?php
    $usuario_config = "";
    if (isset($minha_configuracao) && $minha_configuracao = "minha_configuracao") {
        $minha_configuracao = "minha_configuracao";
    } else {
        $minha_configuracao = "null";
    }
?>

<script type="text/javascript">

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
                        atualizaListaUsuarios2();
                    },
                    error: function(erro) {
                        alert('Não foi possível excluir, tente novamente.');
                    }
                });
            }
        });

        function atualizaListaUsuarios2() {
            var div = jQuery('div.lista');
            bloquearDiv(div);
            
            var minha_configuracao = <?= $minha_configuracao; ?>

            if ($minha_configuracao == 'minha_configuracao') {
                div.load(baseUrl + 'usuarios/listagem/' + minha_configuracao + '/' + Math.random());
            } else {
                div.load(baseUrl + 'usuarios/listagem/' + minha_configuracao + '/' + Math.random());      
            }
            
        }
    
    });
</script>
