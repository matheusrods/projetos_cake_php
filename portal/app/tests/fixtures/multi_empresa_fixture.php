    <?php

class MultiEmpresaFixture extends CakeTestFixture {

    var $name = 'MultiEmpresa';
    var $table = 'multi_empresa';
    
    var $fields = array(
        'codigo' => array('type' => 'integer', 'null' => true,   'key' => 'primary'),
        'codigo_documento' => array('type' => 'string', 'null' => true,  ),
        'codigo_cliente' => array('type' => 'integer', 'null' => true,  ),
        'codigo_departamento' => array('type' => 'integer', 'null' => true,  ),
        'nome' => array('type' => 'string', 'null' => true,  ),
        'apelido' => array('type' => 'string', 'null' => true,  ),
        'senha' => array('type' => 'string', 'null' => true,  ),
        'email' => array('type' => 'string', 'null' => true,  ),
        'ativo' => array('type' => 'boolean', 'null' => true,  ),
        'data_inclusao' => array('type' => 'datetime', 'default' => '(getdate())',  ),
        'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true,  ),
        'codigo_uperfil' => array('type' => 'integer', 'null' => true,  ),
        'codigo_usuario_monitora' => array('type' => 'string', 'null' => true,  ),
        'celular' => array('type'=>'string', 'null' => true, 'default' => null,  ),
        'alerta_portal' => array('type' => 'boolean', 'null' => true,  ),
        'alerta_email' => array('type' => 'boolean', 'null' => true,  ),
        'alerta_sms' => array('type' => 'boolean', 'null' => true,  ),
        'token' => array('type' => 'string', 'null' => true,  ),
        'digital' => array('type' => 'binary', 'null' => true,  ),
        'cracha' => array('type' => 'integer', 'null' => true,  ),
        'fuso_horario' => array('type' => 'integer', 'null' => true,  ),
        'horario_verao' => array('type' => 'boolean', 'null' => true,  ),
        'codigo_seguradora' => array('type' => 'integer', 'null' => true,  ),
        'codigo_corretora' => array('type' => 'integer', 'null' => true,  ),
        'codigo_filial' => array('type' => 'integer', 'null' => true,  ),
        'data_senha_expiracao' => array('type' => 'datetime', 'default' => '',  ),        
        'permite_sm_sem_pgr' => array('type' => 'boolean', 'null' => true,  ),
        'admin' => array('type' => 'boolean', 'null' => true,  ),
        'alerta_sm_usuario' => array('type' => 'boolean', 'null' => true,  ),
        'alerta_sm_refe_codigo_origem' => array('type' => 'boolean', 'null' => true,  ),
        'refe_codigo_origem' => array('type' => 'integer', 'null' => true,  ),  
        'codigo_usuario_pai' => array('type' => 'integer', 'null' => true,  ),  
        'codigo_usuario_pai_real' => array('type' => 'integer', 'null' => true,  ),  
        'escala' => array('type' => 'integer', 'null' => true,  ),
        'codigo_diretoria' => array('type' => 'integer', 'null' => true,  ),  
        'indexes' => array('0' => array())
    );
    
