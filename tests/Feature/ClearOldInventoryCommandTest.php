<?php

namespace Tests\Feature;

use App\Models\Inventory;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClearOldInventoryCommandTest extends TestCase
{
     #[Test]
    public function it_deletes_only_inventory_older_than_90_days(): void
    {
        // Creates old record (100 days)
        $oldInventory = Inventory::factory(10)->create([
            'last_updated' => Carbon::now()->subDays(100),
        ]);

        // Creates recent record (10 days)
        $recentInventory = Inventory::factory(20)->create([
            'last_updated' => Carbon::now()->subDays(10),
        ]);

        // Execute the command
        $this->artisan('inventory:clear-old')
            ->expectsOutput('10 registros de estoque antigos foram removidos.')
            ->assertExitCode(0);

        // Verify that the old one has been removed
        $this->assertDatabaseMissing('inventory', [
            'id' => $oldInventory->first()->id,
        ]);

        // Check that the recent continues
        $this->assertDatabaseHas('inventory', [
            'id' => $recentInventory->first()->id,
        ]);
    }
}
