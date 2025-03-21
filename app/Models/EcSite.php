<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EcSite extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 有効なECサイトのみを取得するスコープ
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * ECサイトに紐づく注文を取得
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'ec_site_code', 'code');
    }

    /**
     * ECサイトコードで検索
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * ECサイト名で部分一致検索
     */
    public function scopeSearchByName($query, string $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * ECサイトを有効化
     */
    public function activate(): void
    {
        $this->is_active = true;
        $this->save();
    }

    /**
     * ECサイトを無効化
     */
    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
    }

    /**
     * ECサイトが有効かどうかを確認
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
