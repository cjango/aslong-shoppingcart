<?php

return [

    /**
     * 关联用户模型
     */
    'user_model' => App\Models\User::class,

    /**
     * 货币单位 格式化
     */
    'format'     => [
        'decimals'           => 2,
        'decimal_point'      => '.',
        'thousand_seperator' => ',',
    ],
];
