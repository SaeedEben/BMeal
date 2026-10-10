<?php

return [
    'errors' => [
        'auth' => [
            'login_failed'       => 'Invalid credentials.',
            'verify_code_failed' => 'Invalid or expired verification code.',
        ],
        'unauthorized' => 'You are not authorized to perform this action.',
    ],

      // PANEL ----------------------------------------------
      // {User} ----------------------------------------------
    'auth' => [
        'login'    => 'Welcome...',
        'logout'   => 'Logout Successfully.',
        'profile'  => 'User profile data',
        'register' => 'User registered successfully.',
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

      // {Ingredient} ----------------------------------------------
    'ingredients' => [
        'index'   => 'Ingredients list',
        'show'    => 'Ingredient data',
        'store'   => 'Ingredient created successfully.',
        'update'  => 'Ingredient updated successfully.',
        'destroy' => 'Ingredient deleted successfully.',
    ],

      // {Recipe} ----------------------------------------------
    'recipes' => [
        'index'   => 'Recipes list',
        'show'    => 'Recipe data',
        'store'   => 'Recipe created successfully.',
        'update'  => 'Recipe updated successfully.',
        'destroy' => 'Recipe deleted successfully.',
    ],

      // {File} ----------------------------------------------
    'files' => [
        'show'    => 'File data',
        'store'   => 'File created successfully.',
        'destroy' => 'File deleted successfully.',
    ],


      // Api ----------------------------------------------
      // Auth ----------------------------------------------
    'api' => [
        'auth' => [
            'verification' => 'Verification code sent to your email.',
            'verification_success' => 'Email verified successfully.',
            'password_reset_success' => 'Password updated successfully.',
        ]
    ],
];
