<?php

/**
 * Consumo do Web services API-PESSOA
 *
 * @package    core_webservice
 * @copyright AGU - Advocacia Geral da União ©2016 <http://www.agu.gov.br>
 * @license Este código é livre para uso dentro do AGU! Fora deste pode apenas
 *          servir como fonte de estudo ou base para futuros códigos-fonte sem
 *          nenhuma restrição, salvo pelas informações de @copyright e @author
 *          que devem ser mantidas inalteradas.
 * @author Basis Tecnologia da Informação
 * @author Anderson Sathler M. Ribeiro <asathler@gmail.com>
 */

// Guzzle - Para consumo do serviço
//require_once('config.php');
//require_once($CFG->dirroot . '/vendor/autoload.php');
require_once($CFG->dirroot . '/lib/guzzle/autoload.php');
use GuzzleHttp\Client;


class PessoaAgu
{

    /**
     * Atualiza dados do usuário, recebido do serviço, conforme $emailUsuario
     *
     * @param string $emailcpfUsuario
     * @return bool
     */
    public function atualizaDadosUsuario($emailcpfUsuario)
    {
        global $DB;

        // Busca dados do usuário, conforme email, via Serviço
        $cpf = is_numeric($emailcpfUsuario['cpf']);
        if ($cpf){
            $cpfexiste = self::BuscaCpfUsuario_existe($emailcpfUsuario);
            unset($emailcpfUsuario);
            if ($cpfexiste['dadosUsuario'] != 1){
                return false;
            }
            return true;
        }
        $dadosUsuario = self::buscaDadosUsuarioPorServico($emailcpfUsuario);

        if ($dadosUsuario['sucesso'] != true) {
            return false;
        }

        $dados = $dadosUsuario['dadosUsuario'];

        // Separa nome e sobrenome
        $nomeCompleto = isset($dados['nome']) ? trim($dados['nome']) : '';
        $posicaoEspaco = strpos($nomeCompleto, ' ');

        if ($posicaoEspaco != false) {
            $nome = substr($nomeCompleto, 0, $posicaoEspaco);
            $sobrenome = substr($nomeCompleto, $posicaoEspaco++);
        }

        $cpf = isset($dados['cpf']) ? $dados['cpf'] : '';
        $siape = isset($dados['dadoFuncional']['siape']) ? $dados['dadoFuncional']['siape'] : '';
        $cargo = isset($dados['cargoEfetivo']['descricao']) ? $dados['cargoEfetivo']['descricao'] : '';
        $lotacao = isset($dados['lotacao']['descricao']) ? $dados['lotacao']['descricao'] : '';
//        $cidade = isset($dados['lotacao']['endereco']['municipio']) ? $dados['lotacao']['endereco']['municipio'] : '';
//        $sexo = isset($dados['sexo']) ? $dados['sexo'] : '';
//        $formacao = isset($dados['formacao']) ? $dados['formacao'] : '';
//        $situacao = isset($dados['situacao']) ? $dados['situacao'] : '';
//        $uf = isset($dados['lotacao']['endereco']['uf']['sigla']) ? $dados['lotacao']['endereco']['uf']['sigla'] : '';
//        $funcao = isset($dados['funcaoComissionada']['descricao']) ? $dados['funcaoComissionada']['descricao'] : '';
//        $escolaridade = isset($dados['escolaridade']) ? $dados['escolaridade'] : 0;
//        $lotacaoEndereco = isset($dados['lotacao']['endereco']['endereco']) ? $dados['lotacao']['endereco']['endereco'] : '';
//        $lotacaoEndereco .= isset($dados['lotacao']['endereco']['complemento']) ? ' ' . $dados['lotacao']['endereco']['complemento'] : '';
//        $valores = array($cpf, $sexo, $escolaridade, $formacao, $situacao, $uf, $siape, $cargo, $funcao, $lotacao);


        $usuarioExiste = <<<SQL
        SELECT id FROM mdl_user WHERE cpf = '{$cpf}'
SQL;
        // Verifica se a tabela possui o usuario de acordo com o email. (Retorna 1 se existir)
        $existe = $DB->record_exists_sql($usuarioExiste);

        if ($existe !== 1) {
            $existecargo = "SELECT id from mdl_user_cargo where name = '{$cargo}'";
            $existe = $DB->record_exists_sql($existecargo);
            if (!$existe) {
             $DB->execute("INSERT INTO mdl_user_cargo (name) VALUES('$cargo')");
            }

            $idcargo = $DB->get_fieldset_sql($existecargo);
            $cargo = $DB->get_record('user_cargo', array('id'=>$idcargo[0]));

            // Atualiza os dados do servidor
            $sql = '';
            $sql .= 'UPDATE ';
            $sql .= 'mdl_user ';
            $sql .= 'SET ';
            $sql .= 'firstname = \'' . $nome . '\' , ';
            $sql .= 'lastname = \'' . $sobrenome . '\' , ';
//            $sql .= 'city = \'' . $cidade . '\' , ';
////            $sql .= 'address = \'' . $lotacaoEndereco . '\' , ';
            $sql .= 'cargo = \'' . $cargo->id . '\' , ';
            $sql .= 'description = \'' . $cargo->name . '\' , ';
            $sql .= 'lotacao = \'' . $lotacao . '\' , ';
            $sql .= 'siape = \'' . $siape . '\' , ';
////            $sql .= 'pemail = \'' . $emailcpfUsuario . '\' ';
            $sql .= 'cpf = \'' . $cpf . '\' ';
            $sql .= 'WHERE ';
            $sql .= 'email = \'' . $emailcpfUsuario . '\' ';

            $DB->execute($sql);

            return true;
        }
    }

