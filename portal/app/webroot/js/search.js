(function($) {
    function generateIcon(id) {
        return '<a id="' + id + '"href="javascript:void(0)" class="icon-search"></a>';
    }

    $.fn.search_clientes = function() {
        return this.each(function() {
            var input = $(this);
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var link = "/portal/clientes/buscar_codigo/searcher:" + root_id + "/" + Math.random();
                    open_dialog(link, "Clientes", 940);
                });
            }
        });

    } 

    $.fn.search_label_questoes = function() {
        return this.each(function() {
            var input = $(this);
            var root_id = input.attr('id')+'-search';
            var type = input.attr('data-type');
            $(this).click(function() {
                var link = "/portal/questoes/buscar_codigo/searcher:" + root_id + "/type:" + type + '/' + Math.random();
                    open_dialog(link, "Localizar perguntas", 940);
                });

            // if ($('#' + root_id).length == 0) {
            //     input.after(generateIcon(root_id));
            //     var icon_search = $('#' + root_id);
            //     icon_search.css('display', input.css('display'));
            //     icon_search.click(function() {
            //         var link = "/portal/questoes/buscar_codigo/searcher:" + root_id + "/" + Math.random();
            //         open_dialog(link, "Questoes", 940);
            //     });
            // }
        });

    } 

    $.fn.search_fornecedor = function(input_id,input_display) {
        return this.each(function() {
            var searcher = $(input_id).attr('id');
            var display = $(input_display).attr('id');

            var input = $(this);
            
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var link = "/portal/fornecedores/buscar_codigo/searcher:" + searcher + "/display:" + display + "/" + Math.random();
                    open_dialog(link, "Fornecedores", 940);
                });
            }
        });
    }  
    
    $.fn.search_cnae = function(input_id, input_display) {
        return this.each(function() {
            var searcher = $(input_id).attr('id');
            var display = $(input_display).attr('id');

            var input = $(this);
            var root_id = input.attr('id')+'-search';
            
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    
                    var classe = $('#ClienteCnae-search').attr('class');
                    $('#ClienteCnae-search').removeAttr().attr('class', 'icon-loading');
                    
                    var link = "/portal/cnae/busca_cnae/searcher:" + searcher + "/display:" + display + "/" + Math.random();
                    open_dialog(link, "Busca de CNAE", 940);
                    
                    $('#ClienteCnae-search').attr('class', classe);
                });
            }
        });
    }    

    $.fn.search_cid = function(input_id, input_display) {
        return this.each(function() {
            var searcher = $(input_id).attr('id');
            var display = $(input_display).attr('id');

            var input = $(this);
            var root_id = input.attr('id')+'-search';
            
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                	
                	var classe = $('#CidCodigoCid10-search').attr('class');
                	$('#CidCodigoCid10-search').removeAttr().attr('class', 'icon-loading');
                	
                    var link = "/portal/cid/busca_cid/searcher:" + searcher + "/display:" + display + "/" + Math.random();
                    open_dialog(link, "Busca de CID", 940);
                    
                    $('#CidCodigoCid10-search').attr('class', classe);
                });
            }
        });
    }
    
    
    $.fn.search_credenciado = function(input_id,input_display) {
        return this.each(function() {
            var searcher = $(input_id).attr('id');
            var display = $(input_display).attr('id');

            var input = $(this);
            
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var link = "/portal/propostas_credenciamento/buscar_codigo/searcher:" + searcher + "/display:" + display + "/" + Math.random();
                    open_dialog(link, "Propostas de Credenciamento", 940);
                });
            }
        });
    }    

    $.fn.search_corretoras = function(input_id, input_display) {
        return this.each(function() {
            var input = $(this);
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var link = "/portal/corretoras/buscar_codigo/searcher:" + input_id + "/display:" + input_display + "/" + Math.random();
                    open_dialog(link, "Corretoras", 940);
                });
            }
        });

    }
    
    $.fn.search_ceps = function() {
        return this.each(function() {
            var input = $(this);
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function(e) {
                    var link = "/portal/enderecos/buscar_cep/searcher:" + root_id + "/" + Math.random();
                    return open_dialog(link, "Endereços", 940);
                });
            }
        });
        
    }
})(jQuery);

