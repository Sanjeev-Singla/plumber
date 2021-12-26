<?php

return [
    'users' =>  [
        'role'  =>  [   
            'super_admin'   =>  0,
            'admin'         =>  1,
            'employee'      =>  2
        ],

        'status'    =>  [
            'disabled'  =>  0,
            'enabled'   =>  1
        ]
    ],

    'vehicles'  =>  [   
        'status'    =>  [
            'disabled'  =>  0,
            'enabled'   =>  1
        ]
    ],

    'projects'  =>  [
        'status'    =>  [
            'disabled'  =>  0,
            'enabled'   =>  1
        ]
    ],
    'materials' =>  [
        'status'    =>  [
            'pending'   =>  0,
            'approved'  =>  1,
            'received'  =>  2,
            'rejected'  =>  3
        ]
    ]
];