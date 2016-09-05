<?php

use App\Models\UserEmail;
use App\Repositories\UserEmailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserEmailRepositoryTest extends TestCase
{
    use MakeUserEmailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var UserEmailRepository
     */
    protected $userEmailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->userEmailRepo = App::make(UserEmailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateUserEmail()
    {
        $userEmail = $this->fakeUserEmailData();
        $createdUserEmail = $this->userEmailRepo->create($userEmail);
        $createdUserEmail = $createdUserEmail->toArray();
        $this->assertArrayHasKey('id', $createdUserEmail);
        $this->assertNotNull($createdUserEmail['id'], 'Created UserEmail must have id specified');
        $this->assertNotNull(UserEmail::find($createdUserEmail['id']), 'UserEmail with given id must be in DB');
        $this->assertModelData($userEmail, $createdUserEmail);
    }

    /**
     * @test read
     */
    public function testReadUserEmail()
    {
        $userEmail = $this->makeUserEmail();
        $dbUserEmail = $this->userEmailRepo->find($userEmail->id);
        $dbUserEmail = $dbUserEmail->toArray();
        $this->assertModelData($userEmail->toArray(), $dbUserEmail);
    }

    /**
     * @test update
     */
    public function testUpdateUserEmail()
    {
        $userEmail = $this->makeUserEmail();
        $fakeUserEmail = $this->fakeUserEmailData();
        $updatedUserEmail = $this->userEmailRepo->update($fakeUserEmail, $userEmail->id);
        $this->assertModelData($fakeUserEmail, $updatedUserEmail->toArray());
        $dbUserEmail = $this->userEmailRepo->find($userEmail->id);
        $this->assertModelData($fakeUserEmail, $dbUserEmail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteUserEmail()
    {
        $userEmail = $this->makeUserEmail();
        $resp = $this->userEmailRepo->delete($userEmail->id);
        $this->assertTrue($resp);
        $this->assertNull(UserEmail::find($userEmail->id), 'UserEmail should not exist in DB');
    }
}
