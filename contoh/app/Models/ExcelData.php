<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelData extends Model
{
    use HasFactory;

    protected $fillable = ['id_dipotong', 'pdf_name'];

    public function pdf()
    {
        return $this->belongsTo(Pdf::class, 'pdf_name', 'name');
    }
}
