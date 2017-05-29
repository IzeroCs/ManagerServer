<?php

    return [
        'auto_redirect' => [
            'file_chmod'  => true,
            'file_rename' => true,

            'create_directory' => true,
            'create_file'      => true,

            'create_database' => true,
            'rename_database' => true
        ],

        'enable_disable' => [
            'button_save_on_javascript'       => true,
            'auto_focus_input_last'           => true,
            'count_checkbox_file_javascript'  => true,
            'count_checkbox_mysql_javascript' => true
        ],

        'login' => [
            'enable_forgot_password' => false
        ],

        'paging' => [
            'file_edit_text'      => 30,
            'file_edit_text_line' => 20,
            'file_home_list'      => 10,
            'file_view_zip'       => 0,

            'mysql_list_data' => 5
        ],

        'theme' => [
            'directory' => 'default'
        ]

    ];

?>