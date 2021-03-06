<?php

class UsersTest extends \TestCase
{
    const URL = '/api/v1/users/';

    /**
     * Get all users test
     */
    public function testGettingAllUsers()
    {
        // Request without authentication
        $this->get(UsersTest::URL);
        $this->assertResponseStatus(401);

        // Authentication
        $user = factory(\App\User::class)->create();
        $this->actingAs($user);

        // Get all users with the authenticated user
        $this->get(UsersTest::URL);
        $this->assertResponseOk();

        $this->seeJsonStructure([
            '*' => [
                'id', 'name', 'username', 'email', 'experience'
            ]
        ]);

        // Get all users with the authenticated user
        $this->get(UsersTest::URL . '?active=true');
        $this->assertResponseOk();

        $this->seeJsonStructure([
            '*' => [
                'id', 'name', 'cpf', 'is_active'
            ]
        ]);
    }

    /**
     * Get one user test
     */
    public function testGettingSpecificUser()
    {
        // Request without authentication
        $this->get(UsersTest::URL . '1');
        $this->assertResponseStatus(401);

        // Authentication
        $user = factory(\App\User::class)->create();
        $this->actingAs($user);

        // Get one user
        $this->get(UsersTest::URL . '1');
        $this->assertResponseStatus(200);

        $this->seeJsonStructure([
            'id', 'name', 'username', 'email', 'experience'
        ]);

        // Accessing invalid user should give 404
        $this->get(UsersTest::URL . '123456789');
        $this->assertResponseStatus(404);
    }

    /**
     * Get one user profile data test
     */
    public function testGettingSpecificUserProfileData()
    {
        // Request without authentication
        $this->get(UsersTest::URL . '1/profile');
        $this->assertResponseStatus(401);
    }

    /**
     * Get one user profile data test
     */
    public function testGettingOnlineUsers()
    {
        // Request without authentication
        $this->get(UsersTest::URL . 'online');
        $this->assertResponseStatus(401);

        // Authentication
        $user = factory(\App\User::class)->create();
        $this->actingAs($user);

        // Get one user
        $this->get(UsersTest::URL . 'online')->response->getContent();
        $this->assertResponseStatus(200);

        $this->seeJsonStructure([
            '*' => ['id', 'name', 'cpf']
        ]);
    }

    /**
     * Get one user profile data test
     */
    public function testGettingOnlineUsersStats()
    {
        // Request without authentication
        $this->get(UsersTest::URL . 'online/stats');
        $this->assertResponseStatus(401);
    }

    /**
     * Creating user test
     */
    public function testCreatingUser()
    {
        // Valid request
        $this->post(UsersTest::URL, [
            'name' => 'User teste',
            'username' => 'User',
            'email' => 'user@user.com',
            'password' => '123',
            'experience' => 1000,
            'cpf' => '123.123.123-12',
            'birthday' => '08/12/1996'
        ]);
        $this->assertResponseStatus(201);

        // Invalid request - required fields are missing
        $this->post(UsersTest::URL, [
            'name' => 'User teste',
            'username' => 'User',
            'email' => 'user@user.com',
            'experience' => 1000
        ]);
        $this->assertResponseStatus(422);
    }

    /**
     * Creating quick user test
     */
    public function testCreatingQuickUser()
    {
        // Request without authentication
        $this->get(UsersTest::URL . 'quick');
        $this->assertResponseStatus(401);

        // Authentication
        $user = factory(\App\User::class)->create();
        $this->actingAs($user);

        // Invalid request - required fields are missing
        $this->post(UsersTest::URL . 'quick', [
            'name' => 'User teste',
            'email' => 'user_test@user.com'
        ]);
        $this->assertResponseStatus(422);
    }

    /**
     * Updating user test
     */
    public function testUpdatingUser()
    {
        // Request without authentication
        $this->put(UsersTest::URL . '1', [
            'name' => 'User teste',
            'username' => 'User',
            'email' => 'user@user.com',
            'password' => '123',
            'experience' => 1000
        ]);
        $this->assertResponseStatus(401);

        // Authentication
        $user = factory(\App\User::class)->create();
        $this->actingAs($user);

        // Valid request
        $this->put(UsersTest::URL . '1', [
            'name' => 'User teste',
            'username' => 'User',
            'email' => 'user@user.com',
            'password' => '123',
            'experience' => 1000
        ]);
        $this->assertResponseStatus(200);

        // Invalid request - required fields are missing
        $this->put(UsersTest::URL . '1', [
            'name' => 'User teste',
            'email' => 'user@user.com',
            'experience' => 1000
        ]);
        $this->assertResponseStatus(422);

        // Invalid request - user do not exists
        $this->put(UsersTest::URL . '4444', [
            'name' => 'User teste',
            'username' => 'User 123',
            'email' => '123user@user.com',
            'password' => '123',
            'experience' => 1000
        ]);
        $this->assertResponseStatus(404);
    }

    /**
     * Removing user test
     */
    public function testRemovingUser()
    {
        // Request without authentication
        $this->delete(UsersTest::URL . '1');
        $this->assertResponseStatus(401);

        // Authentication
        $user = factory(\App\User::class)->create();
        $this->actingAs($user);

        // Valid request
        $this->delete(UsersTest::URL . '1');
        $this->assertResponseStatus(204);

        // Invalid request - user don't exists
        $this->delete(UsersTest::URL . '4444');
        $this->assertResponseStatus(404);
    }
}
