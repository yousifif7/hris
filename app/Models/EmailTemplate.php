<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['slug', 'name', 'subject', 'body'];

    public function render(array $vars): array
    {
        $subject = $this->subject;
        $body    = $this->body;

        foreach ($vars as $key => $val) {
            $subject = str_replace("{{{$key}}}", (string) $val, $subject);
            $body    = str_replace("{{{$key}}}", (string) $val, $body);
        }

        return ['subject' => $subject, 'body' => $body];
    }
}