(function($) {
    $.fn.search_enderecos = function() {
        return this.each(function() {
            var input_cep = $(this);

            input_cep.blur(function() {
                var comboEndereco = input_cep.parent().parent().find('.codigo_endereco');
                bloquearDiv(input_cep.parent().parent());
                if(input_cep.val() != ''){
                    $.post(
                        baseUrl + 'enderecos/listar_por_cep/' + input_cep.val() + '/' + Math.random(),
                        function(data) {                            
                            comboEndereco.html(data);
                            qtde_options = comboEndereco.children('option').length;
                            $(input_cep).parent().parent().find('.cep-nao-encontrado').remove();
                            if(qtde_options < 2){
                                input_cep.parent().removeClass('error');
                                input_cep.parent().find(".error-message").remove();                 
                                input_cep.parent().find('#lbl-error').remove();
                                input_cep.addClass('form-error').parent().addClass('error').append('<div id="lbl-error" class="help-block">CEP inválido</div>');
                            }else{
                                verificarCEPUnico( input_cep );
                                input_cep.parent().find('#lbl-error').remove();
                                input_cep.parent().removeClass('error');
                                input_cep.parent().removeClass('error-message');
                            }                            
                            input_cep.parent().parent().unblock();
                        }
                    );
                }else{
                    comboEndereco.html('<option value="">Selecione um endereço..</option>');
                    input_cep.parent().find('#lbl-error').remove();
                    input_cep.parent().removeClass('error');
                    input_cep.parent().removeClass('error-message');
                    input_cep.parent().parent().unblock();
                }

            });

            input_cep.keydown(function(e){
                if (e.keyCode == '13') {
                    e.preventDefault();
                    input_cep.blur();
                    input_cep.parent().parent().find("input[id$='Numero']").focus();
                }
            });

        });
    }
})(jQuery);

