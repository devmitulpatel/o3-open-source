<?php

namespace Laravel\Nova\Tests\Browser;

use Algolia\AlgoliaSearch\PlacesClient;
use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Application;
use Laravel\Dusk\Browser;
use Laravel\Nova\Testing\Browser\Pages\Create;
use Laravel\Nova\Tests\DuskTestCase;

/**
 * @group external-network
 */
class PlaceFieldTest extends DuskTestCase
{
    /**
     * Define environment setup.
     *
     * @param  Application  $app
     *
     * @return void
     */
    protected function defineEnvironment($app)
    {
        if (! class_exists(PlacesClient::class)) {
            $this->markTestSkipped('Missing "algolia/algoliasearch-client-php" requirement.');
        }
    }

    /**
     * @test
     */
    public function resource_can_be_created()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit(new Create('addresses'))
                    ->click('@address_line_1')
                    ->type('@address_line_1', '110 Kingsbrook St Hot Springs')
                    ->pause(6000)
                    ->keys('@address_line_1', '{arrow_down}', '{enter}')
                    ->create()
                    ->pause(2000);

            $address = Address::latest('id')->first();

            $browser->assertPathIs('/nova/resources/addresses/'.$address->id);

            $this->assertEquals('110 Kingsbrook Street', $address->address_line_1);
            $this->assertNull($address->address_line_2);
            $this->assertEquals('Hot Springs', $address->city);
            $this->assertEquals('AR', $address->state);
            $this->assertEquals('71901', $address->postal_code);
            $this->assertEquals('US', $address->country);

            $browser->blank();
        });
    }
}
