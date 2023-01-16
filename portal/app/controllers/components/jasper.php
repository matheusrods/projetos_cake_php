<?php

class JasperComponent extends Object {
    
    public $name = 'Jasper';

    public $components = array('FileServer');

    protected $webservice = null;

    private $_options = array(
        'REPORT_NAME' => null
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
    
    /**
     * Método de geração centralizada
     * Geração centralizada dos relatórios
     *
     * @param array $parametros
     * @param array $opcoes
     * @return void
     */
	public function generate( $parametros = array(), $opcoes = array() )
	{
        $this->autoRender = false;
        
        $parametros = $this->avaliarParametros($parametros);
        
        try {

            require_once APP . 'vendors' . DS . 'buonny' . DS . 'RelatorioWebService.php';

            $RelatorioWebService = new RelatorioWebService();
    
            $url = $RelatorioWebService->executarRelatorio( $opcoes['REPORT_NAME'], (array)$parametros );
            
            return $url;        
        } catch (Exception $th) {
            return null;
        }

		die;
    }

    /**
     * Avaliação de parâmetros em relação a parametros comuns a todos os relatórios
     *
     * @param array $parametros
     * @return void
     */
    private function avaliarParametros($parametros = array()){
        return $parametros;
    }

}