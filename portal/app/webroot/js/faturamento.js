(function($) {
    function generateIcon(id) {
        return '<a id="' + id + '"href="javascript:void(0)" class="icon-search gpEmpresa"><span style="display:none" class="ccusto"></span><span style="display:none" class="sub_codigo"></span><span style="display:none" class="codigo_conta"></span></a>';
    }

    $.fn.search_centro_custo = function() {
        return this.each(function() {
            var input = $(this);
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var grupo_empresa = $("#TranpagCcusto-search").attr("rel");
                    var empresa       = $("#TranpagCcusto-search").attr("alt");
                    if(empresa == '' || empresa == undefined)
                        empresa = 0;
                    var link = "/portal/pagamentos_transacoes/listar_centros_de_custos/" + grupo_empresa + "/" + empresa;
                    open_dialog(link, "Centros de Custos", 640);
                });
            }
        });
    }
    
    $.fn.search_sub_codigo = function() {
        return this.each(function() {
            var input = $(this);
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var grupo_empresa = $("#TranpagSubCodigo-search").attr("rel");
                    var empresa       = $("#TranpagSubCodigo-search").attr("alt");
                    if(empresa == '' || empresa == undefined)
                        empresa = 0;
                    
                    var ccusto    = $("#TranpagSubCodigo-search span.ccusto").attr("id");
                    if(ccusto == '' || ccusto == undefined)
                        ccusto = '0';
                    var link = "/portal/pagamentos_transacoes/listar_centros_de_custos_sub_codigos/" + grupo_empresa + "/" + empresa + "/" + ccusto;
                    open_dialog(link, "Sub Códigos", 640);
                });
            }
        });
    }

    $.fn.search_conta = function() {
        return this.each(function() {
            var input = $(this);
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var grupo_empresa = $("#TranpagCodigoConta-search").attr("rel");
                    var empresa       = $("#TranpagCodigoConta-search").attr("alt");
                    if(empresa == '' || empresa == undefined)
                        empresa = 0;
                    
                    var ccusto    = $("#TranpagCodigoConta-search span.ccusto").attr("id");
                    if(ccusto == '' || ccusto == undefined)
                        ccusto = '0';
                    
                    var sub_codigo = $("#TranpagCodigoConta-search span.sub_codigo").attr("id");
                    if(sub_codigo == '' || sub_codigo == undefined)
                        sub_codigo = '0';
                    
                    var link = "/portal/pagamentos_transacoes/listar_codigos_de_conta/" + grupo_empresa + "/" + empresa + "/" + ccusto + "/" + sub_codigo;
                    open_dialog(link, "Código da Conta", 640);
                });
            }
        });
    }
    
    // filtro centro de custo
    var grupo_empresa = $('input[name=\"data[Tranpag][grupo_empresa]\"]:checked').val();
    var empresa       = $('#TranpagEmpresa option:selected').val();
    var ccusto        = $('#TranpagCcusto').val();
    var sub_codigo    = $('#TranpagSubCodigo').val();
    var codigo_conta  = $('#TranpagCodigoConta').val();

    $('#TranpagCcusto').search_centro_custo();
    $('#TranpagSubCodigo').search_sub_codigo();
    $('#TranpagCodigoConta').search_conta();

    $('.gpEmpresa').attr('rel', grupo_empresa);
    $('.gpEmpresa span.ccusto').attr('id', ccusto);
    $('.gpEmpresa span.sub_codigo').attr('id', sub_codigo);
    $('.gpEmpresa span.codigo_conta').attr('id', codigo_conta);


    $('#TranpagCcusto').focusout(function(){
        $('#TranpagCcusto').val( $(this).val() );
        $('#TranpagCcusto-search > span.ccusto').attr('id', $(this).val() );
        $('#TranpagSubCodigo-search > span.ccusto').attr('id', $(this).val() );
        $('#TranpagCodigoConta-search > span.ccusto').attr('id', $(this).val() );
    });

    $('#TranpagSubCodigo').focusout(function(){
        $('#TranpagSubCodigo').val( $(this).val() );
        $('#TranpagCcusto-search > span.sub_codigo').attr('id', $(this).val() );
        $('#TranpagSubCodigo-search > span.sub_codigo').attr('id', $(this).val() );
        $('#TranpagCodigoConta-search > span.sub_codigo').attr('id', $(this).val() );
    });

    $('#TranpagCodigoConta').focusout(function(){
        $('#TranpagCodigoConta').val( $(this).val() );
        $('#TranpagCcusto-search > span.codigo_conta').attr('id', $(this).val() );
        $('#TranpagSubCodigo-search > span.codigo_conta').attr('id', $(this).val() );
        $('#TranpagCodigoConta-search > span.codigo_conta').attr('id', $(this).val() );
    });


    if(empresa != ''){
        $('.gpEmpresa').attr('alt', empresa);
    }

    $('div.control-group label input').click(function(){
        $('#TranpagCcusto-search').attr('rel', this.value);
        $('#TranpagCcusto-search').attr('alt', '');

        $('#TranpagSubCodigo-search').attr('rel', this.value);
        $('#TranpagSubCodigo-search').attr('alt', '');

        $('#TranpagCodigoConta-search').attr('rel', this.value);
        $('#TranpagCodigoConta-search').attr('alt', '');
    });

    $('select#TranpagEmpresa').change(function(){
        $('#TranpagCcusto-search').attr('alt', $('#TranpagEmpresa option:selected').val());
        $('#TranpagSubCodigo-search').attr('alt', $('#TranpagEmpresa option:selected').val());
        $('#TranpagCodigoConta-search').attr('alt', $('#TranpagEmpresa option:selected').val());
    });

    var nameEmpresa = $('#TranpagEmpresa option:selected').text();
    $('.nameEmpresa').after( ' <span>' + nameEmpresa + '</span>' );
    
})(jQuery);