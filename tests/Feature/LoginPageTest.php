<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginPageTest extends TestCase
{
    public function test_demo_credentials_are_not_rendered_on_login_page(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertDontSee('Akun Demo')
            ->assertDontSee('admin@dthree.id')
            ->assertDontSee('Password:')
            ->assertDontSee("pwEl.value = 'password'", false)
            ->assertDontSee('fillDemo(', false);
    }
}
