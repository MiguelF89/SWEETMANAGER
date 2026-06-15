<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * CORREÇÃO: Este arquivo estava criando a tabela 'instituicoes' por engano.
     * As migrations seguintes (create_vendas_table, add_user_id_to_all_tables)
     * dependem de 'clientes' existir — sem esta correção, todas as FKs falham.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('contato')->nullable();
            $table->string('cpf')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
