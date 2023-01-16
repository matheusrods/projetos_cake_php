<?php
class ShellController extends AppController {
    public $name = 'Shell';
    public $uses = array();

    public function beforeFilter() {
        $this->BAuth->allow(array('*'));
        parent::beforeFilter();
    }

    function executar($shell = null,$funcao = null,$parametro = null) {
        if(Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO){
            if(!empty($shell)){
                $desportal = explode('.', $_SERVER['HTTP_HOST']);    
                if (PHP_OS!='WINNT') {
                    exec("sh /home/sistemas/{$desportal[0]}/portal/cake/console/cake -app /home/sistemas/{$desportal[0]}/portal/app $shell $funcao $parametro"); 
                } else {
                    exec("C:\home\sistemas\portal\portal\cake\console\cake -app C:\home\sistemas\portal\portal\app $shell $funcao $parametro"); 
                }
                $this->set(compact('shell','funcao','parametro'));
            }    
        }else{
            exit();
        }           

    }        
}
?>