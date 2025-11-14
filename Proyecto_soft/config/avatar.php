<?php

return [
    // Disk used to store and serve profile pictures.
    // Set AVATAR_DISK=public (local) or AVATAR_DISK=s3 (cloud) in .env
    'disk' => env('AVATAR_DISK', 'public'),
    // Folder where profile pictures are stored within the disk
    'folder' => 'profile_pics',
    // Default avatar URL
    'default_url' => 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png',
];
