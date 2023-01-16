<?php
class ApiDataFormatComponent {
	
    var $name = 'ApiDataFormat';
	    
    /**
     * @var string $contentType
     */
    public $contentType = '';

    /**
     * @var object $data
     */
    private $data = null;

    /**
     * @param object $data
     */
    public function setData($data) {
        $this->data = $data;
    }
    
    /**
     * @return string $contentType
     */
    public function getContentType() {
        return $this->contentType;
    }

    /**
     * [setContentType: Método para setar qual tipo de content type da api está sendo passado]
     * 
     */
    public function setContentType() 
    {
        //recupera o content type
        $ct = $_SERVER["CONTENT_TYPE"];
        //seta como padrão o json de content type
        $this->contentType = 'json';
        //verifica se é json o tipo
        if (strpos($ct, "application/json") !== false) {
            $this->contentType = 'json';
        } 
        else if (strpos($ct, "application/xml") !== false) { //verifica se é xml o tipo do content
            $this->contentType = 'xml';
        } //fim verificação do content

    }//fim setContentType

	function initialize(&$controller, $settings = array()) {
		// saving the controller reference for later use        
		$this->controller =& $controller;
    }    
   
    /**
     * Retorna os dados recebidos via Post ou Json de forma padronizada, 
     * independente do tipo de conteúdo
     * @return Object 
     */
    public function getDataRequest() {
        if (strpos($this->contentType, "json") !== false) {                        
            return json_decode($this->data);
        } else if (strpos($this->contentType, "xml") !== false) {
            return json_decode(json_encode(simplexml_load_string($this->data)));
        } else if (strpos($this->contentType, "application/x-www-form-urlencoded") !== false) {
            $this->contentType = 'json';
            parse_str($this->data, $post);
            return (object) $post;
        } else if (strpos($this->contentType, "multipart/form-data") !== false) {
            die('Metodo de envio invalido');
        }
    }


}