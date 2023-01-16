<?php
App::import('Model', 'Uperfil');

class UperfilTestCase extends CakeTestCase {

    var $fixtures = array(
        'app.uperfil',
        'app.tipo_perfil',
        'app.uperfil_tipo_alerta',
        'app.acos',
        'app.aros',
        'app.aros_acos',
        'app.usuario',
        'app.usuario_log', 
        'app.documento',
        'app.objeto_acl',
        'app.dependencia_obj_acl',
        'app.objeto_acl_tipo_perfil',
    );

    function startTest() {
        Configure::write('Acl.database', 'test');
        App::import('Component', 'Acl');
        $this->Acl = new AclComponent();
        $this->Uperfil = & ClassRegistry::init('Uperfil');
        $this->ObjetoAcl = & ClassRegistry::init('ObjetoAcl');
        $_SESSION['Auth']['Usuario']['codigo'] = 1;
    }


    function testTestesSomenteConsulta() {
        $this->moduloInicialPerfilAdmin();
        $this->moduloInicialPerfilFinanceiro();
        $this->listar();
        $this->listaPermissoes();
    }

    private function moduloInicialPerfilAdmin() {
        $expected = array(
          'nome' => 'Sistema',
          'url' => array('controller' => 'Painel', 'action' => 'modulo_admin')
        );
        $codigo_perfil = 1;
        $this->assertEqual($expected, $this->Uperfil->moduloInicial($codigo_perfil) );

        $codigo_perfil = 1;
        $this->assertEqual($expected, $this->Uperfil->moduloInicial($codigo_perfil));
    }


    private function moduloInicialPerfilFinanceiro() {
        $expected = array(
            'nome' => 'Financeiro',
            'url' => array('controller' => 'Painel', 'action' => 'modulo_financeiro')
        );

      $codigo_perfil = 2;
      $this->assertEqual($expected, $this->Uperfil->moduloInicial($codigo_perfil));
    }

    private function listar() {
        $condicao = array( 'data_inclusao BETWEEN ? AND ? ' => array('2012-04-16 00:00:00', '2012-04-16 00:00:00') );
        $QtdEncontrada = $this->Uperfil->listar($condicao);
        $this->assertEqual(count($QtdEncontrada), 10);
        $condicao = array('codigo' => 1);
        $QtdEncontrada = $this->Uperfil->listar($condicao);
        $this->assertEqual(count($QtdEncontrada), 1);
    }

    private function listaPermissoes(){
        $codigo_perfil = 2;
        $esperado = array('Permissao' => array(
            'Painel__modulo_financeiro' => 1,
            )
        );
        $resultado = $this->Uperfil->listaPermissoes($codigo_perfil);
        $this->assertEqual($esperado, $resultado);
    }

    function testIncluir() {
        $dados = array(
            'Uperfil' => array(
                'descricao' => 'Gerente Jurídico',
                'codigo_cliente'=>NULL
            )
        );
        $this->assertTrue($this->Uperfil->incluir($dados));
        $this->assertTrue($this->Acl->Aro->node(array('model' => $this->Uperfil->name, 'foreign_key' => $this->Uperfil->id)));
        $this->assertFalse($this->Uperfil->incluir($dados));
        $erro = $this->Uperfil->invalidFields();
        $erro = $erro['descricao'];
        $this->assertEqual($erro,'Perfil já existente na base');
        $dados = array(
            'Uperfil' => array(
                'descricao' => 'Admin',
                'codigo_cliente'=>NULL
            )
        );
        $this->assertFalse($this->Uperfil->incluir($dados));
    }

    function testAlterar() {
        $dados = array(
            'Uperfil' => array(
                'codigo' => 1,
                'descricao' => 'Teste de Edit',
                'codigo_cliente'=>NULL
            )
        );
        $esperado   = $dados['Uperfil']['descricao'];
        $this->Uperfil->atualizar($dados);
        $encontrado = $this->Uperfil->find('first', array('conditions' => array('codigo' => 1), 'fields' => array('descricao')));
        $this->assertEqual($esperado, $encontrado['Uperfil']['descricao']);
    }
    
    function testCriarAdmin() {
      $this->assertFalse($this->Uperfil->criarAdmin());
    }

    function testCriarAdminNovo() {
        $this->Uperfil->excluir(1);
        $this->assertTrue($this->Uperfil->criarAdmin());
    }
    
