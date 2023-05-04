<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Client;

/**
 * @OA\Info(
 *   title="REST API Unicampo",
 *   version="1.0",
 *   description="REST API que realiza a função de inserção de clientes no banco de dados"
 * )
*/

class ClientController extends Controller{
    // função que adiciona a máscara no CPF/CNPJ
    function mask($val, $mask){
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++) {
            if($mask[$i] == '#') {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }
    
    function validarCpfCnpj($cpf_cnpj) {
        // regex remove todos os caracteres que não sejam numéricos
        $cpf_cnpj = preg_replace('/[^0-9]/', '', $cpf_cnpj);
    
        if (strlen($cpf_cnpj) == 11) {
            $tipo_cliente = 'PF';

            // valida o CPF
            $soma = 0;
            for ($i = 0; $i < 9; $i++) {
                $soma += ($cpf_cnpj[$i] * (10 - $i));
            }
            $resto = ($soma % 11);
            if (($resto == 0) || ($resto == 1)) {
                $dv1 = 0;
            } 
            else {
                $dv1 = (11 - $resto);
            }

            $soma = 0;

            for ($i = 0; $i < 9; $i++) {
                $soma += ($cpf_cnpj[$i] * (11 - $i));
            }

            $soma += ($dv1 * 2);
            $resto = ($soma % 11);

            if (($resto == 0) || ($resto == 1)) {
                $dv2 = 0;
            } 
            else {
                $dv2 = (11 - $resto);
            }

            // verifica se os dígitos verificadores estão corretos
            if (($cpf_cnpj[9] == $dv1) && ($cpf_cnpj[10] == $dv2)) {
                $cpf_cnpj = $this->mask($cpf_cnpj, '###.###.###-##');

                return [$cpf_cnpj, $tipo_cliente]; 
            } 
            else {
                return false; 
            }
        }
        elseif (strlen($cpf_cnpj) == 14) {
            $tipo_cliente = 'PJ';

            // valida o CNPJ
            $soma = 0;
            $peso = 5;

            for ($i = 0; $i < 12; $i++) {
                $soma += ($cpf_cnpj[$i] * $peso);
                $peso = ($peso == 2) ? 9 : ($peso - 1);
            }

            $resto = ($soma % 11);

            if (($resto == 0) || ($resto == 1)) {
                $dv1 = 0;
            } 
            else {
                $dv1 = (11 - $resto);
            }
            $soma = 0;
            $peso = 6;
            for ($i = 0; $i < 13; $i++) {
                $soma += ($cpf_cnpj[$i] * $peso);
                $peso = ($peso == 2) ? 9 : ($peso - 1);
            }
            $soma += ($dv1 * 2);
            $resto = ($soma % 11);
            if (($resto == 0) || ($resto == 1)) {
                $dv2 = 0;
            } 
            else {
                $dv2 = (11 - $resto);
            }
            // verifica se os dígitos verificadores estão corretos
            if (($cpf_cnpj[12] == $dv1) && ($cpf_cnpj[13] == $dv2)) {
                $cpf_cnpj = $this->mask($cpf_cnpj, '##.###.###/####-##');

                return [$cpf_cnpj, $tipo_cliente];
            } 
            else {
                return false;
            }
        }
        
        else {
            $tipo_cliente = 'Inválido';
            return false;
        }
    }

    function validarEmail($email) {
        if (preg_match('/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/', $email)) {
            return true;
        } else {
            return false;
        }
    }

    function validarCEP($cep){
        $url = "https://viacep.com.br/ws/$cep/json/";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    /**
 * @OA\Get(
 *     path="/test-unicampo/public/api/status",
 *     summary="Verifica o status da API",
 *     @OA\Response(response="200", description="OK")
 * )
*/
    public function status(){
        return ['status' => 'ok'];
    }

/**
 * @OA\Post(
 *     path="/test-unicampo/public/api/clients/insert",
 *     summary="Insere um novo cliente no banco de dados",
 *     @OA\Parameter(name="nome", in="query", required=true, @OA\Schema(type="string", example="Exemplo")),
 *     @OA\Parameter(name="data_nascimento", in="query", required=true, @OA\Schema(type="string", format="date", example="01/01/1990")),
 *     @OA\Parameter(name="cpf_cnpj", in="query", required=true, @OA\Schema(type="string", example="00000000000")),
 *     @OA\Parameter(name="email", in="query", required=true, @OA\Schema(type="string", example="exemplo@gmail.com")),
 *     @OA\Parameter(name="endereco", in="query", required=true, @OA\Schema(type="string", example="87020010")),
 *     @OA\Parameter(name="localizacao", in="query", required=true, @OA\Schema(type="string", type="string", example="-11.111111,-22.222222")),
 * 
 *     @OA\Response(response="200", description="Cliente inserido com sucesso"),
 *     @OA\Response(response="400", description="Bad request")
 * )
*/
    public function insert(Request $request){
        try{
            $client = new Client();

            $client->nome = $request->nome;
            $client->data_nascimento = $request->data_nascimento;
            $client->tipo_pessoa = '';
            $client->cpf_cnpj = '';
            
            $cpf_cnpj = $request->cpf_cnpj;

            $x = $this->validarCpfCnpj($cpf_cnpj);

            if($x == false){
                return ['error' => 'CPF/CNPJ inválido'];
            }
            else{
                $client->tipo_pessoa = $x[1];
                $client->cpf_cnpj = $x[0];
            }

            if($this->validarEmail($request->email) == false){
                return ['error' => 'E-mail inválido'];
            }
            else{
                $client->email = $request->email;
            }

            $aux = $this->validarCEP($request->endereco);
            
            $client->endereco = $aux['logradouro'] . ', ' . $aux['bairro'] . ', ' . $aux['localidade'] . ', ' . $aux['uf'];
            $client->localizacao = $request->localizacao;

            $client->save();

            return response()->json($client, 200);
        }
        catch(\Exception $error){
            return ['error' => $error->getMessage()];
        }
    }

/**
 * @OA\Get(
 *     path="/test-unicampo/public/api/clients/{id}",
 *     summary="Consulta um cliente pelo ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
 *     @OA\Response(response="200", description="Cliente encontrado"),
 *     @OA\Response(response="404", description="Cliente não encontrado")
 * )
*/

    public function read($id) {
        try{
            $client = Client::find($id);

            if (!$client) {
                return response()->json(['error' => 'Cliente não encontrado'], 404);
            }

            return response()->json($client, 200);
        }

        catch(\Exception $error){
            return ['error' => $error->getMessage()];
        }
    }

/**
 * @OA\Delete(
 *     path="/test-unicampo/public/api/clients/{id}",
 *     summary="Remove um cliente pelo ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
 *     @OA\Response(response="200", description="Cliente removido com sucesso"),
 *     @OA\Response(response="404", description="Cliente não encontrado")
 * )
*/

    public function delete($id) {
        try{
            $client = Client::find($id);

            if (!$client) {
                return response()->json(['error' => 'Cliente não encontrado'], 404);
            }

            $client->delete();

            return response()->json($client, 200);
        }

        catch(\Exception $error){
            return ['error' => $error->getMessage()];
        }
    }

    /**
 * @OA\Put(
 *     path="/test-unicampo/public/api/clients/{id}",
 *     summary="Atualiza um cliente pelo ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
 *     @OA\Parameter(name="nome", in="query", required=false, @OA\Schema(type="string", example="Exemplo")),
 *     @OA\Parameter(name="data_nascimento", in="query", required=false, @OA\Schema(type="string", format="date", example="01/01/1990")),
 *     @OA\Parameter(name="cpf_cnpj", in="query", required=false, @OA\Schema(type="string", example="00000000000")),
 *     @OA\Parameter(name="email", in="query", required=false, @OA\Schema(type="string", example="exemplo@gmail.com")),
 *     @OA\Parameter(name="endereco", in="query", required=false, @OA\Schema(type="string", example="87020010")),
 *     @OA\Parameter(name="localizacao", in="query", required=false, @OA\Schema(type="string", type="string", example="-11.111111,-22.222222")),
 * 
 *     @OA\Response(response="200", description="Cliente atualizado com sucesso"),
 *     @OA\Response(response="400", description="Bad request"),
 *     @OA\Response(response="404", description="Cliente não encontrado")
 * )
*/
    public function update(Request $request, $id){
        try{
            $client = Client::find($id);
            
            if (!$client) {
                return response()->json(['error' => 'Cliente não encontrado'], 404);
            }

            if($request->has('nome')){
                $client->nome = $request->nome;
            }

            if($request->has('data_nascimento')){
                $client->data_nascimento = $request->data_nascimento;
            }

            if($request->has('cpf_cnpj')){
                $cpf_cnpj = $request->cpf_cnpj;

                $x = $this->validarCpfCnpj($cpf_cnpj);

                if($x == false){
                    return ['error' => 'CPF/CNPJ inválido'];
                }
                else{
                    $client->tipo_pessoa = $x[1];
                    $client->cpf_cnpj = $x[0];
                }
            }

            if($request->has('email')){
                if($this->validarEmail($request->email) == false){
                    return ['error' => 'E-mail inválido'];
                }
                else{
                    $client->email = $request->email;
                }
            }

            if($request->has('endereco')){
                $aux = $this->validarCEP($request->endereco);
                
                $client->endereco = $aux['logradouro'] . ', ' . $aux['bairro'] . ', ' . $aux['localidade'] . ', ' . $aux['uf'];
            }

            if($request->has('localizacao')){
                $client->localizacao = $request->localizacao;
            }

            $client->save();

            return response()->json($client, 200);
        }
        catch(\Exception $error){
            return ['error' => $error->getMessage()];
        }
    }
}
