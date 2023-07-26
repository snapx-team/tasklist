<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Xguard\Tasklist\Models\Employee;

class CreateAdminTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        Auth::setUser($user);
        $tasklist = factory(Employee::class)->states('admin')->create(['user_id' => $user->id,]);
        session(['role' => 'admin', 'tasklist_id' => $tasklist->id]);
    }

    /**
     * Test create-admin command.
     *
     * @return void
     */
    public function testCreateAdminCommand()
    {
        //Create a user with a valid email.
        $email = 'admin@example.com';
        $user = factory(User::class)->create(['email' => $email]);

        // Act: Call the handle method of the CreateAdmin command.
        $this->artisan('tasklist:create-admin')
            ->expectsQuestion('ERP email:', $email)
            ->assertExitCode(0);

        // Assert: Check if an admin employee was created for the user.
        $this->assertDatabaseHas('tl_employees', [
            'user_id' => $user->id,
            'role' => 'admin',
        ]);
    }
}