    function testGeraPermissao(){
        $codigo_perfil = 2;
        $data = array(
            'Permissao' => array(
                'Painel__modulo_financeiro' => 1,
                'ParametrosBoleto__parametros_para_boleto_bb' => 0,
                'Clientes__enviar_fatura' => 0,
                'Painel__modulo_buonnysat' => 0,
                'Ocorrencias__lista_ocorrências' => 0,
                'EstatisticasSms__geral' => 0,
                'EstatisticasSms__por_cliente' => 0,
                'EstatisticasSms__por_operacao' => 0,
                'EstatisticasSms__por_operador' => 0,
                'OcorrenciasTipos__sla_tipo_ocorrencia' => 0,
                'Ocorrencias__status_sla' => 0,
                'Ocorrencias__lista_ocorrencias_consulta' => 0,
                'Painel__modulo_comercial' => 0,
                'Clientes__index' => 0,
                'Corretoras__index' => 0,
                'Seguradoras__index' => 0,
                'ClientesProdutos__index' => 0,
                'ClientesProdutos__lista_clientes' => 0,
                'ClientesOperacoes__index' => 0,
                'ClientesRelacionamentos__index' => 0,
                'ClientesLog__index' => 0,
                'Clientes__clientes_cadastrados' => 0,
                'Clientes__clientes_cobrador' => 0,
                'Clientes__clientes_data_cadastro' => 0,
                'Painel__modulo_juridico' => 1,
                'ContratosModelos__index' => 0
            )
        );

        $aro = array('model' => 'Uperfil', 'foreign_key' => $codigo_perfil);
        
        $aco = 'buonny';
        $this->assertFalse($this->Acl->check($aro, $aco));

        $aco = 'buonny/Usuarios/inicio';
        $this->assertFalse($this->Acl->check($aro, $aco));

        $aco = 'buonny/Painel/modulo_financeiro';
        $this->assertTrue($this->Acl->check($aro, $aco));

        $aco = 'buonny/Painel/modulo_juridico';
        $this->assertFalse($this->Acl->check($aro, $aco));

        $this->Uperfil->geraPermissao($codigo_perfil, $data);

        $aro = array('model' => 'Uperfil', 'foreign_key' => $codigo_perfil);

        $aco = 'buonny';
        $this->assertFalse($this->Acl->check($aro, $aco));

        $aco = 'buonny/Usuarios/inicio';
        $this->assertTrue($this->Acl->check($aro, $aco));

        $aco = 'buonny/Painel/modulo_financeiro';
        $this->assertTrue($this->Acl->check($aro, $aco));

        $aco = 'buonny/Painel/modulo_juridico';
        $this->assertTrue($this->Acl->check($aro, $aco));
    }
    

    function testGeraPermissaoMudaTudo(){
        $codigo_perfil = 2;
        $data = array('Permissao' => array(
                'Painel__modulo_financeiro' => 0,
                'ParametrosBoleto__parametros_para_boleto_bb' => 0,
                'Clientes__enviar_fatura' => 0,
                'Painel__modulo_buonnysat' => 0,
                'Ocorrencias__lista_ocorrências' => 0,
                'EstatisticasSms__geral' => 0,
                'EstatisticasSms__por_cliente' => 0,
                'EstatisticasSms__por_operacao' => 0,
                'EstatisticasSms__por_operador' => 0,
                'OcorrenciasTipos__sla_tipo_ocorrencia' => 0,
                'Ocorrencias__status_sla' => 0,
                'Ocorrencias__lista_ocorrencias_consulta' => 0,
                'Painel__modulo_comercial' => 0,
                'Clientes__index' => 0,
                'Corretoras__index' => 0,
                'Seguradoras__index' => 0,
                'ClientesProdutos__index' => 0,
                'ClientesProdutos__lista_clientes' => 0,
                'ClientesOperacoes__index' => 0,
                'ClientesRelacionamentos__index' => 0,
                'ClientesLog__index' => 0,
                'Clientes__clientes_cadastrados' => 0,
                'Clientes__clientes_cobrador' => 0,
                'Clientes__clientes_data_cadastro' => 0,
                'Painel__modulo_juridico' => 1,
                'ContratosModelos__index' => 0
            )
        );

        $aro = array('model' => 'Uperfil', 'foreign_key' => $codigo_perfil);        
        $aco = 'buonny';
        $this->assertFalse($this->Acl->check($aro, $aco));
        $aco = 'buonny/Painel/modulo_financeiro';
        $this->assertTrue($this->Acl->check($aro, $aco));
        $aco = 'buonny/Painel/modulo_juridico';
        $this->assertFalse($this->Acl->check($aro, $aco));
        $this->Uperfil->geraPermissao($codigo_perfil, $data);
        $aro = array('model' => 'Uperfil', 'foreign_key' => $codigo_perfil);
        $aco = 'buonny';
        $this->assertFalse($this->Acl->check($aro, $aco));
        $aco = 'buonny/Painel/modulo_financeiro';
        $this->assertFalse($this->Acl->check($aro, $aco));
        $aco = 'buonny/Painel/modulo_juridico';
        $this->assertTrue($this->Acl->check($aro, $aco));
    }

