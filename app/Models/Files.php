<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Files extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'path',
        'name',
        'size',
        'type',
        'mime_type',
        'ext',
        'is_folder',
        'parent_id',
        'user_id',
    ];

    /**
     * Configura um UUID para cada registro.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(fn(Files $file) => $file->uuid = (string) Str::uuid());
    }

    public function parent()
    {
        return $this->belongsTo(Files::class, 'parent_id');
    }

    /**
     * Configura o caminho do arquivo.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @return void
     */
    public function getPathToRootAttribute()
    {
        $path    = collect();
        $current = $this;

        while ($current->parent) {
            $path->prepend($current->parent);
            $current = $current->parent;
        }

        return $path;
    }

}
