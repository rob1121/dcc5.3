<?php
use App\DCC\External\ExternalSpecRevision;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Request;

class ExternalSpecRevisionTest extends TestCase {
    use DatabaseMigrations, DatabaseTransactions, WithoutMiddleware;

    private $spec;

    protected function setUp() {
        parent::setUp();
        $this->spec = factory(App\CustomerSpec::class)->create();
    }

    /** @test */
    public function it_should_add_data_to_customer_spec_revision() {
        $actual = new Request(factory(App\CustomerSpecRevision::class)->make()->toArray());
        $expected = (new App\CustomerSpecRevision($actual->all()))->toArray();

        (new ExternalSpecRevision($this->spec))->persist($actual);
        $this->seeInDatabase("customer_spec_revisions", $expected);
    }
    
    /** @test */
    public function it_should_update_data_from_customer_spec_revision() {
        $actual = new Request(factory(App\CustomerSpecRevision::class)->make([ "revision" => "**" ])->toArray());
        $this->persistDummyDatabaseDataForRevision($actual);
        $actual["revision"] = "aa";
        $expected = [
            "revision" => "aa",
        ];

        (new ExternalSpecRevision($this->spec))->update($actual);
        $this->seeInDatabase("customer_spec_revisions", $expected);
    }

    private function persistDummyDatabaseDataForRevision($actual)
    {
        $this->spec->customerSpecRevision()->create($actual->all());
    }

}