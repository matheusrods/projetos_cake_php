<?php
class CorretorUsuarioVendas extends AppModel {
    var $name = 'CorretorUsuarioVendas';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth_vendas';
    var $useTable = 'usuario';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    var $message = '';
    var $errorValidation = array();

    public $validate = array(
        'codigo_emp' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a empresa',
                'required' => true
                )
            ),
        'nome' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o nome.',
                'required' => true
                )
            ),
        'email' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o E-mail.',
                'required' => true)
            )   
        );  

    public function exportarCorretor(   $nome_empresa,
                                        $nome_usuario, 
                                        $cnpj,
                                        $email ){


        $success = false;
        $this->query('begin transaction');

        $CorretorEmpresaVendas = ClassRegistry::init('CorretorEmpresaVendas');
        
        // Criar uma empresa antes 
        
        // $insertEmpresa = "  INSERT INTO RHHealth_vendas.dbo.empresa ( nome_empresa, cnpj, email ) VALUES ( '". $nome_empresa ."', '". $cnpj ."', '". $email ."' ); SELECT @@IDENTITY ";
        // $last_id = $success[0][0]['computed'];

        $dadosEmpresa = array(  "nome_empresa"  => $nome_empresa,
                                "cnpj"          => $cnpj,
                                "email"         => $email );

        if( $success = $CorretorEmpresaVendas->incluir( $dadosEmpresa ) ){

            //pr( $CorretorEmpresaVendas );

            // ID Criado
            $last_id = $CorretorEmpresaVendas->id;

            $codigo_validacao = md5( uniqid() );
            // Criar um usuario
            $dados = array (    "codigo_empresa"        => $last_id,
                                "nome"              => "'".$nome_usuario."'",
                                "email"             => "'".$email."'",
                                "codigo_validacao"  => "'".$codigo_validacao."'",
                                "senha"             => 'NULL',
                                "ativo"             => '0' ) ; 

            $insertUsuario = "  INSERT INTO RHHealth_vendas.dbo.usuario 
                                ( ". implode(",", array_keys($dados) ) ." ) VALUES 
                                ( ". implode(",", array_values($dados) ) ." ) ";
            $success = $this->query( $insertUsuario );
            if( $success ){
                // Notificar 
                $success = $this->NotificarEmail( $email, $nome_usuario, $codigo_validacao );
            }

        } else {
            $this->errorValidation = $CorretorEmpresaVendas->invalidFields();
        }
        
        if( $success ){
            $this->message = "Exportado com sucesso!";
            //$this->rollback();
            $this->commit();
        } else {
            $this->message = "Erro ao exportar corretora";
            $this->rollback();
        }

        return $success;

    }

    /**
     * [NotificarEmail description]
     * 
     * metodo para notificar email do corretor
     * 
     * @param [type] $email            [description]
     * @param [type] $nome_usuario     [description]
     * @param [type] $codigo_validacao [description]
     */
    public function NotificarEmail( $email, $nome_usuario, $codigo_validacao )
    {
        //modelo do email a ser disparado
        $template = "envio_acesso_corretor";

        //implementar o tipo do ambiente que irá redirecionar
        $host = 'http://rhhealth_vendas';
        if (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) {
            $host = 'https://portal.rhhealth.com.br/vendas';
        } 
        elseif (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO) {
            $host = 'http://tstportal.rhhealth.com.br/vendas';
        }//fim hosts

        //seta o link para redirecionamento
        $link = "{$host}/login/redefinir_senha/".$codigo_validacao;

        //coloca o email para ser disparado pela rotina de email
        $MailerOutbox = ClassRegistry::init('MailerOutbox');
        return $MailerOutbox->enviaEmail(array(
            'link' => $link,
            'nome_usuario' => $nome_usuario
        ), "Acesso ao Sistema de Vendas / Corretor", $template, $email);
    }//fim NotificarEmail

    /**
     * [esqueciMinhaSenha description]
     * 
     * METODO PARA GERAR UM NOVA SENHA E ENVIAR POR EMAIL
     * 
     * @param  [type] $codigo_usuario [description]
     * @return [type]                 [description]
     */
    public function esqueciMinhaSenha($codigo_usuario)
    {
        //pega os dados do usuario para tratar
        // $usuario = $this->find('first', array('conditions' => array('Usuario.codigo' => $codigo_usuario)));
        $query = "SELECT top 1 codigo, email, nome FROM RHHealth_vendas.dbo.usuario WHERE codigo = " . $codigo_usuario;
        $usuario = $this->query($query);
        $usuario = $usuario[0][0];

        //verifica se existe usuario
        if(!empty($usuario)) {

            $codigo_validacao = md5( uniqid() );
            // atualiza o usuario
            $atualizaUsuario = "  UPDATE RHHealth_vendas.dbo.usuario 
                                SET codigo_validacao = '{$codigo_validacao}',
                                    ativo = '0'
                                WHERE codigo = {$codigo_usuario}";                                
            $success = $this->query( $atualizaUsuario );
            //verifica se foi feito com sucesso
            if( $success ){

                //seta os dados
                $email = $usuario['email'];
                $nome = $usuario['nome'];

                // Notificar 
                $success = $this->NotificarEmail( $email, $nome, $codigo_validacao );

                if($success) {
                    return true;
                }

                return false;

            }//fim sucess

            return false;

        }//fim if usuario

        return false;

    }//fim esqueciMinhaSenha

}

?>