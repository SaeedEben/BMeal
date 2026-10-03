<?php

return [
    'errors' => [
        'auth' => [
            'login_failed' => 'Invalid credentials.',
        ],
        'unauthorized' => 'You are not authorized to perform this action.',
    ],
    // {User} ----------------------------------------------
    'auth' => [
        'login'   => 'Welcome...',
        'logout'  => 'Logout Successfully.',
        'profile' => 'User profile data',
    ],
    'users' => [
        'index'           => 'Users list',
        'show'            => 'User data',
        'store'           => 'User created successfully.',
        'update'          => 'User updated successfully.',
        'destroy'         => 'User deleted successfully.',
        'change_password' => 'User password changed successfully.',
    ],
    'roles' => [
        'index'   => 'Roles list',
        'show'    => 'Role data',
        'store'   => 'Role created successfully.',
        'update'  => 'Role updated successfully.',
        'destroy' => 'Role deleted successfully.',
    ],
    'permissions' => [
        'index' => 'Permissions list',
    ],

    // {Country} ----------------------------------------------
    'countries' => [
        'index'   => 'Countries list',
        'show'    => 'Country data',
        'store'   => 'Country created successfully.',
        'update'  => 'Country updated successfully.',
        'destroy' => 'Country deleted successfully.',
    ],

    // {Category} ----------------------------------------------
    'categories' => [
        'index'   => 'Categories list',
        'show'    => 'Category data',
        'store'   => 'Category created successfully.',
        'update'  => 'Category updated successfully.',
        'destroy' => 'Category deleted successfully.',
    ],

    // {Tag} ----------------------------------------------
    'tags' => [
        'index'   => 'Tags list',
        'show'    => 'Tag data',
        'store'   => 'Tag created successfully.',
        'update'  => 'Tag updated successfully.',
        'destroy' => 'Tag deleted successfully.',
    ],

    // {Unit} ----------------------------------------------
    'units' => [
        'index'   => 'Units list',
        'show'    => 'Unit data',
        'store'   => 'Unit created successfully.',
        'update'  => 'Unit updated successfully.',
        'destroy' => 'Unit deleted successfully.',
    ],
];
