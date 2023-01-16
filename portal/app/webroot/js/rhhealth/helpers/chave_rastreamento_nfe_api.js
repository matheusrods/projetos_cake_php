console.log('chave_rastreamento_nfe_api carregado');

var chave_rastreamento_nfe = {
    "class-key": '.event_on_chave_rastreamento_nfe_api'
};

$('form').on('click', chave_rastreamento_nfe["class-key"], function() {

    console.log( 'click :' + $(this).val() );
    
});

$('form').on('blur', chave_rastreamento_nfe["class-key"], function() {

    console.log( 'blur :' + $(this).val() );

    var _thisVal = $(this).val();

    apiNFsObterCodigo( _thisVal );

    if(_thisVal.length < 44){
        return false;    
    }
    
    apiSerpro( _thisVal ); // TODO: Comentar caso não aprovar POC

    
});

$('form').on('keyup', chave_rastreamento_nfe["class-key"], function() {

    console.log( 'keyup :' + $(this).val() );
    
});

$('form').on('change', chave_rastreamento_nfe["class-key"], function() {

    console.log( 'change :' + $(this).val() );
    
});

function apiNFsObterCodigo( chave_rastreamento_nfe ){
    
    var processData = false;

    // var WWWAuthenticateToken = 'Bearer 4e1a1858bdd584fdc077fb7d80f39283';
    var accepts = 'application/json';
    var contentType = 'application/json';

    var _callbackBeforeSend = function(xhr) {
        
        if(typeof(callbackBeforeSendApiNFsObterCodigo) == 'undefined' || callbackBeforeSendApiNFsObterCodigo == 'null'){

        } else {
            callbackBeforeSendApiNFsObterCodigo();   
        }

        if(contentType){
            xhr.setRequestHeader('Content-Type', contentType);
        }

        xhr.setRequestHeader('Accept', accepts);
        // xhr.setRequestHeader('Authorization', WWWAuthenticateToken);
        //xhr.setRequestHeader('Cache-Control', cacheControl);
    
    };
     
    if(typeof(callbackSuccessApiNFsObterCodigo) == 'undefined' || callbackSuccessApiNFsObterCodigo == 'null'){
         callbackSuccessApiSerpro = function(response){
            console.log('callbackSuccess', response);
        };
    }

    if(typeof(callbackErrorApiNFsObterCodigo) == 'undefined' || callbackErrorApiNFsObterCodigo == 'null'){
         callbackErrorApiSerpro = function(error){
            console.error('callbackError', error);
        };
    }
     
    if(typeof(callbackCompleteApiNFsObterCodigo) == 'undefined' || callbackCompleteApiNFsObterCodigo == 'null'){
         callbackCompleteApiSerpro = function(){};
     }
    

    $.ajax({
            url : baseUrl + "notas_fiscais_servico/obter_codigo_nfe?chave="+chave_rastreamento_nfe,
            type : 'GET',
            dataType : 'json',
            // data : data,
            processData: processData,  
            contentType: contentType,
            beforeSend: _callbackBeforeSend,
            success: callbackSuccessApiNFsObterCodigo,
            error: callbackErrorApiNFsObterCodigo,
            complete: callbackCompleteApiNFsObterCodigo
    });
}

function apiSerpro( chave_rastreamento_nfe ){
    
    var processData = false;

    var WWWAuthenticateToken = 'Bearer 4e1a1858bdd584fdc077fb7d80f39283';
    var accepts = 'application/json';
    var contentType = 'application/json';

    var _callbackBeforeSend = function(xhr) {
        
        if(typeof(callbackBeforeSendApiSerpro) == 'undefined' || callbackBeforeSendApiSerpro == 'null'){

        } else {
            callbackBeforeSendApiSerpro();   
        }

        if(contentType){
            // xhr.setRequestHeader('Content-Type', contentType);
        }

        xhr.setRequestHeader('Accept', accepts);
        xhr.setRequestHeader('Authorization', WWWAuthenticateToken);
        //xhr.setRequestHeader('Cache-Control', cacheControl);
    
    };
     
    if(typeof(callbackSuccessApiSerpro) == 'undefined' || callbackSuccessApiSerpro == 'null'){
         callbackSuccessApiSerpro = function(response){
            console.log('callbackSuccess', response);
        };
    }

    if(typeof(callbackErrorApiSerpro) == 'undefined' || callbackErrorApiSerpro == 'null'){
         callbackErrorApiSerpro = function(error){
            console.error('callbackError', error);
        };
    }
     
    if(typeof(callbackCompleteApiSerpro) == 'undefined' || callbackCompleteApiSerpro == 'null'){
         callbackCompleteApiSerpro = function(){};
     }
    

    $.ajax({
            url : "https://apigateway.serpro.gov.br/consulta-nfe-df-trial/api/v1/nfe/"+chave_rastreamento_nfe,
            type : 'GET',
            // data : data,
            processData: processData,  
            contentType: contentType,
            beforeSend: _callbackBeforeSend,
            success: callbackSuccessApiSerpro,
            error: callbackErrorApiSerpro,
            complete: callbackCompleteApiSerpro
    });
}