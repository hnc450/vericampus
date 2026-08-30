<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PalmaresRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'palmares_import_id',
        'row_number',
        'raw_data',
        'status',
        'student_id',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'raw_data' => 'array',
        ];
    }

    public function palmaresImport(): BelongsTo
    {
        return $this->belongsTo(PalmaresImport::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
