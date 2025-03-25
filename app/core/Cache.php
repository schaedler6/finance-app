<?php

namespace App\Core;

class Cache
{
    private $cachePath = __DIR__ . '/../../cache/';

    public function set($key, $data, $ttl = 3600)
    {
        $file = $this->cachePath . md5($key) . '.cache';
        $content = json_encode([
            'expiry' => time() + $ttl,
            'data' => $data
        ]);
        file_put_contents($file, $content);
    }

    public function get($key)
    {
        $file = $this->cachePath . md5($key) . '.cache';

        if (!file_exists($file)) {
            return null;
        }

        $content = json_decode(file_get_contents($file), true);

        if ($content['expiry'] < time()) {
            unlink($file);  // Expirou, excluir arquivo
            return null;
        }

        return $content['data'];
    }

    public function delete($key)
    {
        $file = $this->cachePath . md5($key) . '.cache';
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
