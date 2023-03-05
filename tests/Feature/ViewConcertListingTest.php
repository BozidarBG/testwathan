<?php

namespace Tests\Feature;

use Database\Factories\ConcertFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Concert;
use Carbon\Carbon;

class ViewConcertListingTest extends TestCase
{
    //use RefreshDatabase;
    use DatabaseMigrations;

    public function test_user_can_view_published_concert_listing()
    {
        $concert=Concert::factory()->published()->create([
            'title'=>'Amorphis',
            'subtitle'=>'i Sonata Arctica',
            'date'=>Carbon::parse('March 28, 2023 8:00pm'),
            'ticket_price'=>3250,
            'venue'=>'SKC',
            'venue_address'=>'Resavska 1',
            'city'=>'Belgrade',
            'state'=>'ON',
            'zip'=>'11000',
            'additional_information'=>'Za karte pozovite 011/123456',
        ]);

        $this->visit('/concerts/'.$concert->id);

        $this->see('Amorphis');
        $this->see('i Sonata Arctica');
        $this->see('March 28, 2023');
        $this->see('8:00pm');
        $this->see('32.50');
        $this->see('SKC');
        $this->see('Resavska 1');
        $this->see('Belgrade');
        $this->see('ON');
        $this->see('11000');
        $this->see('Za karte pozovite 011/123456');
    }

    public function test_user_cant_see_unpublished_concert(){
        $concert=Concert::factory()->make([
            "published_at"=>null
        ]);

        //ne možemo da koristimo visit nego mora get. jer view pokušava da uradi neku redirekciju pa će
        //redirektovati na neku stranu, a ta strana je 200
        $this->get('/concerts/'.$concert->id);

        $this->assertResponseStatus(404);
    }
    public function test_concerts_with_published_at_are_published(){
        $published_concert_A=Concert::factory()->published()->create();
        $published_concert_B=Concert::factory()->published()->create();
        $unpublished_concert=Concert::factory()->unpublished()->create();

        $published_concerts=Concert::published()->get();

        $this->assertTrue($published_concerts->contains($published_concert_A));
        $this->assertTrue($published_concerts->contains($published_concert_B));
        $this->assertFalse($published_concerts->contains($unpublished_concert));

    }

}
