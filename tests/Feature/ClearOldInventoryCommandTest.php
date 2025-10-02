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
        // Arrange
        $oldInventory = Inventory::factory(10)->create([
            'last_updated' => Carbon::now()->subDays(100),
        ]);

        $recentInventory = Inventory::factory(20)->create([
            'last_updated' => Carbon::now()->subDays(10),
        ]);

        // Act
        $this->artisan('inventory:clear-old')
            ->expectsOutput('10 registros de estoque antigos foram removidos.')
            ->assertExitCode(0);

        // Assert
        $this->assertDatabaseMissing('inventory', [
            'id' => $oldInventory->first()->id,
        ]);

        $this->assertDatabaseHas('inventory', [
            'id' => $recentInventory->first()->id,
        ]);
    }
}
