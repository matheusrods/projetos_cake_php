/**
 * Serviço de comunicação rest
 * @param {object} options 
 */
var Services = function( options ){

    this.baseUrl = baseUrl; // global var baseUrl
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
    var strUrl = this.baseUrl + options.url ;
    var method = options.type || 'GET';
    var dataType = options.dataType || 'json';
    var cache = options.cache || false;  // desabilitar cache
    var enctype = options.enctype || "multipart/form-data";
    var timeout = options.timeout || 600000; // definir um tempo limite (opcional)
    var processData = options.processData || false; // impedir que o jQuery tranforma a "data" em querystring
    var contentType = options.contentType || false; // desabilitar o cabeçalho "Content-Type"

    var options = {
        url: strUrl,
        type: method,
        dataType: dataType,
        cache: cache,
        enctype: enctype,
        timeout: timeout, 
        processData: processData, 
        contentType: contentType, 
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
