<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('ID del usuario que ejecutó la acción. Null si es un proceso del sistema o tarea programada.');
            $table->string('action')->comment('Tipo de acción ejecutada. Ej: login, logout, create, update, delete, error, send_order, cancel_order.');
            $table->string('model')->nullable()->comment('Nombre del modelo/entidad afectada. Ej: Pedidos, User, Catalogo, OrdenesTraslado.');
            $table->unsignedBigInteger('model_id')->nullable()->comment('ID del registro afectado en el modelo. Null si la acción no aplica a un registro específico.');
            $table->text('description')->comment('Descripción legible del evento. Ej: El usuario X creó el pedido #5.');
            $table->string('ip_address', 45)->nullable()->comment('Dirección IP del cliente que realizó la acción. Null si es proceso interno.');
            $table->string('user_agent')->nullable()->comment('Navegador o cliente HTTP usado. Null si es proceso interno.');
            $table->json('payload')->nullable()->comment('Datos adicionales en JSON. Ej: campos modificados, request enviado, respuesta recibida.');
            $table->enum('level', ['info', 'warning', 'error', 'critical'])->default('info')->comment('Severidad del evento. info=normal, warning=alerta, error=fallo recuperable, critical=fallo grave.');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['action', 'level']);
            $table->index(['model', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
