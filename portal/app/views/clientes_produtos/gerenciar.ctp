<?php


if ($financeiro && $financeiro !='consulta') {
    echo $this->Html->script('jquery-stree');
    echo $this->Javascript->codeBlock("
        var codigo_cliente = " . $cliente['Cliente']['codigo'] . ";
        $(document).ready(function() {
            atualizaListaClientesProdutosFinanceiro(codigo_cliente);
        });
    ");
}elseif($financeiro == 'consulta'){
    echo $this->Html->script('jquery-stree');
    echo $this->Javascript->codeBlock("
        var codigo_cliente = " . $cliente['Cliente']['codigo'] . ";
        $(document).ready(function() {
            atualizaListaClientesProdutos('consulta', codigo_cliente);
        });
    ");
}else{
    echo $this->Html->script('jquery-stree');
    echo $this->Javascript->codeBlock("
        var codigo_cliente = " . $cliente['Cliente']['codigo'] . ";
        $(document).ready(function() {
            atualizaListaClientesProdutos('gerenciar', codigo_cliente);
        });
    ");
}
    echo $this->Javascript->codeBlock("
        $(document).ready(function() {
        
        	$(document).on('click', '.evt-incluir-servico', function(e) {
        		e.preventDefault();
                var link = $(this).prop('href');
    			open_dialog(link, 'Incluir', 720);
            });

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
                atualizarIcones();
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
                open_dialog(link, 'Editar', 720);
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
            
            $(document).on('click', '.expand', function(e) {
				atualizarIcones();
            });
        });
        
        
        function atualizarIcones() {
        	var rows = $('.expand');
        	rows.each(function() {
            	var icone = $('i', this);
            	var row = $(this);
                if (row.data('expanded')) {
                	icone.removeClass('icon-chevron-right').addClass('icon-chevron-down');
                } else {
                	icone.removeClass('icon-chevron-down').addClass('icon-chevron-right');
                }            	
        	});
        }
        
    ");

?>
<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['codigo']); ?>
    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['razao_social']); ?>
</div>
<div style="margin-bottom:20px;">
    <strong>Legenda, tipo de bloqueio:</strong>&nbsp;
    <span class="badge-empty badge" title="Pendência Comercial"></span>&nbsp;Comercial&nbsp;&nbsp;
    <span class="badge-empty badge badge-important" title="Pendência Financeira"></span>&nbsp;Financeira&nbsp;&nbsp;
    <span class="badge-empty badge badge-warning" title="Pendência Jurídica"></span>&nbsp;Jurídica
</div>
<div class="actionbar-right">
<?php
    if(!$financeiro) {
        echo $this->Html->link('<i class="icon-eye-close"></i>', 'javascript:void(0);',
            array(
                'escape' => false,
                'class' => 'btn evt-botao-detalhe',
                'title' => 'Expandir Produtos e Serviços na Tabela'
            )
        );
    }
?>
</div>
<div class="lista"></div>
