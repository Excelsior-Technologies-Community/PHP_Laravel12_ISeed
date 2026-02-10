<?php

return [
    'path' => database_path('seeders'),
    'chunk_size' => 500, // Maximum number of rows per insert statement
    'exclude' => [], // Tables to exclude from iseed
    'prerun' => '', // Command to run before iseed
    'postrun' => '', // Command to run after iseed
];