    function testGeraPermissaoEDependencias(){
        $codigo_perfil = 2;
        $data = array(
            'Permissao' => array(
                'Painel__modulo_financeiro' => 0,
                'ParametrosBoleto__parametros_para_boleto_bb' => 0,
                'Clientes__enviar_fatura' => 0,
                'Painel__modulo_buonnysat' => 0,
                'Ocorrencias__lista_ocorrencias' => 0,
                'EstatisticasSms__geral' => 0,
                'EstatisticasSms__por_cliente' => 0,
                'EstatisticasSms__por_operacao' => 0,
                'EstatisticasSms__por_operador' => 0,
                'OcorrenciasTipos__sla_tipo_ocorrencia' => 0,
                'Ocorrencias__status_sla' => 0,
                'Ocorrencias__lista_ocorrencias_consulta' => 0,
                'Painel__modulo_comercial' => 0,
                'Clientes__index' => 0,
                'Corretoras__index' => 1,
                'Seguradoras__index' => 0,
                'ClientesProdutos__index' => 0,
                'ClientesProdutos__lista_clientes' => 0,
                'ClientesOperacoes__index' => 0,
                'ClientesRelacionamentos__index' => 0,
                'ClientesLog__index' => 0,
                'Clientes__clientes_cadastrados' => 0,
                'Clientes__clientes_cobrador' => 0,
                'Clientes__clientes_data_cadastro' => 0,
                'Painel__modulo_juridico' => 0,
                'ContratosModelos__index' => 0
            )
        );
        $this->Uperfil->geraPermissao($codigo_perfil, $data);
        $aro = array('model' => 'Uperfil', 'foreign_key' => $codigo_perfil);
        $aco = 'buonny/Corretoras/index';
        $this->assertTrue($this->Acl->check($aro, $aco));
        $aco = 'buonny/Corretoras/incluir';
        $this->assertTrue($this->Acl->check($aro, $aco));
        $aco = 'buonny/Corretoras/listagem';
        $this->assertTrue($this->Acl->check($aro, $aco));
    }

    function testClienteIncluirPerfil() {
        $dados = array('Uperfil' => array('descricao' => 'Seguradora', 'codigo_cliente' => 19113, 'codigo_tipo_perfil'=> 1 ));
        $this->assertTrue ($this->Uperfil->incluir($dados));
        $this->assertFalse($this->Uperfil->incluir($dados));
        $dados2 = array('Uperfil' => array('descricao' => 'Seguradora', 'codigo_cliente' => 7 ));
        $this->assertTrue ($this->Uperfil->incluir($dados2));
    }

    function testCarregaPerfisCadastradosPeloCliente(){
        $codigo_cliente = 19113;
        $perfis_cliente = $this->Uperfil->carregaPerfisCadastradosPeloCliente( $codigo_cliente );
        $this->assertEqual( 1, count($perfis_cliente) );
    }

    function testPerfilPermitido(){
        $objetos = $this->ObjetoAcl->listaObjetos(5,null, true);
        $perfilPermitido = count($objetos);
        $this->assertEqual(8,$perfilPermitido);     
        
    }

    function testPerfilNaoPermitido(){
        $objetos = $this->ObjetoAcl->listaObjetos(3,null, true);
        $perfilNaoPermitido = count($objetos);
        $this->assertEqual(7,$perfilNaoPermitido);  
    }

    function testCarregaPerfilInterno(){
        $perfis = $this->Uperfil->carrega_perfis_interno();
        $this->assertTrue(in_array('Admin', $perfis));
        $this->assertFalse(in_array('Filial', $perfis));
    }

    function testCarregaPerfilFilho(){
        $perfis = $this->Uperfil->listaPerfilFilho( 11 );
        $this->assertEqual(array(12 => 'Coordenador Buonny Sat',13 => 'Supervisor Buonny Sat',14 => 'Operador BuonnySat'), $perfis);        
        $perfis = $this->Uperfil->listaPerfilFilho( 12 );
        $this->assertEqual(array( 13 => 'Supervisor Buonny Sat'), $perfis);
    }

    function endTest() {
        unset($this->Uperfil);
        unset($this->Acl);
        unset($this->ObjetoAcl);
        ClassRegistry::flush();
    }

}

?>