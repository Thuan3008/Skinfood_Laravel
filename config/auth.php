<?php

return [
    'defaults' => ['guard' => 'web', 'passwords' => 'users'],
    'guards' => ['web' => ['driver' => 'session', 'provider' => 'accounts']],
    'providers' => ['accounts' => ['driver' => 'eloquent', 'model' => App\Models\TaiKhoan::class]],
    'passwords' => ['users' => ['provider' => 'accounts', 'table' => 'password_reset_tokens', 'expire' => 60, 'throttle' => 60]],
    'password_timeout' => 10800,
];
