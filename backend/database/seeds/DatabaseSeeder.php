<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedOauthClientAccess();
        $this->seedPrimaryPages();
        $this->seedDevPosition();
        $this->seedDevPositionAccesses(1);
        $this->seedDevAccount();
        $this->seedDummyDataForTesting();

        // $this->call(UsersTableSeeder::class);

        //MODULE SEEDINGS
        $this->seedDefaultBenefitTypes();
        $this->seedDefaultPaymentModes();
        $this->seedDefaultPreExisting();
        $this->seedDefaultPaymentMethod();
        $this->seedDefaultProcedureTypes();
        $this->seedDefaultProcedures();

        // $this->seedDefaultTesting();
    }

    public function seedOauthClientAccess(): void
    {
        if(!DB::table('oauth_clients')->first())
        {
            DB::table('oauth_clients')->insert([
                'name' => 'Laravel Password Grant Client',
                'secret' => 'ZWEWo9Es5jJpmW74pNAdVeoTKJWRKNLzwZovlmRv',
                'redirect' => 'http://localhost	',
                'personal_access_client' => '0',
                'password_client' => '1',
                'revoked' => '0',
            ]);
        }
    }

    public function seedDevPosition(): void
    {
        $exist = DB::table('user_positions')->where('name', 'Developer')->first();

        if(!$exist)
        {
            factory(App\Models\UserPosition::class)->create(
                ['name' => 'Developer']
            );
        }
    }

    public function seedDevPositionAccesses($id): void
    {
        $pages = DB::table('pages')->get();

        $types = ["create", "read", "edit", "archive", "show"];
        

        foreach ($pages as $key => $page)
        {
            $check = DB::table('user_position_accesses')->where('user_position_id', $id)
                                                        ->where('page_id', $page->id)
                                                        ->first();
            if(!$check)
            {
                $data = factory(\App\Models\UserPositionAccess::class)->create([
                    'page_id'               =>  $page->id,
                    'user_position_id'      =>  $id,
                    'code'                  =>  str_replace(" ", "-", strtolower(str_replace("'", "", $page->name))),
                    'types'                 =>  serialize($types),
                ]);
            }
        }
    }

    public function seedDevAccount(): void
    {
        $dev = DB::table('users')->where("email", "digima@gmail.com")->first();

        if(!$dev)
        {
            factory(App\User::class)->create([
                'user_position_id' => 1,
                'first_name' => 'Digima',
                'middle_name' => 'Xs',
                'last_name' => 'Developer',
                'email' => 'digima@gmail.com',
                'password' => Hash::make('water123'),
                'crypt' => Crypt::encrypt('water123'),
                'remember_token' => str_random(10),
                'is_default' => 1,
            ]);
        }
    }

    public function seedPageChildren($arr, $data, $sub = 1)
    {
        if(count($arr) && $data)
        {
            foreach ($arr as $key => $value)
            {
                $check = DB::table('pages')->where('name',$value->name)->first();
                if(!$check)
                {
                    $child = factory(\App\Models\Page::class)->create([
                        'name'          =>  $value->name,
                        'type'          =>  $sub,
                        'icon'          =>  null,
                        'color'         =>  null,
                        'text_color'    =>  $value->text_color,
                        'parent_id'     =>  $data["id"],
                        'url'           =>  $data["url"]. '/' . str_replace(" ", "-", strtolower(str_replace("'", "", $value->name)))
                    ]);

                    $this->seedPageChildren($value->children, $child, $sub + 1);
                }
            }
        }
    }

    public function seedPrimaryPages(): void
    {
        $seed = public_path().'/seeds/default_pages.json';
        $seed = json_decode(file_get_contents($seed));
        // $exist = DB::table('pages')->truncate();

        foreach ($seed->items as $key => $page)
        {
            $primary = 0;
            $check = DB::table('pages')->where('name',$page->name)->first();
            if(!$check)
            {
                $data = factory(\App\Models\Page::class)->create([
                    'name'      =>  $page->name,
                    'type'      =>  $primary,
                    'icon'      =>  $page->icon,
                    'color'     =>  $page->color,
                    'text_color'     =>  $page->text_color,
                    'url'       =>   '/' . str_replace(" ", "-", strtolower(str_replace("'", "", $page->name)))
                ]);

                $this->seedPageChildren($page->children, $data, $primary + 1);
            }
        }
    }

    public function seedDefaultBenefitTypes(): void
    {
        $seed = public_path().'/seeds/default_benefit_types.json';
        $seed = json_decode(file_get_contents($seed));

        foreach ($seed->items as $key => $item)
        {
            $check = DB::table('benefit_types')->where('name', $item->name)->first();
            if(!$check)
            {
                $data = factory(\App\Models\BenefitType::class)->create([
                    'name'      =>  $item->name,
                    'is_default'      =>  1,
                ]);
            }
        }
    }

    public function seedDefaultPaymentModes(): void
    {
        $seed = public_path().'/seeds/default_payment_modes.json';
        $seed = json_decode(file_get_contents($seed));

        foreach ($seed->items as $key => $item)
        {
            $check = DB::table('payment_modes')->where('name', $item->name)->first();
            if(!$check)
            {
                $data = factory(\App\Models\PaymentMode::class)->create([
                    'name'      =>  $item->name,
                    'is_default'      =>  1,
                ]);
            }
        }
    }

    public function seedDefaultPreExisting(): void 
    {
        $seed = public_path().'/seeds/default_pre_existing.json';
        $seed = json_decode(file_get_contents($seed));

        foreach ($seed->items as $key => $item)
        {
            $check = DB::table('pre_existings')->where('name', $item->name)->first();
            if(!$check)
            {
                $data = factory(\App\Models\PreExisting::class)->create([
                    'name'      =>  $item->name,
                    'number'      =>  $item->number,
                    'is_default'      =>  1,
                ]);
            }
        }
    }

    public function seedDefaultPaymentMethod(): void 
    {
        $seed = public_path().'/seeds/default_payment_method.json';
        $seed = json_decode(file_get_contents($seed));

        foreach ($seed->items as $key => $item)
        {
            $check = DB::table('payment_methods')->where('name', $item->name)->first();
            if(!$check)
            {
                $data = factory(\App\Models\PaymentMethod::class)->create([
                    'name'      =>  $item->name,
                    'is_default'      =>  1,
                ]);
            }
        }
    }

    public function seedDefaultProcedureTypes(): void
    {
        $seed = public_path().'/seeds/default_procedure_types.json';
        $seed = json_decode(file_get_contents($seed));

        foreach ($seed->items as $key => $item)
        {
            $check = DB::table('procedure_types')->where('name', $item->name)->first();
            if(!$check)
            {
                $data = factory(\App\Models\ProcedureType::class)->create([
                    'name'      =>  ucfirst($item->name),
                    'is_default'      =>  1,
                ]);
            }
        }
    }

    public function seedDefaultProcedures(): void
    {
        $seed = public_path().'/seeds/default_procedures.json';
        $seed = json_decode(file_get_contents($seed));

        foreach ($seed->items as $key => $item)
        {
            $check = DB::table('procedures')->where('name', ucfirst($item->name))->first();
            if(!$check)
            {
                $data = factory(\App\Models\Procedure::class)->create([
                    'procedure_type_id'     =>  $item->procedure_type_id,
                    'name'                  =>  ucfirst($item->name),
                    'is_default'      =>  1,
                ]);
            }
        }
    }

    public function seedDefaultTesting(): void
    {
        $data = factory(\App\Models\Provider::class)->create([
            'name' => 'digima',
            'rate_rvs' => 2001,
            'tel_number' => '1234567',
            'contact_person' => 'security guard',
            'contact_number' => '163',
            'email' => 'securityguard@gmail.com',
            'address' => 'guard house'
        ]);

        $data = factory(\App\Models\ProviderPayee::class)->create([
            'provider_id' => 1,
            'name' => "payee1"
        ]);

        $data = factory(\App\Models\Doctor::class)->create([
            'first_name' => 'doc',
            'middle_name' => 'doc',
            'last_name' => 'doc',
            'email' => 'doc@doc.com',
            'contact_number' => '123',
            'tin' => '123',
            'tax' => 'VATABLE'
        ]);

        $data = factory(\App\Models\DoctorsProvider::class)->create([
            'provider_id' => 1,
            'doctor_id' => 1
        ]);
    }

    public function seedDummyDataForTesting(): void 
    {
        
        // factory(App\Models\UserPosition::class, 100)->create()->each(function ($position) {
        //     $this->seedDevPositionAccesses($position->id);
        // }); 
        // factory(App\User::class, 10)->create(); 
        // factory(App\Models\DiagnosisList::class, 10)->create(); 
    }
}
