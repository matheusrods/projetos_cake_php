<?php

class ConsultaMotorista extends AppModel {

    public $motoristaMonitora    = null;
    public $motoristaTeleConsult = null;
    public $motoristaGuardian    = null;    

    public function __construct(){

        $this->motoristaMonitora    = ClassRegistry::init('Motorista');
        $this->motoristaTeleConsult = ClassRegistry::init('Profissional');
        $this->motoristaGuardian    = ClassRegistry::init('TPfisPessoaFisica');
    }

    public function consultarMotoristaMonitora($cpf){
        $result = $this->motoristaMonitora->find('first',array(
            'conditions' => array("REPLACE(REPLACE(CPF,'.',''),'-','')" => $this->replaceCpf($cpf))
        )); 
        if( !empty($result) )
            return $result;
        else
            return false;
    }

    public function consultarMotoristaTeleConsult($cpf){
        $result = $this->motoristaTeleConsult->find('first',array(
            'conditions' => array("REPLACE(REPLACE(codigo_documento,'.',''),'-','')" => $this->replaceCpf($cpf))
        ));
        if( !empty($result) )
            return $result;
        else
            return false;    
    }

    public function consultarMotoristaGuardian($cpf){
        $TMotoMotorista = ClassRegistry::init('TMotoMotorista');
        $joins = array(
            array(
                'table'      => "{$TMotoMotorista->databaseTable}.{$TMotoMotorista->tableSchema}.{$TMotoMotorista->useTable}",
                'alias'      => 'TMotoMotorista',
                'conditions' => 'TPfisPessoaFisica.pfis_pess_oras_codigo = TMotoMotorista.moto_pfis_pess_oras_codigo',
                'type'       => 'LEFT'
            )
        );
        $result = $this->motoristaGuardian->find('first',array(
            'fields' => array('TPfisPessoaFisica.pfis_pess_oras_codigo','TMotoMotorista.moto_pfis_pess_oras_codigo'),
            'joins' => $joins,
            'conditions' => array("REPLACE(REPLACE(TPfisPessoaFisica.pfis_cpf,'.',''),'-','')" => $this->replaceCpf($cpf))
        ));        
        if( !empty($result) ){
            if(is_null($result['TMotoMotorista']['moto_pfis_pess_oras_codigo']))
                return $result['TPfisPessoaFisica']['pfis_pess_oras_codigo'];
            else
                return true;
        }else{
            return false;
        }
    }

    public function incluirMotoristaMonitora($data){
        if($this->motoristaMonitora->save($data))
            return true;
        else
            return false;
    }

    public function incluirMotoristaTeleConsult($data){
    	
        if($this->motoristaTeleConsult->save($data))
            return true;
        else
            return false;
    }

    public function incluirTOrasObjetoRastreado($data){
       $TOrasObjetoRastreado = ClassRegistry::init('TOrasObjetoRastreado');
       if($TOrasObjetoRastreado->save($data))
            return $TOrasObjetoRastreado->id;
        else
            return false;
    }

    public function incluirTPessPessoa($data){
       $TPessPessoa = ClassRegistry::init('TPessPessoa');
       if($TPessPessoa->save($data))
            return true;
        else
            return false;
    }

    public function incluirTPfisPessoaFisica($data){
       $TPfisPessoaFisica = ClassRegistry::init('TPfisPessoaFisica');
       if($TPfisPessoaFisica->save($data))
            return true;
        else
            return false;
    }

    public function incluirTMotoMotorista($data){
       $TMotoMotorista = ClassRegistry::init('TMotoMotorista');
       if($TMotoMotorista->save($data))
            return true;
        else
            return false;
    }

