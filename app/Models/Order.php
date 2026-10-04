<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS = [
        'menunggu_pembayaran' => ['label' => 'Menunggu Pembayaran', 'warna' => 'warning'],
        'menunggu_verifikasi' => ['label' => 'Menunggu Verifikasi', 'warna' => 'info'],
        'dibayar' => ['label' => 'Dibayar', 'warna' => 'primary'],
        'dikirim' => ['label' => 'Dikirim', 'warna' => 'secondary'],
        'selesai' => ['label' => 'Selesai', 'warna' => 'success'],
        'dibatalkan' => ['label' => 'Dibatalkan', 'warna' => 'danger'],
    ];

    protected $fillable = [
        'user_id', 'status', 'total', 'recipient_name', 'phone', 'address',
        'payment_method', 'snap_token', 'payment_receipt', 'is_paid',
    ];

    protected function casts(): array
    {
        return ['is_paid' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS[$this->status]['warna'] ?? 'secondary';
    }
}
