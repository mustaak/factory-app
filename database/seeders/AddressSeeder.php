<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        // Jis user ke liye addresses chahiye, uska email yahan likhiye
        $user = User::where('email', 'admin@gmail.com')->firstOrFail();

        // Purane test addresses hatane ke liye (optional)
        Address::where('user_id', $user->id)->delete();

        $addresses = [
            [
                'label' => 'Home',
                'name' => 'Rahul Sharma',
                'phone' => '9876543210',
                'address_line1' => '12, Green Park Colony',
                'address_line2' => 'Near City Mall',
                'city' => 'Sangrur',
                'state' => 'Punjab',
                'postal_code' => '148001',
                'country' => 'IN',
                'is_default' => true,
            ],
            [
                'label' => 'Office',
                'name' => 'Priya Verma',
                'phone' => '9123456780',
                'address_line1' => 'Plot 45, Sector 17',
                'address_line2' => null,
                'city' => 'Chandigarh',
                'state' => 'Chandigarh',
                'postal_code' => '160017',
                'country' => 'IN',
                'is_default' => false,
            ],
            [
                'label' => 'Parents',
                'name' => 'Amit Singh',
                'phone' => '9988776655',
                'address_line1' => '7, Model Town Road',
                'address_line2' => 'Opposite Civil Hospital',
                'city' => 'Ludhiana',
                'state' => 'Punjab',
                'postal_code' => '141002',
                'country' => 'IN',
                'is_default' => false,
            ],
        ];

        foreach ($addresses as $data) {
            Address::create($data + ['user_id' => $user->id]);
        }
    }
}