
<?php

$campo_upload_texto = (isset($options['campo_upload']) && isset($options['campo_upload']['texto'])) ? $options['campo_upload']['texto'] : 'Escolher Arquivo';
$model_field = (isset($options['campo_upload']) && isset($options['campo_upload']['model_field'])) ? $options['campo_upload']['model_field'] : 'Upload.caminho_arquivo';
$id_field = (isset($options['campo_upload']) && isset($options['campo_upload']['nome'])) ? $options['campo_upload']['nome'] : 'file_1';
$nome_field = (isset($options['campo_upload']) && isset($options['campo_upload']['id'])) ? $options['campo_upload']['id'] : 'file_1';

$titulo_pagina = (isset($options['pagina']) && isset($options['pagina']['titulo'])) ? $options['pagina']['titulo'] : 'Upload de arquivo';

$service = (isset($options['js']) && isset($options['js']['service'])) ? $options['js']['service'] : 'UploadService';
$url = (isset($options['js']) && isset($options['js']['url'])) ? $options['js']['url'] : '';
$codigo = (isset($options['js']) && isset($options['js']['codigo'])) ? $options['js']['codigo'] : '1';

$id_container = (isset($options['js']) && isset($options['js']['id_container'])) ? $options['js']['id_container'] : 'upload-field-imagem';

?>

<div class="row">
	<div class="span4">
        
        <h3><?php echo $titulo_pagina; ?></h3>

		<div class="row">
            
			<div id="upload-campo" class="span3">

                <div class="btn-group">
                    <button id="upload-btn" class="btn"><?php echo $campo_upload_texto;?></button>
                    <button id="remove-btn" class="btn">Remover</button>
                </div>

				<?php echo $this->BForm->input($model_field, array(
                    'id' => $id_field,
                    'type' => 'file', 
                    'class' => 'upload-field input-xlarge', 
                    'label' => '',
                    'style' => 'visibility:hidden;'
                )); ?>

				<i>Dimensões recomendadas '100px' por '40px'</i>
                <p style="color:red;font-style: italic;font-size: 11px;">
                    As imagens são limitadas às extensões png, jpg e jpeg. O tamanho limite é de 2MB. 
                </p>

            </div>

		</div>
    </div>
    <div class="span8">
        <div class="row" style="padding-top:10%;">
            
            <div id="<?php echo $id_container?>"></div>

            <div id="upload-mensagem"></div>

            <div id="carregando-logo" class="span2" style="display:none; text-align: center;padding:5px; ">
                    <img src="/portal/img/ajax-loader.gif"/>
            </div>

        </div>
    </div>
</div>

<?php 
    // $this->addScript($this->Buonny->link_js('services/services.js')); 
    // $this->addScript($this->Buonny->link_js("services/{$service}.js")); 
    // $this->addScript($this->Buonny->link_js('upload_dinamico.js')); 

