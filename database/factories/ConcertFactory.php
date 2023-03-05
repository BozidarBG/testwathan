<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConcertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'=>'Metallica',
            'subtitle'=>'i Megadeth',
            'date'=>Carbon::parse('+2 weeks'),
            'ticket_price'=>2000,
            'venue'=>'KST',
            'venue_address'=>'Bulevar neki 12',
            'city'=>'Belgrade',
            'state'=>'ON',
            'zip'=>'11000',
            //'published_at'=>Carbon::parse('-1 weeks'),
            'additional_information'=>'Šaljite mail na gustav@gmail.com'
        ];
    }

    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => Carbon::parse('-1 week')
            ];
        });
    }

    public function unpublished()
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => null
            ];
        });
    }


}
