<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\BusinessHour;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateToSaaS extends Command
{
    protected $signature = 'saas:migrate-data';
    protected $description = 'Migrate existing data to the new SaaS architecture.';

    public function handle()
    {
        $this->info('Starting data migration...');

        DB::transaction(function () {
            User::whereNull('business_id')->each(function ($user) {
                $this->line("Processing user: {$user->email}");

                // 1. Create a business for the user
                $business = Business::create([
                    'owner_id' => $user->id,
                    'name' => $user->name . "'s Business",
                    'type' => 'outro', // Default type
                    'phone' => '00000000000', // Placeholder
                    'address' => 'Default Address', // Placeholder
                ]);
                $business->settings()->create(); // Create default settings
                $this->info("  -> Created business #{$business->id} for user #{$user->id}");

                // 2. Assign the user to the new business
                $user->business_id = $business->id;
                $user->role = 'owner';
                $user->is_admin = true;
                $user->save();

                // 3. Migrate appointments and customers
                Appointment::where('user_id', $user->id)->each(function ($appointment) use ($business) {
                    $customer = $business->customers()->firstOrCreate(
                        ['phone' => $appointment->phone],
                        ['name' => $appointment->customer_name]
                    );

                    $appointment->update([
                        'business_id' => $business->id,
                        'customer_id' => $customer->id,
                    ]);
                });
                $this->info("  -> Migrated appointments and customers for user #{$user->id}");

                // 4. Migrate business hours
                BusinessHour::where('user_id', $user->id)->update(['business_id' => $business->id]);
                $this->info("  -> Migrated business hours for user #{$user->id}");
            });
        });

        $this->info('Data migration completed successfully!');
    }
}
