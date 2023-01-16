<?php
/**
 * Classe usada para encriptação.
 */
class Buonny_Encriptacao
{
    /**
     * Chave privada. Pode ser usada para encriptação e desencriptação.
     */
    const CHAVE_PRIVADA = "-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQC9HZ2+0mfyWa0SEKR7pv2T/vcsUuujWVxZX7ul2oPi3O44m8x9
EaHJCe9JNH6i8j0fuzEqVNfsbf7BP/gcmf/QcEumBvM6XXnxfE+FMaEjigRGR7wz
eYmVXyxAaQffV9EXx+6Y9ZWXdYhtJZ65PGwWEhUiArFIzDe+LTA4W8M6kwIDAQAB
AoGBAJmcYdJq87Xd83+V9xTdWmIJGtps7Cv93M5XvYMFUFVI4VMn8dyxIrm6kRvk
QOy4WL/afCAHVHAeFG0COdV5nVhYMqDrK3wnyOiaoDcHcAIoJplGPIZyIcKZqIft
twP6UhZz+8RjJffjEPnuFujC/toOO80LVShn4S+XgpgSAFIBAkEA+qFEFk6gW2PQ
PsbyLFmv6aIyvY8TezII3GotyIDR9MFG48etpYUlXQWzwAYnHzQkm6juPJisQxGg
z7a+kL3XkwJBAMEq7/2YuzacTOYtkwlmeNMGB3SVqSdsuDagHP6C0mIV0ZpFAy4y
fR9qxnGIZg3Zxa934JTa9y5osFrpD+Uc8QECQGpjCA7cA0+n1969/lB7JaPr2NQE
JsXVoFNHsvV4UScu39OSkBBBq3GIGRv6wKKLNLrqg24vdHfnxLZHIS85locCQAUB
+vTFF91jke7Jsa0lte4qktjn5Fm8TM5Fulqyw4d9b1Cjh2CUOIAvAXQCCDtFsQVK
g0z4nD3cwu2oaxwScwECQQCZb8iU95ICS04hHEArkAiAuVikGlSjgxEBJXRmF/z3
ZVm95OAiYvIJDKThCYrOgS9hHnsAqXm1IQCyQNSUZjxB
-----END RSA PRIVATE KEY-----";

    /**
     * Chave pública. Pode ser apenas usada para encriptação.
     */
    const CHAVE_PUBLICA = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC9HZ2+0mfyWa0SEKR7pv2T/vcs
UuujWVxZX7ul2oPi3O44m8x9EaHJCe9JNH6i8j0fuzEqVNfsbf7BP/gcmf/QcEum
BvM6XXnxfE+FMaEjigRGR7wzeYmVXyxAaQffV9EXx+6Y9ZWXdYhtJZ65PGwWEhUi
ArFIzDe+LTA4W8M6kwIDAQAB
-----END PUBLIC KEY-----";

    /**
     * A OpenSSL só consegue criptografar 117 bytes por vez
     */
    const TAMANHO_CHUNK = 117;

    /**
     * Delimitador de chunk
     */
    const DELIMITADOR_CHUNK = '|';

    /**
     * Resource da chave privada
     * @var integer
     */
    private $_chavePrivada;

    /**
     * Resource da chave publica
     * @var integer
     */
    private $_chavePublica;

    public function  __construct()
    {
        $this->_chavePrivada = openssl_pkey_get_private(self::CHAVE_PRIVADA);
        $this->_chavePublica = openssl_pkey_get_public(self::CHAVE_PUBLICA);
    }

    /**
     * Função usada para encriptar dados com uma chave privada.
     * @param string $dadosDesencriptados Dados a serem encriptados
     * @return string String codificada em base64
     */
    public function encriptar($dadosDesencriptados)
    {
        if (empty($dadosDesencriptados) || !is_string($dadosDesencriptados)) {
            throw new InvalidArgumentException(
                    'A variável $dadosDesencriptados não pode ser vazia ou ' .
                    'ter dados que não sejam string.'
            );
        }

        $dadosDesencriptados = str_split(
                $dadosDesencriptados,
                self::TAMANHO_CHUNK
        );
        
        $dadosEncriptados = array();

        foreach ($dadosDesencriptados as $chunkDesencriptado) {
            if (openssl_public_encrypt($chunkDesencriptado, $chunkEncriptado, $this->_chavePublica)) {
                $dadosEncriptados[] = base64_encode($chunkEncriptado);
            } else {
                return false;
            }
        }

        return join(self::DELIMITADOR_CHUNK, $dadosEncriptados);
    }

    /**
     * Função usada para decriptar dados com uma chave privada.
     * @param string $dadosEncriptados Dados a serem desencripados
     * @return string String decriptada
     */
    public function desencriptar($dadosEncriptados)
    {
        if (empty($dadosEncriptados) || !is_string($dadosEncriptados)) {
            throw new InvalidArgumentException(
                    'A variável $dadosEncriptados não pode ser vazia ou ' .
                    'ter dados que não sejam string.'
            );
        }

        $dadosEncriptados = explode(self::DELIMITADOR_CHUNK, $dadosEncriptados);
        $dadosDesencriptados = array();

        foreach ($dadosEncriptados as $chunkEncriptado) {
            if (openssl_private_decrypt(base64_decode($chunkEncriptado), $chunkDesencriptado, $this->_chavePrivada)) {
                $dadosDesencriptados[] = $chunkDesencriptado;
            } else {
                return false;
            }
        }

        return join('', $dadosDesencriptados);
    }
}