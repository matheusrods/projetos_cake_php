<?php
class FileServerComponent extends Object {
    
    public $name = 'FileServer';
    
    private $_options = array(
        'url' => 'https://api.rhhealth.com.br',
        'prefix'=>'ithealth'
    );

    // Aguardando testes para implementar este metodo
    // function __construct() 
    // {
    //     // ao inicializar sobrescreve a url de acordo com o ambiente
    //     $url = Ambiente::getUrlServidorFileServer();
    //     $this->setOption('url', $url);
    // }
    
    public function getOptions(){
        return $this->_options;
    }
    
    public function getOption($option){
        return $this->_options[$option];
	}
	
	public function setOption($option, $value){
		$this->_options[$option] = $value;  
	}
	
    public function setOptions($option = array()){

    }

    public function getUrl( $path = null){

        $url = $this->getOption('url');

        if(!empty($path)){
            $url = $url . $path;
        }

        return $url;
    }

    public function getUrlImage( $path = null ){
        return $this->getOption('url').$path;
    }

    public function getImage( $path = null ){
        return $this->getOption('url').$this->getUrlImage();
    }

    public function send( $file ){

        $prefix = $this->getOption('prefix');
        $url = $this->getOption('url');

		$data = array(
			'file'=> $file, 
            'prefix' => $prefix
        );

        try {

            $cURL = curl_init();
            curl_setopt( $cURL, CURLOPT_URL, $url );
            curl_setopt( $cURL, CURLOPT_POST, true );
            curl_setopt( $cURL, CURLOPT_POSTFIELDS, $data);
            curl_setopt( $cURL, CURLOPT_RETURNTRANSFER, true );
            
            $result = curl_exec( $cURL );
			$result = json_decode($result);
			
            curl_close ($cURL);
    
            return $result;

        } catch (\Exception $e) {
            return array('error'=>$e->getMessage());
        }

    }

          
}