(function($) {
    function generateIcon(id) {
        return '<a id="' + id + '"href="javascript:void(0)" class="icon-search"></a>';
    }

    $.fn.search_referencias = function(localizador_input_codigo_cliente, localizador_input_codigo_referencia, localizador_input_codigo_cliente2) {
        return this.each(function() {
            var input = $(this);
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var codigo_cliente = $(localizador_input_codigo_cliente).val();
                    var codigo_cliente2 = $(localizador_input_codigo_cliente2).val();
                    if (codigo_cliente > 0 || codigo_cliente2 > 0) {
                        var link = "/portal/referencias/buscar_codigo" + "/searcher:" + localizador_input_codigo_referencia.replace('#','') + "/display:" + input.attr('id') + "/codigo:" + codigo_cliente  + "/codigo2:" + codigo_cliente2 + "/" + Math.random();
                        open_dialog(link, "Alvos", 940);
                    } else {
                        alert('Cliente não informado');
                    }
                });
            }
        });
    }

    $.fn.search_rotas = function(localizador_input_codigo_cliente, localizador_input_codigo_referencia) {
        return this.each(function() {
            var input = $(this);
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var codigo_cliente = $(localizador_input_codigo_cliente).val();
                    if (codigo_cliente > 0) {
                        var link = "/portal/rotas/buscar_codigo" + "/searcher:" + localizador_input_codigo_referencia.replace('#','') + "/display:" + input.attr('id') + "/codigo:" + codigo_cliente  + "/" + Math.random();
                        open_dialog(link, "Rotas", 940);
                    } else {
                        alert('Cliente não informado');
                    }
                });
            }
        });
    }

    $.fn.search_rotas_emb_transp = function(localizador_input_codigo_embarcador, localizador_input_codigo_transportador, localizador_input_codigo_referencia) {
        return this.each(function() {
            var input = $(this);
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var codigo_embarcador = $(localizador_input_codigo_embarcador).val();
                    var codigo_transportador = $(localizador_input_codigo_transportador).val();
                    if (codigo_embarcador > 0 || codigo_transportador > 0) {
                        var link = "/portal/rotas/buscar_codigo" + "/searcher:" + localizador_input_codigo_referencia.replace('#','') + "/display:" + input.attr('id') + "/codigo_embarcador:" + codigo_embarcador+ "/codigo_transportador:" + codigo_transportador  + "/" + Math.random();
                        open_dialog(link, "Rotas", 940);
                    } else {
                        alert('Cliente não informado');
                    }
                });
            }
        });
    }

    $.fn.search_escoltas = function(localizador_input_codigo_escolta) {
        return this.each(function() {
            var input = $(this);
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var link = "/portal/escoltas/buscar_codigo/searcher:" + localizador_input_codigo_escolta.replace('#','') + "/display:" + input.attr('id') + "/" + Math.random();
                    open_dialog(link, "Empresas Escoltas", 800);
                    
                });
            }
        });
    }

   $.fn.search_prestadores = function() {
        return this.each(function() {
            var input = $(this);            
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var latitude = 0;
                    var longitude = 0;                
                    if($("#HistoricoSmLatitude").val() != '')
                        latitude = $("#HistoricoSmLatitude").val();
                    if($("#HistoricoSmLongitude").val() != '')
                        longitude = $("#HistoricoSmLongitude").val();                    
                    var link = "/portal/prestadores/mapa_prestadores/searcher:" + root_id + "/latitude:" +latitude+ "/longitude:"+longitude+"/"+ Math.random();                    
                    open_dialog(link, "Prestadores", 940);
                });
            }
        });

    }

    $.fn.search_risco = function(input_id,input_display) {
        return this.each(function() {
            var searcher = $(input_id).attr('id');
            var display = $(input_display).attr('id');
            var input = $(this);
            
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var link = "/portal/riscos/buscar_risco/input_id:" + searcher + "/input_display:" + display + "/" + Math.random();
                    open_dialog(link, "Riscos", 940);
                });
            }
        });
    }

    $.fn.search_cbo = function(input_id,input_display) {
        return this.each(function() {
            var searcher = $(input_id).attr('id');
            var display = $(input_display).attr('id');
            var input = $(this);
            
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var link = "/portal/cbo/localiza_cbo/input_id:" + searcher + "/input_display:" + display + "/" + Math.random();
                    open_dialog(link, "CBO", 800);
                });
            }
        });
    }

    $.fn.search_medico = function(input_id) {
        return this.each(function() {
            var searcher = $(input_id).attr('id');
            var input = $(this);
            
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var link = "/portal/medicos/buscar_medico/input_id:" + searcher + "/" + Math.random();
                    open_dialog(link, "Profissional", 940);
                });
            }
        });
    }

    $.fn.search_medico_readonly = function(input_id, input_crm_display, input_uf_display, input_nome_display, input_cpf_display) {
    	return this.each(function() {
            var searcher = $(input_id).attr('id'); 
            var display_crm  = $(input_crm_display).attr('id');
            var display_uf  = $(input_uf_display).attr('id');
            var display_nome  = $(input_nome_display).attr('id');
            var display_cpf  = $(input_cpf_display).attr('id');
            
            var input = $(this);
            
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var link = "/portal/medicos/buscar_medico_readonly/input_id:" + searcher + "/input_crm_display:" + display_crm + "/input_uf_display:" + display_uf + "/input_nome_display:" + display_nome + "/input_cpf_display:" + display_cpf + "/" + Math.random();
                    open_dialog(link, "Profissional", 940);
                });
            }
        });
    }    

    $.fn.search_grupo_exposicao = function(input_id, codigo_cliente) {
        return this.each(function() {
            var searcher = $(input_id).attr('id');
            var input = $(this);
            
            
            var root_id = input.attr('id')+'-search';
            if ($('#' + root_id).length == 0) {
                input.after(generateIcon(root_id));
                var icon_search = $('#' + root_id);
                icon_search.css('display', input.css('display'));
                icon_search.click(function() {
                    var link = "/portal/grupos_exposicao/buscar_grupo_exposicao/input_id:" + searcher + "/"+ "codigo_cliente:"+ codigo_cliente + "/" + Math.random();
                    open_dialog(link, "Grupos de Exposição", 940);
                });
            }
        });
    }

    
})(jQuery); 