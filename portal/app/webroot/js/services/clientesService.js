var ClientesService = {

    obterCaminhoImagem : function ( options )
    {
        var codigo_cliente = options.codigo_cliente

        var options = {
            url: 'clientes/logotipo/'+codigo_cliente, 
            animeInput: options.animeInput
        };
        
        var svc = new Services();
        return svc.ajax(options);
    }
};

// var consulta = ClientesService.obterCaminhoImagem({
//     codigo_cliente: {$codigo}, 
//     animeInput: false
// });

// consulta.then(function(dados){

//     console.log('dados', dados);

//     if(dados.error){
//         console.log(dados.error);
//         return;
//     }
    
    
// });