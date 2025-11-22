<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../functions/user_functions.php'; // fungsi login dan validasi user

class UserTest extends TestCase
{
    // a. UnitTest validasi akun login
    public function testLoginValidationSuccess()
    {
        $result = validateLogin('fafa', '123'); // username & password benar
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('username', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('role', $result);
    }

    public function testLoginValidationFail()
    {
        $result = validateLogin('admin', 'salahpassword'); // username & password benar
        $this->assertFalse($result);
    }

    // b. UnitTest validasi input data user
    public function testUserInputValidationSuccess()
    {
        $user = [
            'username' => 'admin@admin.com',
            'email' => 'admin@admin.com',
            'password' => '123',
            'role' => 'admin'
        ];
        $result = validateUserInput($user);
        $this->assertTrue($result); // Perbaikan: validasi hasil true
    }

    public function testUserInputValidationFailEmptyUsername()
    {
        $user = [
            'username' => '',
            'email' => 'user@mail.com',
            'password' => 'pass',
            'role' => 'admin'
        ];
        $result = validateUserInput($user);
        $this->assertFalse($result);
    }

    public function testUserInputValidationFailInvalidEmail()
    {
        $user = [
            'username' => 'user',
            'email' => 'salah-email',
            'password' => 'pass',
            'role' => 'user'
        ];
        $result = validateUserInput($user);
        $this->assertFalse($result);
    }
}