    public function estruturarDadosDeInclusaoGuardian($data){
        if(isset($data['Profissional'])){
            $pess_nome = $data['Profissional']['nome'];
            $pfis_rg   = $data['Profissional']['rg'];
            $pfis_cpf  = $data['Profissional']['codigo_documento'];
        }else{
            $pess_nome = $data['Motorista']['Nome'];
            $pfis_rg   = $data['Motorista']['RG'];
            $pfis_cpf  = $data['Motorista']['CPF'];
        }
        $dados = array(
            array(
                'TOrasObjetoRastreado' => array(                                 
                    'oras_data_cadastro' => date('Y-m-d H:i:s'),                  
                    'oras_usuario_alterou' => 'Serviço Cadastro de Motorista',  
                ),
            ),
            array(
                'TPessPessoa' => array(                
                    'pess_oras_codigo' => null,
                    'pess_nome' => $pess_nome,
                    'pess_usuario_adicionou' => 'Serviço Cadastro de Motorista',
                ),
            ),
            array(
                'TPfisPessoaFisica' => array(                
                    'pfis_pess_oras_codigo' => null, 
                    'pfis_rg' => $pfis_rg, 
                    'pfis_cpf' => $pfis_cpf,
                    'pfis_usuario_adicionou' => 'Serviço Cadastro de Motorista',
                ),
            ),
            array(
                'TMotoMotorista' => array(
                    'moto_pfis_pess_oras_codigo' => null,                
                    'moto_usuario_alterou' => 'Serviço Cadastro de Motorista', 
                ),
            )
        );

        return $dados;
    }

    public function incluirMotoristaGuardian($data){
        $dados = $this->estruturarDadosDeInclusaoGuardian($data);
        try{
            $codigo = $this->incluirTOrasObjetoRastreado($dados[0]);
            if($codigo){
                $dados[1]['TPessPessoa']['pess_oras_codigo']              = $codigo;
                $dados[2]['TPfisPessoaFisica']['pfis_pess_oras_codigo']   = $codigo;
                $dados[3]['TMotoMotorista']['moto_pfis_pess_oras_codigo'] = $codigo;
                $this->incluirTPessPessoa($dados[1]['TPessPessoa']);
                $this->incluirTPfisPessoaFisica($dados[2]['TPfisPessoaFisica']);
                $this->incluirTMotoMotorista($dados[3]['TMotoMotorista']);
                return true;
            }
        }catch(Exception $ex){
            return 'Erro ao cadastrar motorista: ' . $ex->getMessage();
        }
    }

    public function estruturarDadosInclusaoMonitoraComDadosTeleConsult($data){
        $Motorista = ClassRegistry::init('Motorista');
        $dados = array(
            'Motorista' => array(
                'Codigo'        => $Motorista->retornaNovoCodigo(),
                'Nome'          => $data['Profissional']['nome'],
                'CNH_Validade'  => $data['Profissional']['cnh_vencimento'],
                'CNH'           => $data['Profissional']['cnh'],
                'RG'            => $data['Profissional']['rg'],
                'CPF'           => $data['Profissional']['codigo_documento'],
            )
        );

        return $dados;
    }

    public function estruturarDadosInclusaoTeleConsultComDadosMonitora($data){
        $dados = array(
            'Profissional' => array(                                
                'codigo_documento'        => $this->replaceCpf($data['Motorista']['CPF']),
                'nome'                    => $data['Motorista']['Nome'],
                'rg'                      => $data['Motorista']['RG'],
                'cnh'                     => $data['Motorista']['CNH'],
                'cnh_vencimento'          => $data['Motorista']['CNH_Validade'],
                'codigo_modulo'           => 1,
                'codigo_usuario_inclusao' => 2,                
            )
        );

        return $dados;
    }

    public function estruturarDadosInclusaoMotoMotorista($codigo){
        $data = array(
            'TMotoMotorista' => array(
                'moto_pfis_pess_oras_codigo' => $codigo,                
                'moto_usuario_alterou' => 'Serviço Cadastro de Motorista', 
            ),
        );

        return $data;
    }

