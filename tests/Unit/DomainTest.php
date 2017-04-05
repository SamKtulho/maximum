<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Domain;

class DomainTest extends TestCase
{
    use DatabaseMigrations;

    public function testADomainCanBeCreated()
    {
        $testData = ['domain' => 'test.com', 'tic' => 10, 'type' => Domain::TYPE_EMAIL, 'status' => Domain::STATUS_MODERATE, 'source' => '+100500'];
        $domain = new Domain();
        foreach ($testData as $prop => $value) {
            $domain->$prop = $value;
        }
        $domain->save();

        $latestDomain = Domain::latest()->first();

        $this->assertEquals($domain->id, $latestDomain->id);
        $this->assertEquals('test.com', $latestDomain->domain);
        $this->assertEquals(Domain::TYPE_EMAIL, $latestDomain->type);
        $this->assertEquals(Domain::STATUS_MODERATE, $latestDomain->status);
        $this->assertEquals('+100500', $latestDomain->source);

        $this->assertDatabaseHas('domains', $testData);
    }
}
