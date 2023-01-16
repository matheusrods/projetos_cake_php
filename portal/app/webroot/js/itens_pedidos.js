var i = $('#key').val();
console.log(i);
var valor_total = $('#valor_total').val();
$(document).ready(function() {

    function calcula(este) {
        var quant = este.parents('.app-begin')
        .find('#js-quant')
        .val();
        var valor = parseFloat(este.parents('.app-begin')
            .find('#js-vl-unit')
            .val()
            .replace(/\./g, '')
            .replace(/,/g, '.'));
        total = new Object();
        total.value = String((quant * valor).toFixed(2));
        este.parents('.app-begin')
        .find('#js-vl-tot')
        .val(moeda(total));
    } 

    function mostra_valor_total(valor) {
        text_valor_total = new Object();
        text_valor_total.value = String(valor);
        $('.js-valor-total').text(moeda(text_valor_total)).parents('.in').removeClass('hide');
    }

    if(valor_total > 0) {
        mostra_valor_total(parseFloat(valor_total).toFixed(2));
    }

    $('.js-add-resultado').click(function(event) {
        var este = $(this);
        var descricao = este.parents('.app-begin').find('#js-desc').val();
        var codigo = este.parents('.app-begin').find('#js-codigo-servico').val();
        var codigo_produto = este.parents('.app-begin').find('#js-codigo-produto').val();
        var quantidade = este.parents('.app-begin').find('#js-quant').val();
        var valor_unitario = este.parents('.app-begin').find('#js-vl-unit').val().replace(/\./g, '').replace(/,/g, '.');
        
        if(descricao == '') {
            alerta_atencao('Insira um serviço.');
            return false;
        }

        if(quantidade == '' || quantidade == '0') {
            alerta_atencao('Insira a quantidade maior que zero.');
            return false;
        }

        if(valor_unitario == '' || valor_unitario <= '0' || valor_unitario == '0.00') {
            alerta_atencao('Insira o valor unitário maior que zero.');
            return false;
        }



        if(descricao != '' && codigo != '' && quantidade != '' && valor_unitario != '') {
            var quantidade = parseInt(quantidade);
            var valor_unitario = parseFloat(valor_unitario).toFixed(2);
            var unit_total = parseFloat(quantidade*valor_unitario).toFixed(2);
            var html = $('#memoria').clone();
            html.find('.div-table').append($('<input>', {type: 'hidden', name: 'data[ItemPedido]['+codigo_produto+']['+i+'][descricao]', value: descricao}));
            html.find('.div-table').append($('<input>', {type: 'hidden', name: 'data[ItemPedido]['+codigo_produto+']['+i+'][codigo_servico]', value: codigo}));
            html.find('.div-table').append($('<input>', {type: 'hidden', name: 'data[ItemPedido]['+codigo_produto+']['+i+'][quantidade]', value: quantidade}));
            html.find('.div-table').append($('<input>', {type: 'hidden', name: 'data[ItemPedido]['+codigo_produto+']['+i+'][valor_unitario]', value: valor_unitario}));
            html.find('.desc').text(descricao);
            html.find('.quant').text(quantidade);
            text_valor_unitario = new Object();
            text_valor_unitario.value = String(valor_unitario);
            html.find('.vl-unit').text(moeda(text_valor_unitario));
            html.find('.vl-unit').attr('data-preco', valor_unitario);
            text_valor_total = new Object();
            text_valor_total.value = String(unit_total);
            html.find('.vl-tot').text(moeda(text_valor_total));
            html = html.html();
            valor_total = (parseFloat(quantidade*valor_unitario) + parseFloat(valor_total)).toFixed(2);
            mostra_valor_total(valor_total);
            $('.inseridos').append(html);
            este.parents('.app-begin').find('#js-desc').val('');
            este.parents('.app-begin').find('#js-codigo-servico').val('');
            este.parents('.app-begin').find('#js-codigo-produto').val('');
            este.parents('.app-begin').find('#js-quant').val('');
            este.parents('.app-begin').find('#js-vl-unit').val('');
            este.parents('.app-begin').find('#js-vl-tot').val('');
            $('#js-desc').focus();
            i++;
        } else {
            swal({
                type: 'warning',
                title: 'Atenção',
                text: 'Preencha todos os campos para inserir o serviço.'
            });
        }
    });

    alerta_atencao = function(msg){
        swal({
            type: 'warning',
            title: 'Atenção',
            text: msg
        });
    }

    $('body').on('click', '.js-remover-resultado', function(event) {        
        var este = $(this);
        var valor = parseFloat(este.parents('.div-table').find('.vl-unit').attr('data-preco'));

        // quando o item existi na base de dados
        // var codigo = este.attr('data');
        // if(codigo != "undefined") {
        //     codigo = codigo.split("_");
        //     remove_item_ajax(codigo[0],codigo[1]);
        // }

        este.parents('.div-table').remove();
        console.log(valor_total);
        valor_total = (valor_total - valor).toFixed(2);
        console.log(valor_total);
        text_valor_total = new Object();
        text_valor_total.value = String(valor_total);
        $('.js-valor-total').text(moeda(text_valor_total));
        i--;
        if(i < 1) {
            $('.js-valor-total').text(moeda(text_valor_total)).parents('.in').addClass('hide');
        }
    });

    //remove item e detalhe quando existir na base de dados
    remove_item_ajax = function(codigo_item,codigo_detalhe) {
        $.ajax({
            type: 'POST',
            url: baseUrl + 'itens_pedidos/excluir_item_detalhe/'+codigo_item+'/'+codigo_detalhe,
            data: {codigo_item: codigo_item, codigo_detalhe: codigo_detalhe},
            dataType : 'json',
            success : function(data) {
                if(data) {
                   return true;
                }
                else{
                    return false;
                }
            },
            error : function(){
                return false;
            }
        });
    }//fim remove_item_ajax

        // ajax auto complete
        var timer;
        $('body').on('keyup', '.js-servico', function() {
            var este = $(this);
            var string = this.value;
            if(string.length >= 2) {
                este.parent().css('position', 'relative');
                $('.loader-gif').remove();
                este.tooltip('destroy');
                este.parent().append(' <img src=\"'+baseUrl+'img/default.gif\" style=\"position:absolute;top:4px;right:0px;\" class=\"loader-gif\">');
                $('.seleciona-servico').remove();
                clearTimeout(timer); 
                timer = setTimeout(function() {
                    $.ajax({
                        url: baseUrl + 'servicos/carrega_servicos_por_ajax',
                        type: 'POST',
                        dataType: 'json',
                        data: {string: string},
                    })
                    .done(function(response) {
                        if(response) {
                            $('.seleciona-servico').remove();
                            var canvas = $('<div>', {class: 'seleciona-servico'}).html(response);
                            este.parent().append(canvas);
                        } else {
                            este.tooltip({title: '<span style=\"font-size-16\">Serviço não localizado no sistema</span>', html: true, trigger: 'hover', placement: 'top'});
                        }
                    })
                    .always(function() {
                        $('.loader-gif').remove();
                    });
                }, 500);
            } else {
                $('.seleciona-servico').remove();
                $('.loader-gif').remove();
            }
        });
        
        $('body').on('click', '.js-click', function() {
            var valor_unitario = parseFloat($(this).attr('data-valor')).toFixed(2);
            text_valor_unitario = new Object();
            text_valor_unitario.value = String(valor_unitario);
            $(this).parents('.app-begin').find('.js-servico').val($(this).find('td:last-child').text());
            $(this).parents('.app-begin').find('#js-vl-unit').val( moeda(text_valor_unitario) );
            $(this).parents('.app-begin').find('#js-codigo-servico').val($(this).attr('data-codigo'));
            $(this).parents('.app-begin').find('#js-codigo-produto').val($(this).attr('data-codigo-produto'));
            $(this).parents('.app-begin').find('#js-quant').val(1);
            calcula($('.js-add-resultado'));
            $('.seleciona-servico').remove();
        });

        $('body').click(function(event) {
            $('.seleciona-servico').remove();
        });

        $('body').on('keyup', '#js-vl-unit', function(event) {
            calcula($(this));
        });

        $('body').on('keyup', '#js-quant', function(event) {
            calcula($(this));
        });

        //========
        
        $('.js-salvar').click(function(event) {
            text_valor_total = new Object();
            text_valor_total.value = String(valor_total);
            if(i < 1) {
                swal({
                    type: 'warning',
                    title: 'Atenção',
                    text: 'Você precisa inserir ao menos um serviço para continuar'
                });
            } else if ( parseFloat($('#PedidoValorDesconto').val()) > parseFloat(text_valor_total.value) ) {
                swal({
                    type: 'warning',
                    title: 'Atenção',
                    text: 'O desconto não pode ser maior que o total do pedido!'
                });
            } else {
                $('.js-submeter').click();
                $('body').append($('<div>', {class: 'ajax-loader'}));
            }
            // console.log( 'Total'+ parseFloat(text_valor_total.value) );
          //  console.log( 'Desconto' + parseFloat($('#PedidoValorDesconto').val()) );
           // console.log('Maior'+ parseFloat($('#PedidoValorDesconto').val()) > parseFloat(text_valor_total.value) );
        });
    });

