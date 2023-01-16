<?php
class UploadComponent extends Object {
    
    public $name = 'Upload';

    public $components = array(
		'FileServer'
    );
    
    var $helpers = array('Form', 'Html', 'Javascript', 'Text');
    
    private $_options = array(
        'field_name'=> 'file',
        'size_max'=> 2200000,
        'size_max_message' => 'Tamanho máximo excedido!',
        'accept_extensions' => array('jpg','png','jpeg'),
        'accept_extensions_message' => 'Arquivo inválido! Favor escolher arquivo jpg, jpeg ou png.'
    );

    public function getOptions(){
        return $this->_options;
    }
    
    public function getOption($option){
        return $this->_options[$option];
	}
	
	public function setOption($option, $value){
		$this->_options[$option] = $value;  
    }

    public function getUrlFileServer( $path = null)
    {
        return $this->FileServer->getUrl( $path );
    }
    
    /**
     * Upload de um ou mais arquivos
     *
     * @param array $fileOrFiles
     * @return array
     */
    public function fileServer( array $fileOrFiles = array() ){

        // nome do campo esperado no upload
        $field_name = $this->getOption('field_name');

        // valida se array upload esta como esperado
        if(!isset($fileOrFiles[$field_name]) || empty($fileOrFiles) || !isset($fileOrFiles[$field_name]['name']) ){
            return array('error'=>'Dados do formulário inválidos');
        }

        // valida se recebido multiplos arquivos
        $multiplos = (is_array($fileOrFiles[$field_name]['name']));

        // se for um arquivo
        if(!$multiplos){
            $file_name = $fileOrFiles[$field_name]['name'];
            $upload = $this->uploadFileServer( $fileOrFiles[$field_name] );

            if(isset($upload['error'])){
                return array('error' => array($file_name => 'Erro de Upload do arquivo ['.$file_name.']. '.$upload['error']));
            }

            return array('data' => array( $file_name => $upload));
        }

        // se for vários arquivos
        // para multiplos arquivo é necessário fazer um tratamento no array $_FILES
        $uploads_data = array();
        $uploads_error = array();
        
        for ($i=0; $i < count($fileOrFiles[$field_name]['name']); $i++) { 
            
            // hidratar os dados de $_FILES
            $file = array(
                'name' => $fileOrFiles[$field_name]['name'][$i],
                'type' => $fileOrFiles[$field_name]['type'][$i],
                'tmp_name' => $fileOrFiles[$field_name]['tmp_name'][$i],
                'error' => $fileOrFiles[$field_name]['error'][$i],
                'size' => $fileOrFiles[$field_name]['size'][$i],
            );

            // definir um nome de retorno
            $file_name = $fileOrFiles[$field_name]['name'][$i];

            // enviar ao file server
            $upload = $this->uploadFileServer( $file );

            // se retornou algum erro
            if(isset($upload['error'])){
                $uploads_error[$file_name] = 'Erro de Upload do arquivo ['. $file['name'].']. '.$upload['error'];
            }

            $uploads_data[$file_name] = $upload;
        }

        $uploads = array();

        // inclua o array de erros no resultado apenas se ocorreu
        if(count($uploads_error) > 0){
            $uploads['error'] = $uploads_error; 
        }

        $uploads['data'] = $uploads_data;

        return $uploads;
	}    

    /**
     * Upload de arquivo para o FileServer
     *
     * @param array $file
     * @return array
     */
    private function uploadFileServer( $file ){

        $accept_extensions = $this->getOption('accept_extensions');

        if(!empty($accept_extensions) && is_array($accept_extensions) && count($accept_extensions) > 0){
            $accept_extensions = implode("|", $accept_extensions);
        }
        
        $accept_extensions_message = $this->getOption('accept_extensions_message');

        if( !preg_match('@\.('.$accept_extensions.')$@i', $file['name']) ) {
			return array('error' => $accept_extensions_message);
        }

        $file_name = $file['name'];

        $size_max = $this->getOption('size_max');
        $size_max_message = $this->getOption('size_max_message');

		if (!empty($size_max) && $file['size'] >= $size_max){
			return array('error' => $size_max_message);
		}

		$array_path_arquivo = explode(DS, $file['tmp_name']);
		array_pop($array_path_arquivo);
		$array_path_arquivo[] = $file['name'];
		$novo_path_arquivo = implode(DS, $array_path_arquivo);

        try {

            if (copy($file['tmp_name'], $novo_path_arquivo)){
			
                $url_imagem = $this->FileServer->send('@'.$novo_path_arquivo);

                if(isset($url_imagem->{'response'}->{'path'})){

                    $response_url = $url_imagem->{'response'}->{'path'};

                    return array(
                        'path' => $response_url,
                        'path_url' => $this->getUrlFileServer( $response_url ),
                        'message'=> 'Upload do arquivo ['.$file_name.'] feito com sucesso.'
                    );
                }

                return array(
                    'error' => 'Path não encontrado para o arquivo ['.$file_name.'].'
                );    

            } else {
                throw new Exception("Não foi possível copiar arquivo.", 1);
            }

        } catch (Exception $e) {
            return array('error'=>'Não foi possível copiar arquivo. Erro: '.$e->getMessage());
        }

    }

}