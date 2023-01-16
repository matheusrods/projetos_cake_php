// ITHEALTH_HELPER
// var ITHEALTH_HELPER = function( strUrl ) { }

function bloquearSubmit(){
    var buttonSubmit = $('.btn-submit-form-default');
        buttonSubmit.attr('disabled', 'disabled');
        buttonSubmit.addClass('disabled');
}

function desbloquearSubmit(){
    var buttonSubmit = $('.btn-submit-form-default');
        buttonSubmit.removeAttr('disabled', 'disabled');
        buttonSubmit.removeClass('disabled');
}

function ithealthAjaxFormRequest(form, options, callbackBeforeSend, callbackSuccess, callbackError, callbackComplete) {
    
    //console.log(form);

    var data = new FormData();

    var nulled = '';
    var method = 'post';
    var processData = false;

    var WWWAuthenticateToken = '11e455ca72d6828c3213e400567bd54e';
    var cacheControl = 'no-store';
    var accepts = 'portal';
    // var contentType = "application/x-www-form-urlencoded";
    // var contentType = 'application/json';
    var contentType = "multipart/form-data";

    var form = jQuery(form);
    
    if(typeof(form.attr('enctype')) != 'undefined'){
        contentType = form.attr('enctype');
    }
    
    if(typeof(jQuery(form).attr('method')) != 'undefined'){
        method = jQuery(form).attr('method');
    }

    var x = $(form).serializeArray(); 

    // avalia campos existentes no form
    $.each(x, function(i, field) { 
        data.append(field.name, field.value);
    }); 

    // adiciona campos file
    $files = $(document).find('.input-file');
    
    if($files.length > 0) 
    {
        contentType = false;
        processData = false;

        $.each($files, function(i, field) { 
            if( $('#'+field.id).val() ) {
                data.append(field.name, $('#'+field.id)[0].files[0]);
            } else {
                data.append(field.name, nulled);
            }
        });
    }
    
    var _callbackBeforeSend = function(xhr) {
        
        if(typeof(callbackBeforeSend) == 'undefined' || callbackBeforeSend == null || callbackBeforeSend == 'null'){

        } else {
            callbackBeforeSend();   
        }

        if(contentType){
            xhr.setRequestHeader('Content-Type', contentType);
        }

        xhr.setRequestHeader('Accept', accepts);
        xhr.setRequestHeader('WWW-Authenticate', WWWAuthenticateToken);
        xhr.setRequestHeader('Cache-Control', cacheControl);
    
    };
     
    if(typeof(callbackSuccess) == 'undefined' || callbackSuccess == null || callbackSuccess == 'null'){
         callbackSuccess = function(response){
            console.log('callbackSuccess', response);
        };
    }

    if(typeof(callbackError) == 'undefined' || callbackError == null || callbackError == 'null'){
         callbackError = function(error){
            console.error('callbackError', error);
        };
    }
     
    if(typeof(callbackComplete) == 'undefined' || callbackComplete == null || callbackComplete == 'null'){
         callbackComplete = function(){};
     }
    

    $.ajax({
            url : jQuery(form).attr('action')+'/',
            type : 'POST',
            data : data,
            processData: processData,  
            contentType: contentType,
            beforeSend: _callbackBeforeSend,
            success: callbackSuccess,
            error: callbackError,
            complete: callbackComplete
        });

    return false;
};



var languageSelect2 = {
    inputTooShort: function (args) { 
        var g=args.minimum-args.input.length;
        var f='Por favor digite '+g+' ou mais caracteres';
        return f;
    },
    noResults: function(){
        return 'Registro(s) não encontrado(s)';
    },
    errorLoading: function(){
        return 'Carregando..., por favor tente novamente se demorar mais de 5s.';
    },
    loadingMore:function(){
        return 'Carregando mais ...';
    }
}; 