echo $this->Javascript->codeBlock("
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
        var enctype = options.enctype || 'multipart/form-data';
        var timeout = options.timeout || 600000; // definir um tempo limite (opcional)
        var processData = options.processData || false; // impedir que o jQuery tranforma a 'data' em querystring
        var contentType = options.contentType || false; // desabilitar o cabeçalho 'Content-Type'

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


    // upload_dinamico.js
        
    (function ( $ ) {
        
        $.fn.UploadDinamico = function(options) {
            var _this = this;
            var service = options.service || UploadService;
            var container_id = options.container_id || 'upload-container';
            var container = $('#'+container_id);
            var file_url = options.file_url || false;
            
            var botao_upload = $('#upload-btn');
            var botao_remove = $('#remove-btn');
            
            botao_remove.hide(); // ocultar botão de remover por padrão

            var mensagem_carregando = $('#carregando-logo');
            var mensagem_upload = $('#upload-mensagem');

            mensagem_carregando.hide(); // ocultar mensagem carregando por padrao
            mensagem_upload.hide();

            if( container.length == 0){
                console.error('Container não definido.');
            }
            
            var file_url = options.file_url;
            var codigo_fk = options.codigo_fk || null;

            if(typeof service != 'object'){
                console.error('Serviço não encontrado ou não definido.');
                return _this;
            }

            // imprime a imagem conforme recebe um endereço src
            var carregarImagem = function(url){
                container.html('<img src=\"'+url+'\" />');
                botao_remove.show();
                mensagem('', false);
            };

            var mensagem = function(texto, mostrar){
                mensagem_upload.html(texto);
                if(mostrar){
                    mensagem_upload.show();
                } else {
                    mensagem_upload.hide();
                }
            };

            // inicializa o plugin carregando imagem 
            var initialize = function(){
                
                if(file_url){
                    carregarImagem(file_url);
                    return this;
                }
                
                var consulta = service.obterCaminhoImagem({
                    codigo_cliente: codigo_fk, 
                    animeInput: false
                });

                mensagem_carregando.show();
                mensagem('', false);

                consulta.then(function(dados){
                    mensagem_carregando.hide();

                    if(dados.error){
                        mensagem(dados.error);
                        
                        mensagem_upload.show();
                        console.error(dados.error);
                        return _this;
                    }
                    
                    //verifica se input file esta vazio
                    if(!dados.data.url){
                        mensagem('Imagem não encontrada',true);
                        mensagem_upload.show();
                        //container.html('<p style=\"margin:30px;padding:5px; margin-top:90px;\">Imagem não encontrada.</p>');
                    } else {
                        carregarImagem(dados.data.url);
                    }
                    
                });


            };

            var botaoUpload = function(){
            
            botao_upload.on('click', function(e){
                    e.preventDefault();
            
                    swal({
                            type: 'warning',
                            title: 'Atenção',
                            text: 'Tem certeza que deseja alterar o logotipo? Isto pode influenciar nos relatórios.',
                            showCancelButton: true,
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Alterar',
                            showLoaderOnConfirm: true
                        },
                        function(isConfirm) {
                            
                            if (isConfirm) {
            
                                //setTimeout(function() {
                                    _this.trigger('click');
                                //}, 1000);
            
                            } else {
                                return false;
                            }
                        }
                    );        
        
                });
            };

            var botaoUploadRemove = function(){
                console.log('botaoUploadRemove');
                botao_remove.on('click', function(e) {
                    e.preventDefault();
                    
                    var url = '/portal/clientes/logotipo/'+codigo_fk;

                    $.ajax({
                        type: 'DELETE',
                        url: url,
                        dataType: 'json',
                        cache: false, // desabilitar cache
                        enctype: 'multipart/form-data',
                        timeout: 600000, // definir um tempo limite (opcional)
                        processData: false, // impedir que o jQuery tranforma a 'data' em querystring
                        contentType: false, // desabilitar o cabeçalho 'Content-Type'
                        beforeSend: function(){
                            mensagem_carregando.show();
                        },
                        success: function(response){ 

                            if(response.data){
            
                                container.html('');
                                container.hide;

                                mensagem(response.data.message, true);
                                botao_remove.hide();

                                return swal({
                                    type: 'success',
                                    title: 'Sucesso',
                                    text: response.data.message
                                });
                                
                            } else {
            
                                swal({
                                    type: 'danger',
                                    title: 'Error',
                                    text: 'Não foi possível remover a imagem'
                                });
                            }
                            
                        },
                        complete: function(data){
                            mensagem_carregando.hide();
                        },error: function(error){
                            console.log('error', error.status, error.statusText);
                        }
                
                    });          
                });            
            };

            var botaoUploadChange = function(){
                // $('#file_1'), $('#upload-imagem'), '/portal/clientes/logotipo/'.{$upload['codigo_cliente']}
                _this.on('change', function(e) {
            
                    e.preventDefault();
            
                    var file_data = _this.prop('files')[0];   
                    var form_data = new FormData();                  
                    form_data.append('file', file_data);
                    
                    var url = '/portal/clientes/logotipo/'+codigo_fk;
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: form_data,
                        dataType: 'json',
                        cache: false,
                        enctype: 'multipart/form-data',
                        processData: false,
                        contentType: false,
                        beforeSend: function(){
                            mensagem('', false);
                            mensagem_carregando.show();
                            
                        },
                        success: function(response){ 
                            
                            console.log(response);
            
                            if(typeof response.error != 'undefined'){

                                swal({
                                    type: 'danger',
                                    title: 'Error',
                                    text: 'Não foi possível atualizar a imagem'
                                });

                                console.log(response.error);
                            }

                            if(typeof response.data.path != 'undefined'){
                                carregarImagem(response.data.url);
                            }
                           
                        },
                        complete: function(data){
                            mensagem_carregando.hide();
                            container.show();
                        },error: function(error){
            
                            console.log(error.status, error.statusText);
                        }
                
                    });  
                
                });
            };

            initialize(); 
            botaoUpload(); // ação do botao pra fazer upload
            botaoUploadRemove(); // ação do botao pra remover imagem
            botaoUploadChange(); // ação do input file pra alterar arquivo

            return this;
        };
    
    }( jQuery ));


    
    jQuery(document).ready(function() {
        
        // USANDO UPLOAD 
        // parametros para utilizar upload em ajax
        var options = {
            file_url: '{$url}',
            file_name_field: '{$nome_field}',
            file_id_field: '{$id_field}',
            container_id: '{$id_container}',
            service: {$service},
            codigo_fk: '{$codigo}',
        };

        $( '#{$id_field}' ).UploadDinamico( options ); // carregar componente Upload

    });
");
