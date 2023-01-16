<?php
    echo $this->Javascript->codeBlock("
        $(document).ready(function() {
            var codigo_cliente = " . $cliente['Cliente']['codigo'] . ";
            atualizaListaClientesProdutosFinanceiro(codigo_cliente);
        });
    ");

    echo $this->Javascript->codeBlock("
        $(document).ready(function() {

            $('.evt-botao-detalhe').bind('click', function() {
                var tag = $(this).find('i');
                if (tag.hasClass('icon-eye-open')) {
                    tag.removeClass('icon-eye-open').addClass('icon-eye-close');
                    $('tr.produto-servico, tr.produto-servico-sem-detalhe, tr.produto-servico-detalhe').show();
                    $('.expand').data('expanded', true);
                } else {
                     tag.removeClass('icon-eye-close').addClass('icon-eye-open');
                     $('tr.produto-servico, tr.produto-servico-sem-detalhe, tr.produto-servico-detalhe').hide();
                     $('.expand').data('expanded', false);
                }
            });

            $(document).on('click', '.evt-excluir-cliente-produto', function(e) {
                e.preventDefault();
                var confirmation = window.confirm('Deseja cancelar o produto para o cliente?');
                if (confirmation === true) {
                    var link = $(this).prop('href');

                    $.ajax({
                        url: link,
                        type: 'get',
                        success: function(data) {
                            atualizaListaClientesProdutos('gerenciar', codigo_cliente);
                        },
                        error: function(erro) {
                            alert('Não foi possível excluir, tente novamente.');
                        }
                    });
                }
            });

            $(document).on('click', '.evt-incluir-cliente-produto', function(e) {
                e.preventDefault();
                var link = $(this).prop('href');
                open_dialog(link, 'Incluir produto', 600);
            });

            $(document).on('click', 'tr input, tr a', function(e) {
                e.stopPropagation();
            });


            $(document).on('change', 'input.todos', function() {
                var cls = $(this).prop('class').match(/servico-\d+/)[0];
                var col = $(this).parents('td').prop('class').match(/col-\d+/)[0];
                $([['td.', col].join(''), cls].join(' .')).val($(this).val());
            });


            $(document).on('click', '.evt-editar-servico', function(e) {
                e.preventDefault();
                var link = $(this).prop('href');
                open_dialog(link, 'Editar', 600);
            });

            $(document).on('click', '.evt-excluir-servico', function(e) {
                e.preventDefault();
                var link = $(this).prop('href');
                var confirmation = window.confirm('Deseja realmente remover o serviço?');

                if (confirmation === true) {
                    $.ajax({
                        url: link,
                        type: 'get',
                        success: function(data) {
                            atualizaListaClientesProdutos('gerenciar', codigo_cliente);
                        },
                        error: function(erro) {
                            alert('Não foi possível excluir, tente novamente.');
                        }
                    });
                }
            });

            $(document).on('click', '.evt-alterar-status', function(e) {
                e.preventDefault();
                var link = $(this).prop('href');
                open_dialog(link, 'Alterar status', 640);
            });

        });
    ");

?>
<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['codigo']); ?>
    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['razao_social']); ?>
</div>
<div class="lista"></div>