    public function consultaMotorista($cpf){
        if( $this->consultarMotoristaTeleConsult($cpf) && !$this->consultarMotoristaMonitora($cpf) && !$this->consultarMotoristaGuardian($cpf) ){
            $data = $this->consultarMotoristaTeleConsult($cpf);            
            $this->incluirMotoristaMonitora($this->estruturarDadosInclusaoMonitoraComDadosTeleConsult($data));
            $dado = $this->estruturarDadosDeInclusaoGuardian($data);
            $this->incluirMotoristaGuardian($data);
        }elseif( $this->consultarMotoristaTeleConsult($cpf) && !$this->consultarMotoristaMonitora($cpf) && is_numeric($this->consultarMotoristaGuardian($cpf)) ) {
            $data = $this->consultarMotoristaTeleConsult($cpf);            
            $this->incluirMotoristaMonitora($this->estruturarDadosInclusaoMonitoraComDadosTeleConsult($data));
            $codigo = $this->consultarMotoristaGuardian($cpf);
            $this->incluirTMotoMotorista($this->estruturarDadosInclusaoMotoMotorista($codigo));
        }elseif( $this->consultarMotoristaTeleConsult($cpf) && $this->consultarMotoristaMonitora($cpf) && is_numeric($this->consultarMotoristaGuardian($cpf)) ) {
            $codigo = $this->consultarMotoristaGuardian($cpf);
            $this->incluirTMotoMotorista($this->estruturarDadosInclusaoMotoMotorista($codigo));        
        }elseif( $this->consultarMotoristaTeleConsult($cpf) && !$this->consultarMotoristaMonitora($cpf) && $this->consultarMotoristaGuardian($cpf) === true ) {
            $data = $this->consultarMotoristaTeleConsult($cpf);            
            $this->incluirMotoristaMonitora($this->estruturarDadosInclusaoMonitoraComDadosTeleConsult($data));
        }elseif( $this->consultarMotoristaTeleConsult($cpf) && $this->consultarMotoristaMonitora($cpf) && !$this->consultarMotoristaGuardian($cpf) ) {
            $data = $this->consultarMotoristaTeleConsult($cpf);
            $dado = $this->estruturarDadosDeInclusaoGuardian($data);
            $this->incluirMotoristaGuardian($data);
        }elseif( $this->consultarMotoristaMonitora($cpf) && !$this->consultarMotoristaTeleConsult($cpf) && !$this->consultarMotoristaGuardian($cpf) ) {
            $data = $this->consultarMotoristaMonitora($cpf);
            $this->incluirMotoristaTeleConsult($this->estruturarDadosInclusaoTeleConsultComDadosMonitora($data));
            $this->incluirMotoristaGuardian($data);
        }elseif( $this->consultarMotoristaMonitora($cpf) && !$this->consultarMotoristaTeleConsult($cpf) && is_numeric($this->consultarMotoristaGuardian($cpf)) ) {
            $data = $this->consultarMotoristaMonitora($cpf);
            $this->incluirMotoristaTeleConsult($this->estruturarDadosInclusaoTeleConsultComDadosMonitora($data));
            $codigo = $this->consultarMotoristaGuardian($cpf);
            $this->incluirTMotoMotorista($this->estruturarDadosInclusaoMotoMotorista($codigo));
        }elseif( $this->consultarMotoristaMonitora($cpf) && $this->consultarMotoristaTeleConsult($cpf) && is_numeric($this->consultarMotoristaGuardian($cpf)) ) {
            $codigo = $this->consultarMotoristaGuardian($cpf);
            $this->incluirTMotoMotorista($this->estruturarDadosInclusaoMotoMotorista($codigo));
        }elseif( $this->consultarMotoristaMonitora($cpf) && !$this->consultarMotoristaTeleConsult($cpf) && $this->consultarMotoristaGuardian($cpf) === true ) {
            $data = $this->consultarMotoristaMonitora($cpf);
            $this->incluirMotoristaTeleConsult($this->estruturarDadosInclusaoTeleConsultComDadosMonitora($data));
        }elseif( $this->consultarMotoristaMonitora($cpf) && $this->consultarMotoristaTeleConsult($cpf) && !$this->consultarMotoristaGuardian($cpf) ) {
            $data = $this->consultarMotoristaMonitora($cpf);
            $dado = $this->estruturarDadosDeInclusaoGuardian($data);
            $this->incluirMotoristaGuardian($data);
        }
        else{
            return false;
        }
    }

    private function replaceCpf($cpf){
        return str_replace(array('.','-'), '', $cpf);
    }
}       
