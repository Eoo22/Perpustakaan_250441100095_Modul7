<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Kolom "role" sengaja TIDAK dimasukkan agar tidak bisa diisi lewat form.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function anggota(): HasOne
    {
        return $this->hasOne(Anggota::class);
    }

    public function riwayatPeminjaman(): HasManyThrough
    {
        return $this->hasManyThrough(
            Peminjaman::class,
            Anggota::class,
            'user_id',
            'anggota_id',
            'id',
            'id'
        );
    }

    /**
     * Ambil data anggota milik user ini. Jika belum ada, hubungkan dengan
     * anggota lama (email sama, belum terhubung) atau buat yang baru.
     */
    public function anggotaAtauBuat(): Anggota
    {
        if ($this->anggota) {
            return $this->anggota;
        }

        $lama = Anggota::where('email', $this->email)->whereNull('user_id')->first();

        if ($lama) {
            $lama->update(['user_id' => $this->id]);
            return $lama;
        }

        return $this->anggota()->create([
            'nama' => $this->name,
            'email' => $this->email,
            'no_telepon' => '-',
        ]);
    }
}