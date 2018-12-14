<?php

namespace UserBundle\Utility;

class TokenGenerator
{
    public function generate(int $length = 30): string
    {
        return \rtrim(\strtr(\base64_encode(\random_bytes($length)), '+/', '-_'), '=');
    }
}
