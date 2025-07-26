<?php
namespace App\Services\Ajuan;

use Illuminate\Database\Eloquent\Model;

interface AjuanHandlerInterface
{
    public function approve(Model $ajuan): void;
    public function reject(Model $ajuan): void;
}