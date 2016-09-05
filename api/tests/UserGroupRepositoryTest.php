<?php

use App\Models\UserGroup;
use App\Repositories\UserGroupRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserGroupRepositoryTest extends TestCase
{
    use MakeUserGroupTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var UserGroupRepository
     */
    protected $userGroupRepo;

    public function setUp()
    {
        parent::setUp();
        $this->userGroupRepo = App::make(UserGroupRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateUserGroup()
    {
        $userGroup = $this->fakeUserGroupData();
        $createdUserGroup = $this->userGroupRepo->create($userGroup);
        $createdUserGroup = $createdUserGroup->toArray();
        $this->assertArrayHasKey('id', $createdUserGroup);
        $this->assertNotNull($createdUserGroup['id'], 'Created UserGroup must have id specified');
        $this->assertNotNull(UserGroup::find($createdUserGroup['id']), 'UserGroup with given id must be in DB');
        $this->assertModelData($userGroup, $createdUserGroup);
    }

    /**
     * @test read
     */
    public function testReadUserGroup()
    {
        $userGroup = $this->makeUserGroup();
        $dbUserGroup = $this->userGroupRepo->find($userGroup->id);
        $dbUserGroup = $dbUserGroup->toArray();
        $this->assertModelData($userGroup->toArray(), $dbUserGroup);
    }

    /**
     * @test update
     */
    public function testUpdateUserGroup()
    {
        $userGroup = $this->makeUserGroup();
        $fakeUserGroup = $this->fakeUserGroupData();
        $updatedUserGroup = $this->userGroupRepo->update($fakeUserGroup, $userGroup->id);
        $this->assertModelData($fakeUserGroup, $updatedUserGroup->toArray());
        $dbUserGroup = $this->userGroupRepo->find($userGroup->id);
        $this->assertModelData($fakeUserGroup, $dbUserGroup->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteUserGroup()
    {
        $userGroup = $this->makeUserGroup();
        $resp = $this->userGroupRepo->delete($userGroup->id);
        $this->assertTrue($resp);
        $this->assertNull(UserGroup::find($userGroup->id), 'UserGroup should not exist in DB');
    }
}
