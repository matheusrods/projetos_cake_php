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
            $dados = array (    "codigo_emp"        => $last_id,
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

    function NotificarEmail( $email, $nome_usuario, $codigo_validacao ){

        $template = "envio_acesso_corretor";
        $host = "local.rhhvendas";
        $link = "http://{$host}/login/redefinir_senha/".$codigo_validacao;


        $MailerOutbox = ClassRegistry::init('MailerOutbox');
        return $MailerOutbox->enviaEmail(array(
            'link' => $link,
            'nome_usuario' => $nome_usuario
        ), "Acesso ao Sistema de Vendas / Corretor", $template, $email);
    }

}

?>