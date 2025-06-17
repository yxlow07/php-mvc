<?php

require_once '../vendor/autoload.php';

$generator = new \core\Database\Generator();

for ($i = 0; $i < 2; $i++) {
    print_r([
        'idMurid' => $generator->id(),
        'noTel' => $generator->phone(),
        'kLMurid' => $generator->password(),
        'infoLogMasuk' => [
            'nama' => $generator->name(),
            'kelas' => $generator->class(),
            'personal_details' => [
                'email' => $generator->email(),
                'birthday' => $generator->date(),
                'address' => $generator->address(),
            ],
            'profile_img' => $generator->image(),
            'status' => $generator->status(),
            'email_notification' => $generator->bool(),
            'lang_preference' => $generator->language(),
            'dark_mode_preference' => $generator->bool(),
            'sessions' => [
                [
                    'ip' => $generator->ip(),
                    'status' => $generator->status(),
                    'info' => $generator->client(),
                    'last_login' => $generator->date(),
                ]
            ],
            'created' => $generator->date(),
            'modified' => $generator->date()
        ]
    ]);
}
