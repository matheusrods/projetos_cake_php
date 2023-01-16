
/**
 * Serviço de comunicação rest
 * @param {object} options 
 */
var Services = function( options ){

    this.baseUrl = baseUrl;
}


Services.prototype.ajax = function(options){

    var input = options.animeInput || false;

    // TODO Interceptor
    // $.ajaxSetup({
    // 	beforeSend: function (xhr) {
    // 		// xhr.setRequestHeader('Authorization', 'Token 123')
    // 	},
    // });

    var dfd = $.Deferred();
    var strUrl = this.baseUrl + options.url  + '/' +Math.random();

    var options = {
        url: strUrl,
        beforeSend: function(xhr) {
            if(input){
                bloquearDiv(input.parent());
            }
        },
        success: function(data) {
            dfd.resolve( data );
        },
        error: function(data) {
                console.log('ERRO : ', data); 
                dfd.reject( data );

        },
        complete: function(data) {
            if(input){
                input.parent().unblock();
            }
        }
    };

    $.ajax(options);	

    return dfd.promise();
}


var MultiClienteService = {
    
    obterLista : function ( )
    {
        var options = {url: 'grupos_economicos/obter_multiclientes'};
        
        // TODO chained
        // return new Services().ajax(options);

        var svc = new Services();
        return svc.ajax(options);
    }

};


var UnidadeService = {
    
    obterLista : function ( options )
    {
        var options = {url: 'grupos_economicos_clientes/por_cliente/'+options.codigo_cliente, animeInput: options.animeInput};
        var svc = new Services();
        return svc.ajax(options);
    }

};


var SetorService = {
    
    obterLista : function ( options )
    {
        var options = {url: 'setores/por_cliente/'+options.codigo_cliente, animeInput: options.animeInput};
        var svc = new Services();
        return svc.ajax(options);
    }

};


var CargoService = {
    
    obterLista : function ( options )
    {
        var options = {url: 'cargos/por_cliente/'+options.codigo_cliente, animeInput: options.animeInput};
        var svc = new Services();
        return svc.ajax(options);
    }

};
    

var form_dinamico = function () {

    // recupera
    var obterValorDeCampo = function( input, target ){
        var valor = '';
        if ( target.is( 'select' ) ) {
            valor = input.children('option:selected').val(); 
            // TODO verificar array ??
            if ( parseInt(valor) > 0 ){
                return input.children('option:selected').val();
            }
        } else {
            valor = input.val();
        }
        return valor
    }


    var avaliarOptions = function( options ){

        var helper_name = 'inputs_dinamicos';
        var localOptions = JSON.parse(window.localStorage.getItem(helper_name));

        // associa ao options passado no argumento e repassa a outras pesquisas
        input_grupo_economico = localOptions.valores.codigo_cliente = options.codigo_cliente;

        input_grupo_economico = Object.assign( localOptions, { valores : options });
        
        // console.log('inputs_dinamicos', input_grupo_economico);
        window.localStorage.setItem(helper_name, JSON.stringify(input_grupo_economico));

    };


    var inicializaCodigoCliente = function ( options ) {
            
            var input = $('#'+options.inputs.codigo_cliente);
            if( input.length == 0){
                console.log('Seleção de Matrizes não encontrado nesta página');
                
            }
                input.change(function(event){
                    var _this = $(this);
                    var target = $( event.target );
                    var valor = obterValorDeCampo(_this, target);

                    inicializaCodigoClienteAlocacao(options, {codigo_cliente: valor});
                    inicializaCodigoSetor(options, {codigo_cliente: valor});
                    inicializaCodigoCargo(options, {codigo_cliente: valor});
                });
    };


    var inicializaCodigoClienteAlocacao = function ( options, valor ) {

        // Em algumas paginas o codigo_cliente_alocacao pode ser tratado como codigo_unidade 
        var input = $('#'+options.inputs.codigo_cliente_alocacao);
            if( input.length == 0){
                var input = $('#'+options.inputs.codigo_unidade);
                if( input.length == 0){
                    console.log('Seleção de Unidades não encontrado nesta página');
                    return false;
                } 
            }
            // input.empty().append('<option value=\"\">Selecione a Unidade</option>');
            input.empty().append($('<option />').val('').text('Selecione a Unidade'));

        var consulta = UnidadeService.obterLista({codigo_cliente: valor.codigo_cliente, animeInput: input});
    
            consulta.then(function(dados){
                
                $.each(dados, function(indice, valor) {
                    
                    var matrizDescricao = indice; //arrMatrizDescricao[indice];
    
                    var clientes = this.clientes;
                    
                    input.append($('<optgroup label='+matrizDescricao+'>'));
                    
                    $.each(clientes, function(indice_cliente, valor_cliente) {
                        input.append($('<option />').val(valor_cliente.codigo_cliente).text(valor_cliente.descricao));
                    });
    
                    input.append($('</optgroup>'));
                });
    
            });

            input.change(function(event){
                var _this = $(this);
                var target = $( event.target );
                var valor = obterValorDeCampo(_this, target);
                
                inicializaCodigoSetor(options, {codigo_cliente:valor});
                inicializaCodigoCargo(options, {codigo_cliente:valor});

            });
    }

    var inicializaCodigoSetor = function ( options, valor ) {

        // codigo_setor
        var input = $('#'+options.inputs.codigo_setor);
        if( input.length == 0){
            console.log('Seleção de Setores não encontrado nesta página');
            return false;
        }
        // input.empty().append('<option value=\"\">Selecione o Setor</option>');
        input.empty().append($('<option />').val('').text('Selecione o Setor'));
    
        var consulta = SetorService.obterLista({codigo_cliente: valor.codigo_cliente, animeInput: input});
    
        consulta.then(function(dados){

            $.each(dados, function(indice, valor) {
                
                var matrizDescricao = indice; //arrMatrizDescricao[indice];

                var clientes = valor;

                input.append($('<optgroup label='+matrizDescricao+'>'));
                
                $.each(clientes, function(indice_cliente, valor_cliente) {
                    input.append($('<option />').val(valor_cliente.codigo).text(valor_cliente.descricao));
                });

                input.append($('</optgroup>'));
            });

        });

        input.change(function(event){
            var _this = $(this);
            var target = $( event.target );
            var valor = obterValorDeCampo(_this, target);

        });
    };

    var inicializaCodigoCargo = function ( options, valor ) {

        // codigo_cargo
        var input = $('#'+options.inputs.codigo_cargo);
        if( input.length == 0){
            console.log('Seleção de Cargos não encontrado nesta página');
            return;
        }
        // input.empty().append('<option value=\"\">Selecione o Cargo</option>');
        input.empty().append($('<option />').val('').text('Selecione o Cargo'));

        var consulta = CargoService.obterLista({codigo_cliente: valor.codigo_cliente, animeInput: input});
    
        consulta.then(function(dados){

            $.each(dados, function(indice, valor) {
                
                var matrizDescricao = indice; //arrMatrizDescricao[indice];

                var clientes = valor;

                input.append($('<optgroup label='+matrizDescricao+'>'));
                
                $.each(clientes, function(indice_cliente, valor_cliente) {
                    input.append($('<option />').val(valor_cliente.codigo).text(valor_cliente.descricao));
                });

                input.append($('</optgroup>'));
            });

        });

        input.change(function(event){
            var _this = $(this);
            var target = $( event.target );
            var valor = obterValorDeCampo(_this, target);
            
        });
    };
                                
    return {
        init: function ( options ) {
            
            var model = options.model || 'cache';

            inicializaCodigoCliente( options );
            
        }
    }

}();
