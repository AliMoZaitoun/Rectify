<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $data = json_decode(
            file_get_contents(database_path('data/locations/syria.json')),
            true
        );

        $this->createLocations($data);

        $this->command->info('Locations seeded successfully with hierarchical structure!');
    }

    private function createLocations(array $locations, ?int $parentId = null)
    {
        foreach ($locations as $loc) {
            $location = Location::create([
                'name'      => $loc['name'],
                'type'      => $loc['type'],
                'code'      => $loc['code'] ?? null,
                'order'     => $loc['order'] ?? 0,
                'parent_id' => $parentId,
            ]);

            if (isset($loc['children']) && !empty($loc['children'])) {
                $this->createLocations($loc['children'], $location->id);
            }
        }
    }
}