    /**
     * Método que consome o serviço, através da biblioteca Guzzle, para posterior atualização dos dados do usuário
     *
     * @param string $emailUsuario
     * @return bool
     */
    private function buscaDadosUsuarioPorServico($emailUsuario)
    {
        global $CFG;

        $apiUrl = $CFG->apiurl;                 // http://api-pessoas.agu.gov.br
        $apiMetodo = $CFG->apimetodo;           // servidor
        $apiVersao = $CFG->apiversao;           // 1
        $apiUsuario = $CFG->apiusuario;         // eva
        $apiSenha = $CFG->apisenha;             // eva@123
        $apiAplicativo = $CFG->apiaplicativo;   // evaead

        $retorno['sucesso'] = false;
        $retorno['mensagem'] = 'Erro ao buscar dados do usuario, via serviço.';
        $retorno['dadosUsuario'] = null;

        // Definições
        $paramAdicional = '/v' . $apiVersao . '/' . $apiMetodo;

        $opcoes['auth'] = array($apiUsuario, $apiSenha);
        $opcoes['form_params']['app'] = $apiAplicativo; //evaead
        $opcoes['form_params']['email'] = $emailUsuario;

        try {
            $cliente = new Client(['base_uri' => $apiUrl]);
            $requisicao = $cliente->request(
                'POST',
                $paramAdicional,
                $opcoes
            );
            $resposta = $requisicao->getBody()->getContents();
            $dados = json_decode($resposta, true);
            $dadosUsuario = $dados['data'];

        } catch (\Exception $e) {
            $msgErro = '';
            $msgErro .= '<!-- ' . PHP_EOL;
            $msgErro .= 'Erro no Serviço:' . $apiUrl . $paramAdicional . PHP_EOL;
            $msgErro .= 'Nº: ' . $e->getCode() . PHP_EOL;
            $msgErro .= 'Descrição: ' . $e->getMessage() . PHP_EOL;
            $msgErro .= ' -->';

            // Mensagem de erro, propositalmente, não apresentada
            $retorno['mensagem'] = $msgErro;

            return $retorno;
        }

        $retorno['sucesso'] = true;
        $retorno['mensagem'] = '';
        $retorno['dadosUsuario'] = $dadosUsuario;

        return $retorno;
    }

