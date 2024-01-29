<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_mail_validation(): void
    {
        $email = 'axrcode@gmail.com';
        $response = mail_validation($email);
        $this->assertTrue($response);

        $email = 'axrcode';
        $response = mail_validation($email);
        $this->assertFalse($response);
    }
}
