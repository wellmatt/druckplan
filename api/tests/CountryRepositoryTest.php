<?php

use App\Models\Country;
use App\Repositories\CountryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CountryRepositoryTest extends TestCase
{
    use MakeCountryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CountryRepository
     */
    protected $countryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->countryRepo = App::make(CountryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCountry()
    {
        $country = $this->fakeCountryData();
        $createdCountry = $this->countryRepo->create($country);
        $createdCountry = $createdCountry->toArray();
        $this->assertArrayHasKey('id', $createdCountry);
        $this->assertNotNull($createdCountry['id'], 'Created Country must have id specified');
        $this->assertNotNull(Country::find($createdCountry['id']), 'Country with given id must be in DB');
        $this->assertModelData($country, $createdCountry);
    }

    /**
     * @test read
     */
    public function testReadCountry()
    {
        $country = $this->makeCountry();
        $dbCountry = $this->countryRepo->find($country->id);
        $dbCountry = $dbCountry->toArray();
        $this->assertModelData($country->toArray(), $dbCountry);
    }

    /**
     * @test update
     */
    public function testUpdateCountry()
    {
        $country = $this->makeCountry();
        $fakeCountry = $this->fakeCountryData();
        $updatedCountry = $this->countryRepo->update($fakeCountry, $country->id);
        $this->assertModelData($fakeCountry, $updatedCountry->toArray());
        $dbCountry = $this->countryRepo->find($country->id);
        $this->assertModelData($fakeCountry, $dbCountry->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCountry()
    {
        $country = $this->makeCountry();
        $resp = $this->countryRepo->delete($country->id);
        $this->assertTrue($resp);
        $this->assertNull(Country::find($country->id), 'Country should not exist in DB');
    }
}
