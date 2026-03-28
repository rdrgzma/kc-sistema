<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use Illuminate\Database\Seeder;

class PessoaSeeder extends Seeder
{
    public function run(): void
    {
        $pessoas = [
            [
                'tipo'       => 'PF',
                'nome_razao' => 'João Carlos Ferreira',
                'cpf_cnpj'   => '123.456.789-00',
                'email'      => 'joao.ferreira@email.com',
                'telefone'   => '(51) 98765-4321',
                'cep'        => '90010-000',
                'logradouro' => 'Rua dos Andradas',
                'numero'     => '1234',
                'complemento'=> 'Ap 302',
                'bairro'     => 'Centro',
                'cidade'     => 'Porto Alegre',
                'estado'     => 'RS',
            ],
            [
                'tipo'       => 'PF',
                'nome_razao' => 'Maria Aparecida Lima',
                'cpf_cnpj'   => '987.654.321-00',
                'email'      => 'maria.lima@email.com',
                'telefone'   => '(51) 99112-3344',
                'cep'        => '91740-300',
                'logradouro' => 'Av. Bento Gonçalves',
                'numero'     => '5600',
                'complemento'=> 'Casa 2',
                'bairro'     => 'Agronomia',
                'cidade'     => 'Porto Alegre',
                'estado'     => 'RS',
            ],
            [
                'tipo'       => 'PF',
                'nome_razao' => 'Roberto Souza Neto',
                'cpf_cnpj'   => '321.654.987-11',
                'email'      => 'roberto.neto@gmail.com',
                'telefone'   => '(54) 99234-5566',
                'cep'        => '95020-360',
                'logradouro' => 'Rua Júlio de Castilhos',
                'numero'     => '88',
                'complemento'=> null,
                'bairro'     => 'São Pelegrino',
                'cidade'     => 'Caxias do Sul',
                'estado'     => 'RS',
            ],
            [
                'tipo'       => 'PJ',
                'nome_razao' => 'Transportes Gaúcho Ltda',
                'cpf_cnpj'   => '12.345.678/0001-99',
                'email'      => 'contato@transportesgaucho.com.br',
                'telefone'   => '(51) 3222-4455',
                'cep'        => '92110-000',
                'logradouro' => 'Av. Protásio Alves',
                'numero'     => '3300',
                'complemento'=> 'Sala 801',
                'bairro'     => 'Petrópolis',
                'cidade'     => 'Porto Alegre',
                'estado'     => 'RS',
            ],
            [
                'tipo'       => 'PJ',
                'nome_razao' => 'Comércio e Construções Meridional S/A',
                'cpf_cnpj'   => '98.765.432/0001-11',
                'email'      => 'juridico@meridional.com.br',
                'telefone'   => '(51) 3044-8800',
                'cep'        => '90160-093',
                'logradouro' => 'Av. Carlos Gomes',
                'numero'     => '777',
                'complemento'=> '15º Andar',
                'bairro'     => 'Auxiliadora',
                'cidade'     => 'Porto Alegre',
                'estado'     => 'RS',
            ],
        ];

        foreach ($pessoas as $dados) {
            Pessoa::firstOrCreate(['cpf_cnpj' => $dados['cpf_cnpj']], $dados);
        }
    }
}