    var $records = array(
        array(
            'codigo' => 1,
            'codigo_documento' => '42010667387',
            'codigo_cliente' => 1,
            'codigo_departamento' => 1,
            'nome' => 'Nelson Ota',
            'apelido' => 'Nelson',
            'senha' => 'DlBoyICmEh1p86XZHL4o0JJQchimd0b60GMDpyjLMmDP5qBWl+cYDt/ppkVyz5IHVcAy4P49DpynBFVjTee8oQooQhMAKGJj9Oo/7kpkO2bVOTcmDbRrZAuVTXDo3gH2ahRwLSP2y1AKbv9pMYJLa1cKOrJZA0U6W++RgMnE+Ik=',
            'email' => 'nelson.ota@buonny.com.br',
            'ativo' => '1',
            'data_inclusao' => '2011-09-21 00:00:00',
            'codigo_usuario_inclusao' => 1,
            'codigo_uperfil' => null,
            'codigo_usuario_monitora' => '003494',
        	'celular' => '11993114700',
            'alerta_portal' => 1,
            'alerta_email' => 0,
            'alerta_sms' => 0,
        	'token' => '923b1df46c0c5d2044ebb52d41888cea',
            'cracha' => '1234',
            'fuso_horario' => '-2',
            'horario_verao' => '1',
            'codigo_usuario_pai' => 8,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => 2,
            'codigo_documento' => '37368764830',
            'codigo_cliente' => null,
            'codigo_departamento' => 1,
            'nome' => 'Pesquisador Automatico',
            'apelido' => 'pesquisador_automatico',
            'senha' => 'DlBoyICmEh1p86XZHL4o0JJQchimd0b60GMDpyjLMmDP5qBWl+cYDt/ppkVyz5IHVcAy4P49DpynBFVjTee8oQooQhMAKGJj9Oo/7kpkO2bVOTcmDbRrZAuVTXDo3gH2ahRwLSP2y1AKbv9pMYJLa1cKOrJZA0U6W++RgMnE+Ik=',
            'email' => 'vavum.pesquisei@buonny.com.br',
            'ativo' => '1',
            'data_inclusao' => '2011-09-21 00:00:00',
            'codigo_usuario_inclusao' => 1,
            'codigo_uperfil' => 12,
            'codigo_usuario_monitora' => NULL,
        	'celular' => null,
            'alerta_portal' => 1,
            'alerta_email' => 1,
            'alerta_sms' => 0,
        	'token' => null,
        	'cracha' => '1234',
            'fuso_horario' => '-2',
            'horario_verao' => '1',
            'codigo_usuario_pai' => null,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => 3,
            'codigo_documento' => '10698122851',
            'codigo_cliente' => null,
            'codigo_departamento' => 9,
            'nome' => 'Francisco Tadeu Vieira',
            'apelido' => 'tadeu',
            'senha' => '1234',
            'email' => 'bonequinhodapanco@buonny.com.br',
            'ativo' => '1',
            'data_inclusao' => '2012-01-27 00:00:00',
            'codigo_usuario_inclusao' => 1,
            'codigo_uperfil' => 12,
            'codigo_usuario_monitora' => NULL,
        	'celular' => null,
            'alerta_portal' => 1,
            'alerta_email' => 1,
            'alerta_sms' => 0,
        	'token' => null,
        	'cracha' => '1234',
            'fuso_horario' => '-2',
            'horario_verao' => '1',
            'codigo_usuario_pai' => null,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => 4,
            'codigo_documento' => '35852489859',
            'codigo_cliente' => 19114,
            'codigo_departamento' => 9,
            'nome' => 'LG',
            'apelido' => '19114',
            'senha' => '1234',
            'email' => 'lg@buonny.com.br',
            'ativo' => '1',
            'data_inclusao' => '2012-01-27 00:00:00',
            'codigo_usuario_inclusao' => 1,
            'codigo_uperfil' => null,
            'codigo_usuario_monitora' => NULL,
            'celular' => '1195371111',
            'alerta_portal' => 1,
            'alerta_email' => 1,
            'alerta_sms' => 0,
        	'token' => null,
        	'cracha' => '1234',
            'fuso_horario' => '-2',
            'horario_verao' => '1',
            'codigo_usuario_pai' => null,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => 5,
            'codigo_documento' => '35852489859',
            'codigo_cliente' => 19114,
            'codigo_departamento' => 9,
            'nome' => 'MASTER',
            'apelido' => 'MASTER',
            'senha' => '1234',
            'email' => 'lg@buonny.com.br',
            'ativo' => '1',
            'data_inclusao' => '2012-01-27 00:00:00',
            'codigo_usuario_inclusao' => 1,
            'codigo_uperfil' => null,
            'codigo_usuario_monitora' => NULL,
            'celular' => '1195371112',
            'alerta_portal' => 1,
            'alerta_email' => 0,
            'alerta_sms' => 1,
        	'token' => null,
        	'cracha' => '1234',
            'fuso_horario' => '-2',
            'horario_verao' => '1',
            'codigo_usuario_pai' => null,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => 6,
            'codigo_documento' => '35852489859',
            'codigo_cliente' => 2,
            'codigo_departamento' => 9,
            'nome' => 'MASTER',
            'apelido' => '1',
            'senha' => 'Y2F6FtGrhc9l6kmBi58zMIaNeeW77BkNZWCLsYT7oiYVr4ZhoBPkJaPtpPQlMsBW8TpvhgFvNu/8HBJ5sQaVJmb0zlry0rbf81+ZAd/67h08scsRVLXfDnnZfiw7KQRvmIJ+uGV857QySMC7s3Cr9lZIof6kmZPAmHz5HWaNL9Y=',
            'email' => 'lg@buonny.com.br',
            'ativo' => '1',
            'data_inclusao' => '2012-01-27 00:00:00',
            'codigo_usuario_inclusao' => 1,
            'codigo_uperfil' => null,
            'codigo_usuario_monitora' => NULL,
        	'celular' => null,
            'alerta_portal' => 1,
            'alerta_email' => 0,
            'alerta_sms' => 0,
        	'token' => null,
        	'cracha' => '1234',
            'fuso_horario' => '-2',
            'horario_verao' => '1',
            'codigo_usuario_pai' => null,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => 7,
            'codigo_documento' => '35852489859',
            'codigo_cliente' => 2,
            'codigo_departamento' => 9,
            'nome' => 'MASTER',
            'apelido' => '1',
            'senha' => 'Y2F6FtGrhc9l6kmBi58zMIaNeeW77BkNZWCLsYT7oiYVr4ZhoBPkJaPtpPQlMsBW8TpvhgFvNu/8HBJ5sQaVJmb0zlry0rbf81+ZAd/67h08scsRVLXfDnnZfiw7KQRvmIJ+uGV857QySMC7s3Cr9lZIof6kmZPAmHz5HWaNL9Y=',
            'email' => 'lg@buonny.com.br',
            'ativo' => '1',
            'data_inclusao' => '2012-01-27 00:00:00',
            'codigo_usuario_inclusao' => 1,
            'codigo_uperfil' => 1,
            'codigo_usuario_monitora' => NULL,
        	'celular' => null,
            'alerta_portal' => 1,
            'alerta_email' => 1,
            'alerta_sms' => 0,
        	'token' => null,
        	'cracha' => '1234',
            'fuso_horario' => '-2',
            'horario_verao' => '1',
            'codigo_filial' => 1,
            'codigo_usuario_pai' => null,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => 8,
            'codigo_documento' => '35852489859',
            'codigo_cliente' => 2,
            'codigo_departamento' => 9,
            'nome' => 'MASTER',
            'apelido' => '1',
            'senha' => 'Y2F6FtGrhc9l6kmBi58zMIaNeeW77BkNZWCLsYT7oiYVr4ZhoBPkJaPtpPQlMsBW8TpvhgFvNu/8HBJ5sQaVJmb0zlry0rbf81+ZAd/67h08scsRVLXfDnnZfiw7KQRvmIJ+uGV857QySMC7s3Cr9lZIof6kmZPAmHz5HWaNL9Y=',
            'email' => 'lg@buonny.com.br',
            'ativo' => '1',
            'data_inclusao' => '2012-01-27 00:00:00',
            'codigo_usuario_inclusao' => 1,
            'codigo_uperfil' => 1,
            'codigo_usuario_monitora' => NULL,
        	'celular' => null,
            'alerta_portal' => 1,
            'alerta_email' => 0,
            'alerta_sms' => 0,
        	'token' => null,
        	'cracha' => '1234',
            'fuso_horario' => '-2',
            'horario_verao' => '1',
            'codigo_corretora' => 1,
            'codigo_usuario_pai' => 7,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => 9,
            'codigo_documento' => '35852489859',
            'codigo_cliente' => 773,
            'codigo_departamento' => 9,
            'nome' => 'Leandro Lima',
            'apelido' => '1',
            'senha' => 'Y2F6FtGrhc9l6kmBi58zMIaNeeW77BkNZWCLsYT7oiYVr4ZhoBPkJaPtpPQlMsBW8TpvhgFvNu/8HBJ5sQaVJmb0zlry0rbf81+ZAd/67h08scsRVLXfDnnZfiw7KQRvmIJ+uGV857QySMC7s3Cr9lZIof6kmZPAmHz5HWaNL9Y=',
            'email' => 'lg@buonny.com.br',
            'ativo' => '1',
            'data_inclusao' => '2012-01-27 00:00:00',
            'codigo_usuario_inclusao' => 1,
            'codigo_uperfil' => 2,
            'codigo_usuario_monitora' => NULL,
        	'celular' => null,
            'alerta_portal' => 1,
            'alerta_email' => 1,
            'alerta_sms' => 0,
        	'token' => 'Y2F6FtGrhc9l6kmBi58z',
        	'cracha' => '1234',
            'fuso_horario' => '-2',
            'horario_verao' => '1',
            'codigo_seguradora' => 1,
            'refe_codigo_origem' => 16526,
            'codigo_usuario_pai' => 7,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => 10,
            'codigo_documento' => '25846985521',
            'codigo_cliente' => NULL,
            'codigo_departamento' => 18,
            'nome' => 'SILVESTRE STALONNE',
            'apelido' => 'silvestre.stalonne',
            'senha' => 'Y2F6FtGrhc9l6kmBi58zMIaNeeW77BkNZWCLsYT7oiYVr4ZhoBPkJaPtpPQlMsBW8TpvhgFvNu/8HBJ5sQaVJmb0zlry0rbf81+ZAd/67h08scsRVLXfDnnZfiw7KQRvmIJ+uGV857QySMC7s3Cr9lZIof6kmZPAmHz5HWaNL9Y=',
            'email' => 'rambo@buonny.com.br',
            'ativo' => '1',
            'data_inclusao' => '2012-01-27 00:00:00',
            'codigo_usuario_inclusao' => 1,
            'codigo_uperfil' => 2,
            'codigo_usuario_monitora' => NULL,
            'celular' => null,
            'alerta_portal' => 1,
            'alerta_email' => 0,
            'alerta_sms' => 0,
            'token' => '',
            'cracha' => '221234',
            'fuso_horario' => '-2',
            'horario_verao' => '1',
            'codigo_seguradora' => NULL,
            'refe_codigo_origem' => 16526,
            'codigo_usuario_pai' => 9,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => '54594', 
            'codigo_documento' => '71904627000115', 
            'codigo_cliente' => 773, 
            'codigo_departamento' => 11, 
            'nome' => 773, 
            'apelido' => 773, 
            'senha' => 'rD7D/8ZRWdyR1xFAXRU1OrVpseFjEPWscTkUF/HJD8UCINxbFzT5c3lflaPguLGjLcanQ+moWUt0Kwb/DGea+tqKAeq+f9nl/W+b7AfpVCiiDIDE14eqSGCNJkdyDbGYRuxjn3gzJ6xTRd3D6utxGFJHPcTi4FdE+zFs8Z11334=', 
            'email' => 'tam@teste.com.br', 
            'ativo' => '1', 
            'data_inclusao' => '2015-02-10 18:05:26.000', 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'codigo_usuario_monitora' => '035199', 
            'celular' => '', 
            'alerta_portal' => '0', 
            'alerta_email' => '0', 
            'alerta_sms' => '0', 
            'token' => '4d563b34fb503e634d05557b3048ab0e', 
            'cracha' => null, 
            'fuso_horario' => null, 
            'horario_verao' => null, 
            'codigo_seguradora' => null, 
            'refe_codigo_origem' => null, 
            'codigo_usuario_pai' => null,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => '29504', 
            'codigo_documento' => '71904627000245', 
            'codigo_cliente' => 773, 
            'codigo_departamento' => 11, 
            'nome' => 'RENATO ZACCARDELLI', 
            'apelido' => 'renato.zaccardelli', 
            'senha' => 'rD7D/8ZRWdyR1xFAXRU1OrVpseFjEPWscTkUF/HJD8UCINxbFzT5c3lflaPguLGjLcanQ+moWUt0Kwb/DGea+tqKAeq+f9nl/W+b7AfpVCiiDIDE14eqSGCNJkdyDbGYRuxjn3gzJ6xTRd3D6utxGFJHPcTi4FdE+zFs8Z11334=', 
            'email' => 'teste@teste.com.br', 
            'ativo' => '1', 
            'data_inclusao' => '2015-02-10 18:05:26.000', 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'codigo_usuario_monitora' => '035599', 
            'celular' => '', 
            'alerta_portal' => '0', 
            'alerta_email' => '0', 
            'alerta_sms' => '0', 
            'token' => '4d563b34fb503e634d05557b3048ab0e', 
            'cracha' => null, 
            'fuso_horario' => null, 
            'horario_verao' => null, 
            'codigo_seguradora' => null, 
            'refe_codigo_origem' => null, 
            'codigo_usuario_pai' => 9,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => '30291', 
            'codigo_documento' => '71904627012785', 
            'codigo_cliente' => 773, 
            'codigo_departamento' => 11, 
            'nome' => 'BRUNO SALUTES', 
            'apelido' => 'bruno.salutes', 
            'senha' => 'rD7D/8ZRWdyR1xFAXRU1OrVpseFjEPWscTkUF/HJD8UCINxbFzT5c3lflaPguLGjLcanQ+moWUt0Kwb/DGea+tqKAeq+f9nl/W+b7AfpVCiiDIDE14eqSGCNJkdyDbGYRuxjn3gzJ6xTRd3D6utxGFJHPcTi4FdE+zFs8Z11334=', 
            'email' => 'teste@teste.com.br', 
            'ativo' => '1', 
            'data_inclusao' => '2015-02-10 18:05:26.000', 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'codigo_usuario_monitora' => '035599', 
            'celular' => '', 
            'alerta_portal' => '0', 
            'alerta_email' => '0', 
            'alerta_sms' => '0', 
            'token' => '4d563b34fb503e634d05557b3048ab0e', 
            'cracha' => null, 
            'fuso_horario' => null, 
            'horario_verao' => null, 
            'codigo_seguradora' => null, 
            'refe_codigo_origem' => null, 
            'codigo_usuario_pai' => null,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => '29977', 
            'codigo_documento' => '71904627012785', 
            'codigo_cliente' => 773, 
            'codigo_departamento' => 11, 
            'nome' => 'BRUNO KAUFFMAN', 
            'apelido' => 'bruno.kauffman', 
            'senha' => 'rD7D/8ZRWdyR1xFAXRU1OrVpseFjEPWscTkUF/HJD8UCINxbFzT5c3lflaPguLGjLcanQ+moWUt0Kwb/DGea+tqKAeq+f9nl/W+b7AfpVCiiDIDE14eqSGCNJkdyDbGYRuxjn3gzJ6xTRd3D6utxGFJHPcTi4FdE+zFs8Z11334=', 
            'email' => 'teste@teste.com.br', 
            'ativo' => '1', 
            'data_inclusao' => '2015-02-10 18:05:26.000', 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'codigo_usuario_monitora' => '035599', 
            'celular' => '', 
            'alerta_portal' => '0', 
            'alerta_email' => '0', 
            'alerta_sms' => '0', 
            'token' => '4d563b34fb503e634d05557b3048ab0e', 
            'cracha' => null, 
            'fuso_horario' => null, 
            'horario_verao' => null, 
            'codigo_seguradora' => null, 
            'refe_codigo_origem' => null, 
            'codigo_usuario_pai' => null,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => '29531', 
            'codigo_documento' => '71904627111785', 
            'codigo_cliente' => 773, 
            'codigo_departamento' => 11, 
            'nome' => 'Ane Stadnick', 
            'apelido' => 'ane.stadnick', 
            'senha' => 'rD7D/8ZRWdyR1xFAXRU1OrVpseFjEPWscTkUF/HJD8UCINxbFzT5c3lflaPguLGjLcanQ+moWUt0Kwb/DGea+tqKAeq+f9nl/W+b7AfpVCiiDIDE14eqSGCNJkdyDbGYRuxjn3gzJ6xTRd3D6utxGFJHPcTi4FdE+zFs8Z11334=', 
            'email' => 'teste@teste.com.br', 
            'ativo' => '1', 
            'data_inclusao' => '2015-02-10 18:05:26.000', 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'codigo_usuario_monitora' => '035599', 
            'celular' => '', 
            'alerta_portal' => '0', 
            'alerta_email' => '0', 
            'alerta_sms' => '0', 
            'token' => '4d563b34fb503e634d05557b3048ab0e', 
            'cracha' => null, 
            'fuso_horario' => null, 
            'horario_verao' => null, 
            'codigo_seguradora' => null, 
            'refe_codigo_origem' => null, 
            'codigo_usuario_pai' => null,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => '29534', 
            'codigo_documento' => '71904111111785', 
            'codigo_cliente' => 773, 
            'codigo_departamento' => 11, 
            'nome' => 'EDUARDO SAMPAIO', 
            'apelido' => 'eduardo.sampaio', 
            'senha' => 'rD7D/8ZRWdyR1xFAXRU1OrVpseFjEPWscTkUF/HJD8UCINxbFzT5c3lflaPguLGjLcanQ+moWUt0Kwb/DGea+tqKAeq+f9nl/W+b7AfpVCiiDIDE14eqSGCNJkdyDbGYRuxjn3gzJ6xTRd3D6utxGFJHPcTi4FdE+zFs8Z11334=', 
            'email' => 'teste@teste.com.br', 
            'ativo' => '1', 
            'data_inclusao' => '2015-02-10 18:05:26.000', 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'codigo_usuario_monitora' => '035599', 
            'celular' => '', 
            'alerta_portal' => '0', 
            'alerta_email' => '0', 
            'alerta_sms' => '0', 
            'token' => '4d563b34fb503e634d05557b3048ab0e', 
            'cracha' => null, 
            'fuso_horario' => null, 
            'horario_verao' => null, 
            'codigo_seguradora' => null, 
            'refe_codigo_origem' => null, 
            'codigo_usuario_pai' => null,
        ),
        array(
            'codigo' => '29610', 
            'codigo_documento' => '47508411083264', 
            'codigo_cliente' => 29610, 
            'codigo_departamento' => 11, 
            'nome' => 'COMPANHIA BRASILEIRA DE DISTRIBUICAO', 
            'apelido' => '29610', 
            'senha' => 'rD7D/8ZRWdyR1xFAXRU1OrVpseFjEPWscTkUF/HJD8UCINxbFzT5c3lflaPguLGjLcanQ+moWUt0Kwb/DGea+tqKAeq+f9nl/W+b7AfpVCiiDIDE14eqSGCNJkdyDbGYRuxjn3gzJ6xTRd3D6utxGFJHPcTi4FdE+zFs8Z11334=', 
            'email' => 'teste@teste.com.br', 
            'ativo' => '1', 
            'data_inclusao' => '2015-02-10 18:05:26.000', 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 21, 
            'codigo_usuario_monitora' => '035599', 
            'celular' => '', 
            'alerta_portal' => '0', 
            'alerta_email' => '0', 
            'alerta_sms' => '0', 
            'token' => 'f0b7d759cd38520aeefb40f945d6e9c5', 
            'cracha' => null, 
            'fuso_horario' => null, 
            'horario_verao' => null, 
            'codigo_seguradora' => null, 
            'refe_codigo_origem' => null, 
            'codigo_usuario_pai' => 8,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => '13', 
            'codigo_documento' => '66607649505', 
            'codigo_cliente' => NULL, 
            'codigo_departamento' => 11, 
            'nome' => 'FEANOR', 
            'apelido' => 'feanor', 
            'senha' => 'rD7D/8ZRWdyR1xFAXRU1OrVpseFjEPWscTkUF/HJD8UCINxbFzT5c3lflaPguLGjLcanQ+moWUt0Kwb/DGea+tqKAeq+f9nl/W+b7AfpVCiiDIDE14eqSGCNJkdyDbGYRuxjn3gzJ6xTRd3D6utxGFJHPcTi4FdE+zFs8Z11334=', 
            'email' => 'teste@teste.com.br', 
            'ativo' => '1', 
            'data_inclusao' => '2015-02-10 18:05:26.000', 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 2, 
            'codigo_usuario_monitora' => '035599', 
            'celular' => '', 
            'alerta_portal' => '0', 
            'alerta_email' => '0', 
            'alerta_sms' => '0', 
            'token' => '4d563b34fb503e634d05557b3048ab0e', 
            'cracha' => null, 
            'fuso_horario' => null, 
            'horario_verao' => null, 
            'codigo_seguradora' => null, 
            'refe_codigo_origem' => null, 
            'codigo_usuario_pai' => 8,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),
        array(
            'codigo' => '14', 
            'codigo_documento' => '43643086849', 
            'codigo_cliente' => NULL, 
            'codigo_departamento' => 11, 
            'nome' => 'Tom Bombadil', 
            'apelido' => 'tom.bombadil', 
            'senha' => 'rD7D/8ZRWdyR1xFAXRU1OrVpseFjEPWscTkUF/HJD8UCINxbFzT5c3lflaPguLGjLcanQ+moWUt0Kwb/DGea+tqKAeq+f9nl/W+b7AfpVCiiDIDE14eqSGCNJkdyDbGYRuxjn3gzJ6xTRd3D6utxGFJHPcTi4FdE+zFs8Z11334=', 
            'email' => 'tom.bombadil@teste.com.br', 
            'ativo' => '1', 
            'data_inclusao' => '2015-06-10 18:05:26.000', 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 2, 
            'codigo_usuario_monitora' => '035599', 
            'celular' => '', 
            'alerta_portal' => '0', 
            'alerta_email' => '0', 
            'alerta_sms' => '0', 
            'token' => '4d563b34fb503e634d05557b3048ab0e', 
            'cracha' => null, 
            'fuso_horario' => null, 
            'horario_verao' => null, 
            'codigo_seguradora' => null, 
            'refe_codigo_origem' => null, 
            'codigo_usuario_pai' => 6,
            'codigo_usuario_pai_real'=>13,
            'escala' => 1
        ),
        array(
            'codigo' => '15', 
            'codigo_documento' => '67100961815', 
            'codigo_cliente' => 36120, 
            'codigo_departamento' => 11, 
            'nome' => 'TOPEIRA', 
            'apelido' => 'topeira', 
            'senha' => 'rD7D/8ZRWdyR1xFAXRU1OrVpseFjEPWscTkUF/HJD8UCINxbFzT5c3lflaPguLGjLcanQ+moWUt0Kwb/DGea+tqKAeq+f9nl/W+b7AfpVCiiDIDE14eqSGCNJkdyDbGYRuxjn3gzJ6xTRd3D6utxGFJHPcTi4FdE+zFs8Z11334=', 
            'email' => 'teste@teste.com.br', 
            'ativo' => '1', 
            'data_inclusao' => '2015-02-10 18:05:26.000', 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 2, 
            'codigo_usuario_monitora' => '035599', 
            'celular' => '', 
            'alerta_portal' => '0', 
            'alerta_email' => '0', 
            'alerta_sms' => '0', 
            'token' => '4d563b34fb503e634d05557b3048ab0e', 
            'cracha' => null, 
            'fuso_horario' => null, 
            'horario_verao' => null, 
            'codigo_seguradora' => null, 
            'refe_codigo_origem' => '16618', 
            'codigo_usuario_pai' => null,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0,
            'alerta_sm_refe_codigo_origem' => 1
        ),
        array(
            'codigo' => '21430', 
            'codigo_documento' => '67100961814', 
            'codigo_cliente' => 36120, 
            'codigo_departamento' => 11, 
            'nome' => 'Percival', 
            'apelido' => 'percival.tavares', 
            'senha' => 'rD7D/8ZRWdyR1xFAXRU1OrVpseFjEPWscTkUF/HJD8UCINxbFzT5c3lflaPguLGjLcanQ+moWUt0Kwb/DGea+tqKAeq+f9nl/W+b7AfpVCiiDIDE14eqSGCNJkdyDbGYRuxjn3gzJ6xTRd3D6utxGFJHPcTi4FdE+zFs8Z11334=', 
            'email' => 'teste@teste.com.br', 
            'ativo' => '1', 
            'data_inclusao' => '2015-02-10 18:05:26.000', 
            'codigo_usuario_inclusao' => 1, 
            'codigo_uperfil' => 2, 
            'codigo_usuario_monitora' => '035599', 
            'celular' => '', 
            'alerta_portal' => '0', 
            'alerta_email' => '0', 
            'alerta_sms' => '0', 
            'token' => '4d563b34fb503e634d05557b3048ab0e', 
            'cracha' => null, 
            'fuso_horario' => null, 
            'horario_verao' => null, 
            'codigo_seguradora' => null, 
            'refe_codigo_origem' => null, 
            'codigo_usuario_pai' => null,
            'codigo_usuario_pai_real'=>null,
            'escala' => 0
        ),    
    );

}



