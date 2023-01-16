
    (function ( $ ) {
        
        

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
                    return _this;
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
                        mensagem(dados.error, true);
                        console.error(dados.error);
                        return _this;
                    }
                    
                    //verifica se input file esta vazio
                    if(!dados.data.url){
                        mensagem('Imagem não encontrada',true);
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
            
                                setTimeout(function() {
                                    _this.trigger('click');
                                }, 1000);
            
                            } else {
                                return false;
                            }
                        }
                    );        
        
                });
            };

            var botaoUploadRemove = function(){
                
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
                            mensagem('', false);
                        },
                        success: function(response){ 
            
                            if(response.data){
            
                                mensagem('Imagem não foi encontrada', true);

                                botao_remove.hide();
                                container.html('');
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
                            container.hide();
                            mensagem_carregando.show();
                            
                        },
                        success: function(response){ 
                            console.log('botaoUploadChange', response);
            
                            if(response.data.path){
                                carregarImagem(response.data.url);
                            } else {
            
                                swal({
                                    type: 'danger',
                                    title: 'Error',
                                    text: 'Não foi possível atualizar a imagem'
                                });

                                mensagem('Não foi possível atualizar a imagem');        
                            }
                            
                        },
                        complete: function(data){
                            mensagem_carregando.hide();
                            container.show();
                        },error: function(error){
            
                            console.log('error', error.status, error.statusText);
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