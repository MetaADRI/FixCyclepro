<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixcycleSeeder extends Seeder
{
    private $merchantId = 2;
    private $countryId = 2;
    private $now;

    public function run()
    {
        $this->now = now();

        DB::transaction(function () {
            $this->seedMerchant();
            $areaId = $this->seedArea();
            $groupId = $this->seedGroup();

            $ride = $this->seedSegment($groupId, 'Ride', 'TAXI', 3, 1, 1);
            $courier = $this->seedSegment($groupId, 'Courier', 'DELIVERY', 4, 3, 2);

            $rideService = $this->seedService($ride, 'Ride', $areaId);
            $courierService = $this->seedService($courier, 'Courier', $areaId);

            $sedan = $this->seedVehicle('Sedan', 'Comfortable 4-seat ride', 'car-1.png', 1);
            $comfort = $this->seedVehicle('Comfort', 'Extra legroom and boot space', 'taxi.png', 2);
            $bike = $this->seedVehicle('Bike', 'Fast and affordable courier', 'car-2.png', 3);

            $this->seedAreaVehicle($areaId, $sedan, $rideService, $ride);
            $this->seedAreaVehicle($areaId, $comfort, $rideService, $ride);
            $this->seedAreaVehicle($areaId, $bike, $courierService, $courier);

            $params = $this->seedParameters([$ride, $courier]);

            $this->seedPriceCard($areaId, $rideService, $ride, $sedan, 'Sedan - Ride', '25.00', '8.00', '1.50', '60.00', $params);
            $this->seedPriceCard($areaId, $rideService, $ride, $comfort, 'Comfort - Ride', '40.00', '12.00', '2.00', '100.00', $params);
            $this->seedPriceCard($areaId, $courierService, $courier, $bike, 'Bike - Courier', '15.00', '6.00', '1.00', '40.00', $params);

            $this->seedAreaName($areaId);
            $this->seedPaymentMethods($areaId);
        });

        if (!empty($this->command)) {
            $this->command->info('Fixcycle service data seeded (merchant 2, Lusaka).');
        }
    }

    private function seedMerchant()
    {
        DB::table('merchants')->where('id', $this->merchantId)->update([
            'alias_name' => 'fixcycle',
            'updated_at' => $this->now,
        ]);
    }

    private function seedArea()
    {
        $polygon = json_encode([
            ['latitude' => -15.62, 'longitude' => 28.12],
            ['latitude' => -15.62, 'longitude' => 28.55],
            ['latitude' => -15.14, 'longitude' => 28.55],
            ['latitude' => -15.14, 'longitude' => 28.12],
        ]);

        $area = DB::table('country_areas')
            ->where('merchant_id', $this->merchantId)
            ->where('is_geofence', 2)
            ->where('status', 1)
            ->first();

        $values = [
            'country_id' => $this->countryId,
            'is_geofence' => 2,
            'timezone' => 'Africa/Lusaka',
            'minimum_wallet_amount' => '0.00',
            'user_minimum_wallet_amount' => '0.00',
            'email' => 'support@fixcycle.app',
            'whatsapp' => '260970000000',
            'customer_support_number' => '+260970000000',
            'status' => 1,
            'AreaCoordinates' => $polygon,
            'manual_downgradation' => 2,
            'updated_at' => $this->now,
        ];

        if ($area) {
            DB::table('country_areas')->where('id', $area->id)->update($values);
            return $area->id;
        }

        return DB::table('country_areas')->insertGetId(array_merge($values, [
            'merchant_id' => $this->merchantId,
            'created_at' => $this->now,
        ]));
    }

    private function seedGroup()
    {
        $groupId = 1;
        $group = DB::table('segment_groups')->where('id', $groupId)->first();

        if ($group) {
            DB::table('segment_groups')->where('id', $groupId)->update([
                'group_name' => 'Mobility',
                'status' => 1,
                'updated_at' => $this->now,
            ]);
            return $groupId;
        }

        DB::table('segment_groups')->insert([
            'id' => $groupId,
            'group_name' => 'Mobility',
            'status' => 1,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        return $groupId;
    }

    private function seedSegment($groupId, $name, $slag, $subGroupForApp, $subGroupForAdmin, $sequence)
    {
        $segment = DB::table('segments')->where('slag', $slag)->first();

        $values = [
            'name' => $name,
            'slag' => $slag,
            'sub_group_for_app' => $subGroupForApp,
            'sub_group_for_admin' => $subGroupForAdmin,
            'segment_group_id' => $groupId,
            'owner' => 1,
            'updated_at' => $this->now,
        ];

        if ($segment) {
            DB::table('segments')->where('id', $segment->id)->update($values);
            $segmentId = $segment->id;
        } else {
            $segmentId = DB::table('segments')->insertGetId(array_merge($values, ['created_at' => $this->now]));
        }

        DB::table('merchant_segment')->updateOrInsert(
            ['merchant_id' => $this->merchantId, 'segment_id' => $segmentId],
            [
                'sequence' => $sequence,
                'is_coming_soon' => 2,
                'price_card_owner' => 1,
                'request_expire_time' => 120,
            ]
        );

        DB::table('segment_translations')->updateOrInsert(
            ['merchant_id' => $this->merchantId, 'segment_id' => $segmentId, 'locale' => 'en'],
            ['name' => $name, 'created_at' => $this->now, 'updated_at' => $this->now]
        );

        return $segmentId;
    }

    private function seedService($segmentId, $name, $areaId)
    {
        $service = DB::table('service_types')
            ->where('segment_id', $segmentId)
            ->where('serviceName', $name)
            ->first();

        $values = [
            'type' => 1,
            'serviceStatus' => 1,
            'owner' => 2,
            'owner_id' => $this->merchantId,
            'updated_at' => $this->now,
        ];

        if ($service) {
            DB::table('service_types')->where('id', $service->id)->update($values);
            $serviceId = $service->id;
        } else {
            $serviceId = DB::table('service_types')->insertGetId(array_merge($values, [
                'segment_id' => $segmentId,
                'serviceName' => $name,
                'created_at' => $this->now,
            ]));
        }

        DB::table('merchant_service_type')->updateOrInsert(
            ['merchant_id' => $this->merchantId, 'segment_id' => $segmentId, 'service_type_id' => $serviceId],
            ['sequence' => 1]
        );

        DB::table('service_translations')->updateOrInsert(
            ['merchant_id' => $this->merchantId, 'service_type_id' => $serviceId, 'locale' => 'en'],
            ['name' => $name, 'description' => $name . ' service']
        );

        DB::table('country_area_service_type')->updateOrInsert(
            ['country_area_id' => $areaId, 'service_type_id' => $serviceId],
            ['segment_id' => $segmentId]
        );

        return $serviceId;
    }

    private function seedVehicle($name, $description, $image, $sequence)
    {
        $language = DB::table('language_vehicle_types')
            ->where('merchant_id', $this->merchantId)
            ->where('vehicleTypeName', $name)
            ->first();

        if ($language) {
            $vehicleId = $language->vehicle_type_id;
            DB::table('vehicle_types')->where('id', $vehicleId)->update([
                'vehicleTypeImage' => $image,
                'vehicleTypeMapImage' => $image,
                'vehicleTypeRank' => $sequence,
                'vehicleTypeStatus' => 1,
                'sequence' => $sequence,
                'ride_now' => 1,
                'ride_later' => 1,
                'admin_delete' => null,
                'updated_at' => $this->now,
            ]);
            DB::table('language_vehicle_types')->where('id', $language->id)->update([
                'vehicleTypeDescription' => $description,
                'updated_at' => $this->now,
            ]);
            return $vehicleId;
        }

        $vehicleId = DB::table('vehicle_types')->insertGetId([
            'merchant_id' => $this->merchantId,
            'vehicleTypeImage' => $image,
            'vehicleTypeMapImage' => $image,
            'vehicleTypeRank' => $sequence,
            'vehicleTypeStatus' => 1,
            'sequence' => $sequence,
            'ride_now' => 1,
            'ride_later' => 1,
            'in_drive_enable' => 2,
            'pool_enable' => 2,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        DB::table('language_vehicle_types')->insert([
            'merchant_id' => $this->merchantId,
            'vehicle_type_id' => $vehicleId,
            'locale' => 'en',
            'vehicleTypeName' => $name,
            'vehicleTypeDescription' => $description,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        return $vehicleId;
    }

    private function seedAreaVehicle($areaId, $vehicleId, $serviceId, $segmentId)
    {
        DB::table('country_area_vehicle_type')->updateOrInsert(
            [
                'country_area_id' => $areaId,
                'vehicle_type_id' => $vehicleId,
                'service_type_id' => $serviceId,
                'segment_id' => $segmentId,
            ],
            ['status' => 1]
        );
    }

    private function seedParameters(array $segmentIds)
    {
        $types = [
            10 => 'Base Fare',
            1 => 'Distance Fare',
            8 => 'Time Fare',
            16 => 'Minimum Fare',
        ];

        $params = [];
        $sequence = 1;

        foreach ($types as $type => $name) {
            $param = DB::table('pricing_parameters')
                ->where('merchant_id', $this->merchantId)
                ->where('parameterType', $type)
                ->whereNull('deleted_at')
                ->first();

            if ($param) {
                DB::table('pricing_parameters')->where('id', $param->id)->update([
                    'parameterStatus' => 1,
                    'applicable' => 1,
                    'updated_at' => $this->now,
                ]);
                $paramId = $param->id;
            } else {
                $paramId = DB::table('pricing_parameters')->insertGetId([
                    'merchant_id' => $this->merchantId,
                    'sequence_number' => $sequence,
                    'parameterType' => $type,
                    'parameterStatus' => 1,
                    'applicable' => 1,
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ]);
            }

            DB::table('pricing_parameter_values')->updateOrInsert(
                ['pricing_parameter_id' => $paramId, 'price_type' => 1],
                []
            );

            foreach ($segmentIds as $segmentId) {
                DB::table('pricing_parameter_segment')->updateOrInsert(
                    ['pricing_parameter_id' => $paramId, 'segment_id' => $segmentId],
                    []
                );
            }

            DB::table('language_pricing_parameters')->updateOrInsert(
                ['merchant_id' => $this->merchantId, 'pricing_parameter_id' => $paramId, 'locale' => 'en'],
                [
                    'parameterName' => $name,
                    'parameterNameApplication' => $name,
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ]
            );

            $params[$type] = $paramId;
            $sequence++;
        }

        return $params;
    }

    private function seedPriceCard($areaId, $serviceId, $segmentId, $vehicleId, $name, $baseFare, $perKm, $perMinute, $minimumFare, array $params)
    {
        $card = DB::table('price_cards')
            ->where('merchant_id', $this->merchantId)
            ->where('country_area_id', $areaId)
            ->where('service_type_id', $serviceId)
            ->where('vehicle_type_id', $vehicleId)
            ->where('segment_id', $segmentId)
            ->first();

        $values = [
            'pricing_type' => 1,
            'price_card_name' => $name,
            'rate_card_scope' => 1,
            'base_fare' => $baseFare,
            'free_distance' => '2',
            'free_time' => '5',
            'minimum_wallet_amount' => '0.00',
            'cancel_charges' => 0,
            'cancel_amount' => '0.00',
            'additional_stop_charges' => '50.00',
            'status' => 1,
            'updated_at' => $this->now,
        ];

        if ($card) {
            DB::table('price_cards')->where('id', $card->id)->update($values);
            $cardId = $card->id;
        } else {
            $cardId = DB::table('price_cards')->insertGetId(array_merge($values, [
                'merchant_id' => $this->merchantId,
                'country_area_id' => $areaId,
                'service_type_id' => $serviceId,
                'vehicle_type_id' => $vehicleId,
                'segment_id' => $segmentId,
                'created_at' => $this->now,
            ]));
        }

        $cardValues = [
            1 => ['price' => $perKm, 'free' => '2'],
            8 => ['price' => $perMinute, 'free' => '5'],
            16 => ['price' => $minimumFare, 'free' => null],
        ];

        foreach ($cardValues as $type => $value) {
            DB::table('price_card_values')->updateOrInsert(
                ['price_card_id' => $cardId, 'pricing_parameter_id' => $params[$type]],
                [
                    'parameter_price' => $value['price'],
                    'free_value' => $value['free'],
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ]
            );
        }

        return $cardId;
    }

    private function seedPaymentMethods($areaId)
    {
        $methods = [
            1 => ['payment_method' => 'Cash', 'payment_method_type' => 1, 'slug' => 'CASH'],
            2 => ['payment_method' => 'Credit Card / Debit Card', 'payment_method_type' => 2, 'slug' => 'CARD'],
            3 => ['payment_method' => 'Wallet', 'payment_method_type' => 2, 'slug' => 'WALLET'],
            4 => ['payment_method' => 'Online Payment', 'payment_method_type' => 2, 'slug' => 'ONLINE'],
        ];

        foreach ($methods as $id => $method) {
            DB::table('payment_methods')->updateOrInsert(
                ['id' => $id],
                [
                    'payment_method' => $method['payment_method'],
                    'payment_method_type' => $method['payment_method_type'],
                    'payment_method_status' => 1,
                    'slug' => $method['slug'],
                ]
            );

            DB::table('payment_method_translations')->updateOrInsert(
                ['merchant_id' => $this->merchantId, 'payment_method_id' => $id, 'locale' => 'en'],
                ['name' => $method['payment_method']]
            );

            DB::table('merchant_payment_method')->updateOrInsert(
                ['merchant_id' => $this->merchantId, 'payment_method_id' => $id],
                ['icon' => null]
            );

            DB::table('country_area_payment_method')->updateOrInsert(
                ['country_area_id' => $areaId, 'payment_method_id' => $id]
            );
        }
    }

    private function seedAreaName($areaId)
    {
        if (!Schema::hasTable('language_country_areas')) {
            return;
        }

        DB::table('language_country_areas')->updateOrInsert(
            ['merchant_id' => $this->merchantId, 'country_area_id' => $areaId, 'locale' => 'en'],
            ['AreaName' => 'Lusaka', 'created_at' => $this->now, 'updated_at' => $this->now]
        );
    }
}
