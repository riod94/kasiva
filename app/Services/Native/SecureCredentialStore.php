<?php

namespace App\Services\Native;

use Illuminate\Support\Facades\Crypt;

class SecureCredentialStore
{
    public function __construct(private readonly ?string $path = null) {}

    private function file(): string
    {
        return $this->path ?? storage_path('native/credentials.enc');
    }

    public function put(string $key, string $value): void
    {
        $data = $this->all();
        $data[$key] = $value;
        $this->write($data);
    }

    public function get(string $key): ?string
    {
        return $this->all()[$key] ?? null;
    }

    public function forget(string $key): void
    {
        $data = $this->all();
        unset($data[$key]);
        $this->write($data);
    }

    public function putDeviceToken(string $token): void
    {
        $this->put('device_token', $token);
    }

    public function getDeviceToken(): ?string
    {
        return $this->get('device_token');
    }

    private function all(): array
    {
        $f = $this->file();
        if (! file_exists($f)) {
            return [];
        }
        try {
            $raw = file_get_contents($f);
            if (! $raw) {
                return [];
            } $dec = Crypt::decryptString($raw);
            $j = json_decode($dec, true);

            return is_array($j) ? $j : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function write(array $data): void
    {
        $f = $this->file();
        $dir = dirname($f);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $enc = Crypt::encryptString(json_encode($data));
        $tmp = $f.'.tmp';
        file_put_contents($tmp, $enc, LOCK_EX);
        rename($tmp, $f);
    }
}
