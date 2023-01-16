(function($) {
    $.fn.autocomplete_funcionario = function(localizador_input_codigo_cliente, localizador_input_codigo) {
        return this.each(function() {
        	var input_codigo = $(localizador_input_codigo);
            $(this).autocomplete({
				source: function (request, response) {
					var codigo_cliente = $(localizador_input_codigo_cliente).val();
					input_codigo.val("").change();
					if (codigo_cliente > 0) {
						$.ajax({
							url: baseUrl + "clientes_funcionarios/autocomplete_funcionario/codigo:" + codigo_cliente +"/"+ Math.random(), 
							data: request,
							dataType: 'json',
							success: function (data) {
								if (data.length === 0)
									data = [{label: "Nenhum", value: 0}];
								response(data);
							},
							error: function () {
								response([]);
							}
						});
					} else {
						response([]);
					}
				},
				minLength: 1,
				focus: function(){return false;},
				select: function( event, ui ){
					var name 	= $(this);
					input_codigo.val(ui.item.value).change();
					name.val(ui.item.label);
					return false;
				}
			}).data( "uiAutocomplete" )._renderItem = function( ul, item) {
				var li = $( "<li></li>" ).data( "item.autocomplete", item );
				if (item.value == 0) {
					li.append( "Nenhum item encontrado <br />" );
				} else {
					li.append( "<a>"+ item.label + "</a>" )
				}
				return li.appendTo( ul );
			};
        });
    }    

    $.fn.autocomplete_cidades = function(localizador_input_codigo) {
        return this.each(function() {
        	var input_codigo = $(localizador_input_codigo);

            $(this).autocomplete({
				source: function (request, response) {
					$.ajax({
						url: baseUrl + "enderecos/busca_cidades/"+ Math.random(), 
						data: request,
						dataType: 'json',
						success: function (data) {
							if (data.length === 0)
								data = [{label: "Nenhum", value: 0}];
							$(".ui-autocomplete").css({"z-index":"10000"});
							response(data);
						},
						error: function () {
							response([]);
						}
					});
				},
				minLength: 1,
				focus: function(){
					return false;
				},
				select: function( event, ui ){
					var name 	= $(this);
					input_codigo.val(ui.item.value).change();
					name.val(ui.item.label);
					return false;
				}
			}).data( "uiAutocomplete" )._renderItem = function( ul, item) {
				var li = $( "<li></li>" ).data( "item.autocomplete", item );
				if (item.label == "Nenhum") {
					li.append( "Nenhum item encontrado <br />" );
				} else {
					li.append( "<a>"+ item.label + "</a>" )
				}
				return li.appendTo( ul );
			};
        });
    }
})(jQuery);