    public function BuscaCpfUsuario_existe($cpfUsuario) {
        global $CFG;

        $apiUrl = "http://api-agupessoas.agu.gov.br/";
        $apiMetodo = "servidor/cpf/";
        $apiVersao = "1";
        $cpf = (string) $cpfUsuario['cpf'];

        $retorno['sucesso'] = false;
        $retorno['mensagem'] = 'Erro ao buscar dados do usuario, via serviço.';
        $retorno['dadosUsuario'] = null;

        // Definições
        $paramAdicional = '/api/v' . $apiVersao . '/' . $apiMetodo;
        $url = "http://api-agupessoas.agu.gov.br/" . $paramAdicional . $cpf;

        try {
            $requisicao = json_decode(file_get_contents($url), true);
            $dadosUsuario = $requisicao[0]['in_status_servidor'];

        } catch (Exception $e) {
            $msgErro = '';
            $msgErro .= '<!-- ' . PHP_EOL;
            $msgErro .= 'Erro no Serviço:' . $apiUrl . $paramAdicional . PHP_EOL;
            $msgErro .= 'Nº: ' . $e->getCode() . PHP_EOL;
            $msgErro .= 'Descrição: ' . $e->getMessage() . PHP_EOL;
            $msgErro .= ' -->';

            // Mensagem de erro, propositalmente, não apresentada
            $retorno['mensagem'] = $msgErro;

            return $retorno;
        }

        $retorno['sucesso'] = true;
        $retorno['mensagem'] = '';
        $retorno['dadosUsuario'] = $dadosUsuario;

        return $retorno;
    }

}

//        $usuarioExiste = <<<SQL
//        SELECT a.userid FROM mdl_user_info_data a INNER JOIN mdl_user b on a.userid = b.id
//        WHERE b.email = '{$emailUsuario}'
//SQL;
//if ($existe ==! 1) {
//    $pegaId = "SELECT id from mdl_user where cpf = '$cpf'";
//
//    $idUser = $DB->get_fieldset_sql($pegaId);
//    var_dump($idUser);exit();
//
//    foreach ($valores as $key => $valor) {
//        $key = $key + 1;
//        $sqlInsert = "";
//        $sqlInsert .= "INSERT INTO mdl_user_info_data ";
//        $sqlInsert .= "(userid, fieldid, data, dataformat) ";
//        $sqlInsert .= "VALUES($idUser[0], $key, '$valor', 0) ";
//
//        $DB->execute($sqlInsert);
//    }
//} else {
//    // Atualiza os dados do servidor
//    $sql = '';
//    $sql .= 'UPDATE ';
//    $sql .= 'mdl_user ';
//    $sql .= 'SET ';
//    $sql .= 'firstname = \'' . $nome . '\' , ';
//    $sql .= 'lastname = \'' . $sobrenome . '\' , ';
//    $sql .= 'city = \'' . $cidade . '\' , ';
//    $sql .= 'address = \'' . $lotacaoEndereco . '\' ';
//    $sql .= 'email = \'' . $emailUsuario . '\' ';
//    $sql .= 'cpf = \'' . $cpf . '\' , ';
//    $sql .= 'WHERE ';
//    $sql .= 'pemail = \'' . $emailUsuario . '\' ';
//
//            foreach ($valores as $key => $valor) {
//                $key = $key + 1;
//
//                $sql2 = "";
//                $sql2 .= "UPDATE ";
//                $sql2 .= "  mdl_user_info_data a ";
//                $sql2 .= "INNER JOIN ";
//                $sql2 .= "mdl_user b on a.userid = b.id ";
//                $sql2 .= "SET ";
//                $sql2 .= "  a.data = '$valor' ";
//                $sql2 .= "WHERE ";
//                $sql2 .= "  b.email = '$emailUsuario' ";
//                $sql2 .= "AND ";
//                $sql2 .= "  a.fieldid = " . $key;
//
//                $DB->execute($sql2);
//            }
//
//    $DB->execute($sql);
//
//    return true;
//}
//return true;
//}
