<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Popula o banco de dados com 2 clientes PF e 2 clientes PJ
        for ($i = 0; $i < 2; $i++) {
            $client = new Client();
            $client->nome = "Cliente " . ($i+1);
            $client->data_nascimento = "01/01/1990";
            $client->cpf_cnpj = "123.456.789-00";
            $client->email = "cliente" . ($i+1) . "@exemplo.com";
            $client->endereco = "Rua Exemplo, 123, Bairro Exemplo";
            $client->tipo_pessoa = 'PF';
            $client->localizacao = '-11.111111,-22.222222';

            $client->save();
        }

        for ($i = 2; $i < 4; $i++) {
            $client = new Client();
            $client->nome = "Cliente " . ($i+1);
            $client->data_nascimento = "01/01/1990";
            $client->cpf_cnpj = "12.345.678/0001-00";
            $client->email = "cliente" . ($i+1) . "@exemplo.com";
            $client->endereco = "Rua Exemplo, 123, Bairro Exemplo";
            $client->tipo_pessoa = 'PJ';
            $client->localizacao = '-11.111111,-22.222222';

            $client->save();
        }
    }
}