jQuery(document).ready(function() {
        
        function composeDateObject(timeString) {
            
            return moment(timeString, 'DD/MM/YYYY');
    
            if (!timeString || timeString.length < 5) {
                return;
            }
            var hour = timeString.split(':')[0];
            var minutes = timeString.split(':')[1];
            return moment({ hour, minutes });
        }
    
    
        function calculateWorkHours(start, end, pause) {
            
            var startDate = composeDateObject(start);
            var endDate = composeDateObject(end) || moment();
            var duration = moment.duration(
                endDate.diff(startDate.add({ minutes: pause }))
            );
            return duration;
        }
        
        function calculaPeriodo(start, end, pause) {
            
            //var startDate = composeDateObject(start);
            var startDate = moment(start,'DD/MM/YYYY H:mm:ss');
            var endDate = moment();
            
            var duration = moment.duration(
                endDate.diff(startDate.add({ minutes: pause }))
            );
            return duration;
        }
    
        /**
         * @param string period
         * @return string
         * */
        function formataDataHoraHumanizada(period, message) {
            
            if (typeof(message) == 'undefined'){
                message = '';
            }
            
            if(!moment(period, ['DD/MM/YYYY H:mm:ss'], true).isValid() ){
                return '';
            }
            
            var period_moment = moment(period,'DD/MM/YYYY H:mm:ss').format('DD/MM/YYYY H:mm:ss');
            

            var duration = calculaPeriodo(period_moment);
            
            var parts = [];
            // return nothing when the duration is falsy or not correctly parsed (P0D)
    
            //if(!duration || duration.toISOString() === 'P0D') return;
            
            // console.log('duration.years', duration.years());
            // console.log('duration.months', duration.months());
            // console.log('duration.days', duration.days());
            // console.log('duration.hours', duration.hours());
            // console.log('duration.minutes', duration.minutes());
            // console.log('duration.seconds', duration.seconds());
    
            var apresenta_ano = duration.years() >= 1;

            var apresenta_mes = duration.months() >= 1 && !apresenta_ano;

            var apresenta_dia = duration.days() >= 1 && !apresenta_mes && !apresenta_ano;

            var apresenta_hora = duration.hours() >= 1 && !apresenta_dia && !apresenta_mes && !apresenta_ano;

            var apresenta_minuto = duration.minutes() >= 1 && !apresenta_hora && !apresenta_dia && !apresenta_mes && !apresenta_ano;

            var apresenta_segundo = duration.seconds() >= 1 && !apresenta_minuto && !apresenta_hora && !apresenta_dia && !apresenta_mes && !apresenta_ano;
    
            if(apresenta_ano) {
                var years = Math.floor(duration.years());
                parts.push(years+' '+(years > 1 ? 'anos' : 'ano'));
            }
    
            if(apresenta_mes) {
                var months = Math.floor(duration.months());
                parts.push(months+' '+(months > 1 ? 'meses' : 'mês'));
            }
    
            if(apresenta_dia) {
                var days = Math.floor(duration.days());
                parts.push(days+' '+(days > 1 ? 'dias' : 'dia'));
            }
            
            if(apresenta_hora) {
                var hours = Math.floor(duration.hours());
                parts.push(hours+' '+(hours > 1 ? 'horas' : 'hora'));
            }
            
            if(apresenta_minuto) {
                var minutes = Math.floor(duration.minutes());
                parts.push(minutes+' '+(minutes > 1 ? 'minutos' : 'minuto'));
            }
    
            if(apresenta_segundo) {
                var seconds = Math.floor(duration.seconds());
                parts.push(seconds+' '+(seconds > 1 ? 'segundos' : 'segundo'));
            }
            
            if(!parts.join(', ')){
                return '';
            }
    
            return message + parts.join(', ');
        }
    
        // console.info(formataDataHoraHumanizada('08/08/2000 12:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('08/08/2005 05:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('08/08/2010 14:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('13/12/2019 09:13:20', 'Atualizado à '));
        
        // console.log(formataDataHoraHumanizada('08/08/2008 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('08/08/2015 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('08/08/2020 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('01/09/2020 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('10/10/2020 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('15/11/2020 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('01/12/2020 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('10/12/2020 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('15/12/2020 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('25/12/2020 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('28/12/2020 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('29/12/2020 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('30/12/2020 00:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('30/12/2020 07:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('30/12/2020 04:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('30/12/2020 02:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('30/12/2020 14:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('30/12/2020 14:30:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('30/12/2020 15:00:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('30/12/2020 16:20:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('30/12/2020 17:39:00', 'Atualizado à '));
        // console.log(formataDataHoraHumanizada('30/12/2020 17:38:00', 'Atualizado à '));

        




        // --- formularios dinamicos
        // ---- Campos Select2 Codigo, Cnpj, Razao Social e Nome Fantasia
        // - Todos os campos tem como 'value' o Código do registro, se precisar recuperar o text do option utilize campos input hidden

        // Localiza o campo de seleção para códigos
        var _field_codigo = $('select.codigo-key'); 
        var _field_codigo_documento = $('select.codigo-documento-key'); 
        var _field_nome_fantasia = $('input.nome-fantasia-key'); 
        var _field_razao_social = $('select.razao-social-key'); 

        
        // console.log(_field_codigo.data('id'));          // ex. CodigoCredenciado
        // console.log(_field_codigo.data('service'));     // ex. fornecedores/obter_credenciado
        // console.log(_field_codigo.attr('id'));          // ex. NotaFiscalServicoCodigoCredenciado
        // console.log(_field_codigo.attr('name'));        // ex. data[NotaFiscalServico][codigo_credenciado]
        

        var _id_codigo = _field_codigo.attr('id');
        var _id_codigoControlGroup = _field_codigo.attr('id')+'ControlGroup';
        var _service_url_codigo = _field_codigo.data('service');

        var _id_documento = _field_codigo_documento.attr('id');
        var _id_razao_social = _field_razao_social.attr('id');
        var _id_nome_fantasia = _field_nome_fantasia.attr('id');


        var _field_codigo = $('#'+_id_codigo);                 // Cod Credenciado
        var _field_codigo_documento = $('#'+_id_documento);    // CNPJ
        var _field_nome_fantasia = $('#'+_id_nome_fantasia);   // Nome fantasia
        var _field_razao_social = $('#'+_id_razao_social);     // Razao Social
        
        var service_url = baseUrl + _service_url_codigo;
        var service_type = 'GET';
        var service_delay = 250;
        var service_data_type = 'json';
        var service_cache = true;

        var _message_data_humanizada = 'Atualizado à ';

        hack_menu_show_style = 'width: 440px';
        hack_menu_show_timeout = 300; // tempo de carregamento do hack css

        var select2ConfigCodigo = {
            ajax: {
                url: service_url,
                dataType: service_data_type,
                type: service_type,
                delay: service_delay,
                cache: service_cache,
                data: function (params) 
                {
                    var query = {
                        codigo_credenciado: params.term,  // <-- TODO: codigo_credenciado precisa ser dinamico
                        page: params.page || 1
                    }
                    
                    return query;
                },
                processResults: function (data, params) 
                {
                    params.page = params.page || 1;
                    
                    if (typeof(data.data) != 'undefined' && data.data !== null && data.data !== '')
                    {
                        setTimeout(function(){ 
                            // hack para aumentar pois select2 não tem props pra isso
                            var dropdownOpen = $(document).find('span.select2-dropdown.select2-dropdown--below');
                            dropdownOpen.attr('style', hack_menu_show_style);
    
                        }, hack_menu_show_timeout);
                    
                        return {
                            results: $.map(data.data, function (val, item) {

                                var data_alteracao_humanizada = formataDataHoraHumanizada(val.data_alteracao, _message_data_humanizada);

                                return { 
                                    id: parseInt(val.codigo), 
                                    text: val.codigo,
                                    cnpj: val.codigo_documento, 
                                    razao_social: val.razao_social, 
                                    nome_fantasia: val.nome, 
                                    codigo_credenciado: val.codigo, 
                                    data_alteracao: data_alteracao_humanizada,
                                    ativo: val.ativo
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    }
                    
                    return {
                        results: []
                    };
                }
                
            },
            minimumInputLength: 2,
            maximumInputLength: 4,
            templateResult: formatList,
            templateSelection: formatListSelectionCodigo,
            language: languageSelect2
        };

        var select2ConfigCodigoDocumento = {
            ajax: {
                url: service_url,
                dataType: service_data_type,
                type: service_type,
                delay: service_delay,
                cache: service_cache,
                data: function (params) 
                {
                    var query = {
                        codigo_documento: params.term,
                        page: params.page || 1
                    }
                    
                    return query;
                },
                processResults: function (data, params) 
                {
                    params.page = params.page || 1;
                    
                    if (typeof(data.data) != 'undefined' && data.data !== null && data.data !== '')
                    {
                        setTimeout(function(){ 
                            // hack para aumentar pois select2 não tem props pra isso
                            var dropdownOpen = $(document).find('span.select2-dropdown.select2-dropdown--below');
                            dropdownOpen.attr('style', hack_menu_show_style);
    
                        }, hack_menu_show_timeout);
                    
                        return {
                            results: $.map(data.data, function (val, item) {
                                var data_alteracao_humanizada = formataDataHoraHumanizada(val.data_alteracao, _message_data_humanizada);
                                return { 
                                    id: parseInt(val.codigo), 
                                    text: val.codigo_documento,
                                    cnpj: val.codigo_documento, 
                                    razao_social: val.razao_social, 
                                    nome_fantasia: val.nome, 
                                    codigo_credenciado: val.codigo, 
                                    data_alteracao: data_alteracao_humanizada,
                                    ativo: val.ativo
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    }
                    
                    return {
                        results: []
                    };
                }
                
            },
            minimumInputLength: 3,
            maximumInputLength: 7,
            templateResult: formatList,
            templateSelection: formatListSelectionDocumento,
            language: languageSelect2
        };

        var select2ConfigRazaoSocial = {
            ajax: {
                url: service_url,
                dataType: service_data_type,
                type: service_type,
                delay: service_delay,
                cache: service_cache,
                data: function (params) 
                {
                    var query = {
                        razao_social: params.term,
                        page: params.page || 1
                    }
                    
                    return query;
                },
                processResults: function (data, params) 
                {
                    params.page = params.page || 1;
                    
                    if (typeof(data.data) != 'undefined' && data.data !== null && data.data !== '')
                    {
                        setTimeout(function(){ 
                            // hack para aumentar pois select2 não tem props pra isso
                            var dropdownOpen = $(document).find('span.select2-dropdown.select2-dropdown--below');
                            dropdownOpen.attr('style', hack_menu_show_style);
    
                        }, hack_menu_show_timeout);
                    
                        return {
                            results: $.map(data.data, function (val, item) {
                                var data_alteracao_humanizada = formataDataHoraHumanizada(val.data_alteracao, _message_data_humanizada);
                                return { 
                                    id: parseInt(val.codigo), 
                                    text: val.razao_social,
                                    cnpj: val.codigo_documento, 
                                    razao_social: val.razao_social, 
                                    nome_fantasia: val.nome, 
                                    codigo_credenciado: val.codigo,
                                    data_alteracao: data_alteracao_humanizada, 
                                    ativo: val.ativo
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    }
                    
                    return {
                        results: []
                    };
                }
                
            },
            minimumInputLength: 4,
            maximumInputLength: 7,
            templateResult: formatList,
            templateSelection: formatListSelectionDocumento,
            language: languageSelect2
        };



        // CODIGO
        _field_codigo.select2(select2ConfigCodigo);
        
        // listeners para recuperar eventos do componente
        _field_codigo.on('select2:select', function (e) {
            var data = e.params.data;
            var id_componente = $(this).attr('id');
            if(typeof(select2CallbackOnChange) !='function'){
                select2CallbackOnChange = null;
            }
            select2OnChangeValues(data, id_componente, select2CallbackOnChange);
        });

        // _field_codigo.on('select2:open', function (e) {
        //    // var data = e.params.data;

        //    var data = {
        //         id: 1,
        //         text: 'Barn owl'
        //     };

        //     var newOption = new Option(data.text, data.id, false, false);
        //     _field_codigo.append(newOption).trigger('change');
        // });

        // DOCUMENTO
        _field_codigo_documento.select2(select2ConfigCodigoDocumento);

        _field_codigo_documento.on('select2:select', function (e) {
            var data = e.params.data;
            var id_componente = $(this).attr('id');
            if(typeof(select2CallbackOnChange) !='function'){
                select2CallbackOnChange = null;
            }
            select2OnChangeValues(data, id_componente, select2CallbackOnChange);
        });
        
        // RAZAO SOCIAL
        _field_razao_social.select2(select2ConfigRazaoSocial);

        _field_razao_social.on('select2:select', function (e) {
            var data = e.params.data;
            var id_componente = $(this).attr('id');
            if(typeof(select2CallbackOnChange) !='function'){
                select2CallbackOnChange = null;
            }
            select2OnChangeValues(data, id_componente, select2CallbackOnChange);
        });


        function select2OnChangeValues(data, id_componente, select2CallbackOnChange = null)
        {

            var data_ativo = data.ativo;
            // if(data_ativo == 0){
            //     data = {codigo_credenciado: '', cnpj: '', razao_social: '', nome_fantasia: ''};
            // }

            var newOptionCodigoFornecedor = new Option(data.codigo_credenciado, data.codigo_credenciado, true, true);
            var newOptionCodigoDocumento  = new Option(data.cnpj, data.codigo_credenciado, true, true);
            var newOptionRazaoSocial      = new Option(data.razao_social, data.codigo_credenciado, true, true);
            var newOptionNomeFantasia     = new Option(data.nome_fantasia, data.codigo_credenciado, true, true);

            if(id_componente == _id_codigo){
                _field_codigo.append(newOptionCodigoFornecedor).trigger('change');
                _field_codigo_documento.append(newOptionCodigoDocumento).trigger('change');
                _field_razao_social.append(newOptionRazaoSocial).trigger('change');
                _field_nome_fantasia.val(data.nome_fantasia);
            }

            if(id_componente == _id_documento){
                _field_codigo.append(newOptionCodigoFornecedor).trigger('change');
                _field_razao_social.append(newOptionRazaoSocial).trigger('change');
                _field_nome_fantasia.val(data.nome_fantasia);
            }

            if(id_componente == _id_razao_social){
                _field_codigo.append(newOptionCodigoFornecedor).trigger('change');
                _field_codigo_documento.append(newOptionCodigoDocumento).trigger('change');
                _field_nome_fantasia.val(data.nome_fantasia);
            }

            // if(data_ativo == 0){
            //     return false;
            // }

            if(typeof(select2CallbackOnChange) =='function'){
                select2CallbackOnChange(data);
            }
        }

    });

    function formatList (list) 
    {
        if (list.loading) {
            return 'Carregando...';
        }

        setTimeout(function(){ 
            var dropdownOpen = $(document).find('span.select2-dropdown.select2-dropdown--below');
                dropdownOpen.attr('style', hack_menu_show_style);
        }, hack_menu_show_timeout);

        var body_color = '';
        var id_color = 'info';

        if(list.ativo == 0){
            id_color = 'important';
        }


        var template = '<div style=\"width:405px;border-bottom:1px solid #ccc\">';

        template = template + '<div class=\"clearfix\" style=\"width:100%;margin-bottom:3px;\">';
        
        template = template + '<span class=\"pull-left\" style=\"margin-top:3px;margin-left:15px;color: #333;\"><span class=\"label label-'+id_color+'\">'+list.id+'</span></span>';
        if(list.data_alteracao){
            template = template + '<span  class=\"pull-right\" style=\"margin-right:30px;margin-top:3px;\"><small>'+list.data_alteracao+'</small></span>';
        }
        template = template + '</div>';

        template = template + '<div class=\"clearfix\" style=\"width:100%;\">';
        template = template + '<span  class=\"pull-left\" style=\"margin-left:10px;margin-right:15px;margin-top:3px;font-weight:bold;\">'+list.razao_social+'</span>';
        template = template + '</div>';
            
        template = template + '<div class=\"clearfix\" style=\"width:100%;\">';
        template = template + '<span class=\"pull-left muted\" style=\"margin-top:2px;margin-left:10px;word-spacing: 1px;\">'+list.cnpj+'</span>';
        template = template + '<span class=\"pull-right\" style=\"margin-right:15px;margin-bottom:3px;\"></span>';
        template = template + '</div>';

        template = template + '<div class=\"clearfix\" style=\"width:100%;\">';
        template = template + '<span  class=\"pull-left muted\" style=\"font-size:9px;margin-left:10px;margin-right:15px;margin-top:3px;\">'+list.nome_fantasia+'</span>';
        template = template + '</div>';

        template = template + '</div>';

        var templateObj = $(template);


        return templateObj;
    }



    function formatListSelectionCodigo (registro) 
    {
        if(registro.ativo == 0){ return ''; }
        return registro.id;
    }

    function formatListSelectionDocumento (registro) {
        if(registro.ativo == 0){ return ''; }
        return registro.text || registro.id;
    }
    
    function formatListSelectionRazaoSocial (registro) {
        if(registro.ativo == 0){ return ''; }
        return registro.text;
    }
