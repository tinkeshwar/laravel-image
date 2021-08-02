<?php

namespace Tinkeshwar\Imager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'path',
        'image_type',
        'sort_order',
        'alt',
        'image_source'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];


    /**
     * Get status
     *
     */
    public function getStatusAttribute($value) {
        return $value ? 'Active' : 'Inactive';
    }

    /**
     * Get created
     *
     */
    public function getCreatedAttribute() {
        return $this->created_at ? $this->created_at->format('jS M, Y') : '--';
    }

    /**
     * Get modified
     *
     */
    public function getModifiedAttribute() {
        return $this->updated_at ? $this->updated_at->format('jS M, Y') : '--';
    }

    /**
     * Get the owning image model.
     */
    public function imageable() {
        return $this->morphTo();
    }
}
