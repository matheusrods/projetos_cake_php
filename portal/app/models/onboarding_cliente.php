<?php
class OnboardingCliente extends AppModel {

    var $name = 'OnboardingCliente';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'onboarding_cliente';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'fk_onboarding_cliente_codigo_onboarding'));
    

    function carregar($codigo) {
        $dados = $this->find ( 'first', array (
                'conditions' => array (
                        $this->name . '.codigo' => $codigo 
                ) 
        ) );
        return $dados;
    }

    function incluir($dados){

        if (!parent::incluir($dados)){
            return false;
        }
        else{
            return true;
        }
    }
    
    function atualizar($dados){

        if (!parent::atualizar($dados)){
            return false;
        }
        else{
            return true;
        }
    }

    function excluir($codigo) {
        return $this->delete($codigo);
    }

    function obterListaPorSistema($codigo_sistema, $codigo_cliente ) {
        return $this->find('all', array(
            'joins' => array(
                array(
                    "table" => "onboarding",
                    "alias" => "Onboarding",
                    "type" => "INNER",
                    "conditions" => array(
                        "OnboardingCliente.codigo_onboarding = Onboarding.codigo",
                        // 'OnboardingCliente.ativo' => 1, 
                        'Onboarding.codigo_sistema' => $codigo_sistema,
                        'OnboardingCliente.codigo_cliente' => $codigo_cliente
        
                    )
                ),
            )
        ));
    }


    function avaliarListaPorCliente($codigo_sistema, $codigo_cliente ) {
        
        $lista = array();
        // obter codigo_cliente da Matriz
        $GupoEconomicoTable =& ClassRegistry::init('GrupoEconomico');
        $codigo_cliente_matriz = $GupoEconomicoTable->codigoMatrizPeloCodigoFilial($codigo_cliente);

        $OnboardingTable =& ClassRegistry::init('Onboarding');
        $listaPadrao = $OnboardingTable->obterListaPorSistema($codigo_sistema);

        if(!empty($listaPadrao)){
            if(is_array($listaPadrao) && count($listaPadrao) > 0){
                foreach ($listaPadrao as $keylista => $valuelista) {
                    if(isset($valuelista['Onboarding']['codigo'])) {
                        $listaPush = array(
                            'codigo' => $valuelista['Onboarding']['codigo'],
                            'titulo' => $valuelista['Onboarding']['titulo'],
                            'texto' => $valuelista['Onboarding']['texto'],
                            'imagem' => $valuelista['Onboarding']['imagem'],
                            'ativo' => $valuelista['Onboarding']['ativo']
                        );
                        array_push($lista, $listaPush);
                    }
                }
            }

            // abortar se não tem padrao definido
            if(!is_array($lista) || count($lista) == 0){
                throw new Exception("Ocorreu um erro: Onboarding lista padrão não definida");
            }
        }


        // verificar se cliente ja possui relacao de onboarding
        $onboardingCliente = $this->obterListaPorSistema($codigo_sistema, $codigo_cliente_matriz);

        // não tem registros para este cliente
        if(!is_array($onboardingCliente) || count($onboardingCliente) == 0){
            // cadastrar padrões para o cliente
            try {
                $this->query('begin transaction');

                foreach ($lista as $lkey => $lvalue) {
                    $lvalue['codigo_onboarding'] = $lvalue['codigo'];
                    unset($lvalue['codigo']); // remove para criar seu proprio relacionamento
                    $lvalue['codigo_cliente'] = $codigo_cliente_matriz;
                    if(!$this->incluir($lvalue)) {
                        throw new Exception();
                    }
                }

                $this->commit();
                return $lista;
            } catch (Exception $ex) {
                $this->rollback();
                return false;
            } 
        } else {

            $lista = array();

            if(is_array($onboardingCliente) && count($onboardingCliente) > 0){
                foreach ($onboardingCliente as $keylista => $valuelista) {
                    if(isset($valuelista['OnboardingCliente']['codigo'])) {
                        $listaPush = array(
                            'codigo' => $valuelista['OnboardingCliente']['codigo'],
                            'titulo' => $valuelista['OnboardingCliente']['titulo'],
                            'texto' => $valuelista['OnboardingCliente']['texto'],
                            'imagem' => $valuelista['OnboardingCliente']['imagem'],
                            'ativo' => $valuelista['OnboardingCliente']['ativo']
                        );
                        array_push($lista, $listaPush);
                    }
                }
            }
        }

        return $lista;
    }


    function atualizaConfiguracao( $dados, $codigo_cliente, $codigo_sistema = 3 ) {
                
        if(empty($codigo_cliente) || empty($dados)){
            return false;
        }

        // obter codigo_cliente da Matriz
        $GupoEconomicoTable =& ClassRegistry::init('GrupoEconomico');
        $codigo_cliente_matriz = $GupoEconomicoTable->codigoMatrizPeloCodigoFilial($codigo_cliente);
        
        $queryArray = array();
        
        if(is_array($dados)){
            
            foreach ($dados as $key => $value) {
                
                if(isset($value['titulo'])){
                    $conditionsArray[] = "titulo = '" . $value['titulo']."'";
                }
                if(isset($value['texto'])){
                    $conditionsArray[] = "texto = '" . $value['texto']."'";
                }
                if(isset($value['ativo'])){
                    $conditionsArray[] = "ativo = " . $value['ativo'];
                }
                if(isset($value['imagem'])){
                    $conditionsArray[] = "imagem = '" . $value['imagem']."'";
                }
                
                $query = "UPDATE RHHealth.dbo.onboarding_cliente ";
                $query .= "SET ";
                $query .= implode(",", $conditionsArray);
                unset($conditionsArray); // limpa condicionais
                $codigo = $value['codigo'];
				$query .= " WHERE codigo = " . $codigo . " AND codigo_cliente = " . $codigo_cliente_matriz . ";";
                
                
                $queryArray[] = $query;
			    
            }
            
            if(count($queryArray)>0){

                try {
                    //$this->query('begin transaction');
                    foreach ($queryArray as $query) {
                        if (!$this->query($query)){
                            throw new Exception('Ocorreu um erro: OnboardingCliente Atualizando relacionamento com cliente');
                        }
                    }
                    //$this->commit();
                    return true;
                } catch (Exception $ex) {
                    //$this->rollback();
                    return false;
                }
            }
        }
        return false; // não há o que atualizar
    }
}
