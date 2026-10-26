<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeederHistory extends Model
{
    protected $fillable = [
        'batch_id',
        'table_name',
        'seeder_name',
        'file_path',
        'records',
        'file_size_kb',
        'status',
        'masking_applied',
        'chunk_size',
        'options',
        'previous_code',
        'execution_time_ms',
    ];

    protected $casts = [
        'options' => 'array',
        'masking_applied' => 'boolean',
        'records' => 'integer',
        'file_size_kb' => 'float',
        'execution_time_ms' => 'integer',
    ];

    public function getSeederFilePath(): string
    {
        return database_path('seeders/' . $this->seeder_name . '.php');
    }

    public function fileExists(): bool
    {
        return file_exists($this->getSeederFilePath());
    }

    public function getCode(): ?string
    {
        $path = $this->getSeederFilePath();
        return file_exists($path) ? file_get_contents($path) : null;
    }
}
