<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Assured;
use App\Pet;
use App\Coverage;
use App\Premium;

class AssuredController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entity_id = auth('api')->user()->entity_id;
        
        $assured = Assured::select('policy_no',
                                'firstname',
                                'middlename',
                                'surname',
                                'gender',
                                'civil_status',
                                'birth_date',
                                'email',
                                'contact_no')
                            ->where('entity_id','=',$entity_id)
                            ->latest()
                            ->paginate(15);
        
        
        return $assured;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //return auth('api')->user()->id;
        $request->validate([
            'firstname'=>'required',
            'surname'=>'required',
            'civil_status'=>'required|in:Single,Married,Widowed,Separated',
            'contact_no'=>'required',
            'gender'=>'required|in:Male,Female',
            'email'=>'required|email',
            'birth_date'=>'required|date',
        ]);

        try{
            $assured = new Assured($request->all());

            $assured->policy_type_id = 8;
            $assured->user_id = auth('api')->user()->id;
            $assured->agent_id = auth('api')->user()->id;
            $assured->entity_id = auth('api')->user()->entity_id;
            $assured->policy_no = $this->new_policy_number(8);

            $assured->net_premium = 14.00;
            $assured->dst = 0;
            $assured->vat = 0.28;
            $assured->lgt = 0.028;
            $assured->misc = 5.69;
            $assured->premium = 20.00;

            $assured->save(); 
            return response([
                'message'=>'Record saved.',
                'data'=>[
                            'policy_no'=> $assured['policy_no']
                        ]]);
         }
         catch(\Exception $e){
            return $e->getMessage();
         }
  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_payassured(Request $request)
    {

        $requestData = $request->all();
        $requestData['beneficiaries'] = isset($requestData['beneficiaries'])?json_decode($requestData['beneficiaries'],true):'';
        $request->replace($requestData);

        $request->validate([
            'id_card' => 'required|image',
            'firstname'=>'required',
            'surname'=>'required',
            'civil_status'=>'required|in:Single,Married,Widowed,Separated',
            'contact_no'=>'required',
            'address'=>'required',
            'gender'=>'required|in:Male,Female',
            'email'=>'required|email',
            'birth_date'=>'required|date',
            'beneficiaries' => 'required|array|min:1',
            'beneficiaries.*.first_name' => 'required|distinct',
            'beneficiaries.*.last_name' => 'required',
            'beneficiaries.*.relation' => 'required',
            'employer'=>'required',
            'employer_email'=>'required|email',
            'ape'=>'integer',
        ]);

        try{

            $assured = new Assured($request->all());
            $assured->user_id = auth('api')->user()->id;
            $assured->agent_id = auth('api')->user()->id;
            $assured->entity_id = auth('api')->user()->entity_id;
            $assured->policy_type_id = 1;
            $assured->policy_no = $this->new_policy_number(1);
            $assured->age = floor((time() - strtotime($request->birth_date)) / 31556926);

            if(!$request->ape){
                $assured->premium = 1000;
                $assured->net_premium = 800;
                $assured->med_exp = 0;
            }else{//Payassured plus
                $assured->premium = 2000;
                $assured->net_premium = 1200;
                $assured->med_exp = 500;
            }
    
            $assured->lgt = $assured->net_premium * .005; 
            $assured->vat = $assured->net_premium * .12; 
            $assured->dst = $assured->net_premium * .125;
            $assured->misc = $assured->premium - ($assured->net_premium + $assured->dst + $assured->vat + $assured->lgt + $assured->med_exp);

            //Save id card image
            if($request->hasfile('id_card')) {
                $file = $request->file('id_card');
                //$extension = $file->getClientOriginalExtension();
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move('uploads/id_card/', $filename);
                $assured->id_card = 'uploads/id_card/' . $filename;
            }

            if($assured->save()){

                //Saving Beneficiaries
                if(count($request->beneficiaries)>0){
                    $assured->beneficiaries()->createMany($request->beneficiaries);
                }

                return response([
                    'message'=>'Record saved.',
                    'data'=>[
                                'policy_no'=> $assured['policy_no']
                            ],
                    ]);
            }

            
        }
        catch(\Exception $e){
            return $e->getMessage();
        }
  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_kasambahay(Request $request)
    {

        $requestData = $request->all();
        $requestData['beneficiaries'] = isset($requestData['beneficiaries'])?json_decode($requestData['beneficiaries'],true):'';
        $request->replace($requestData);

        $request->validate([
            'id_card' => 'required|image',
            'firstname'=>'required',
            'surname'=>'required',
            'civil_status'=>'required|in:Single,Married,Widowed,Separated',
            'contact_no'=>'required',
            'address'=>'required',
            'gender'=>'required|in:Male,Female',
            'email'=>'required|email',
            'birth_date'=>'required|date',
            'beneficiaries' => 'required|array|min:1',
            'beneficiaries.*.first_name' => 'required|distinct',
            'beneficiaries.*.last_name' => 'required',
            'beneficiaries.*.relation' => 'required',
            'employer'=>'required',
            'employer_email'=>'required|email',
        ]);

        try{

            $assured = new Assured($request->all());
            $assured->user_id = auth('api')->user()->id;
            $assured->agent_id = auth('api')->user()->id;
            $assured->entity_id = auth('api')->user()->entity_id;
            $assured->policy_type_id = 2;
            $assured->policy_no = $this->new_policy_number(2);
            $assured->age = floor((time() - strtotime($request->birth_date)) / 31556926);

            $assured->premium=1000;
            $assured->net_premium=400;
            $assured->vat=48;
            $assured->dst=50;
            $assured->lgt=2;
            $assured->misc=0;
            $assured->med_exp=500;

            //Save id card image
            if($request->hasfile('id_card')) {
                $file = $request->file('id_card');
                //$extension = $file->getClientOriginalExtension();
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move('uploads/id_card/', $filename);
                $assured->id_card = 'uploads/id_card/' . $filename;
            }

            if($assured->save()){

                //Saving Beneficiaries
                if(count($request->beneficiaries)>0){
                    $assured->beneficiaries()->createMany($request->beneficiaries);
                }

                return response([
                    'message'=>'Record saved.',
                    'data'=>[
                                'policy_no'=> $assured['policy_no']
                            ],
                    ]);
            }

            
        }
        catch(\Exception $e){
            return $e->getMessage();
        }
  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_pet(Request $request)
    {

        $requestData = $request->all();
        $requestData['beneficiaries'] = isset($requestData['beneficiaries'])?json_decode($requestData['beneficiaries'],true):'';
        $requestData['pets'] = isset($requestData['pets'])?json_decode($requestData['pets'],true):'';
        $request->replace($requestData);

        $request->validate([
            'id_card' => 'required|image',
            'firstname'=>'required',
            'surname'=>'required',
            'civil_status'=>'required|in:Single,Married,Widowed,Separated',
            'contact_no'=>'required',
            'address'=>'required',
            'gender'=>'required|in:Male,Female',
            'email'=>'required|email',
            'birth_date'=>'required|date',
            'beneficiaries' => 'required|array|min:1',
            'beneficiaries.*.first_name' => 'required|distinct',
            'beneficiaries.*.last_name' => 'required',
            'beneficiaries.*.relation' => 'required',
            'clinic'=>'required',
            'clinic_email'=>'required|email',
            'pets' => 'required|array|min:1',
            'pets.*.name' => 'required|distinct',
            'pets.*.gender' => 'required|in:Male,Female',
            'pets.*.birthdate' => 'required|date',
            'pets.*.origin' => 'required',
        ]);


        try{

            $assured = new Assured($request->all());
            $assured->user_id = auth('api')->user()->id;
            $assured->agent_id = auth('api')->user()->id;
            $assured->entity_id = auth('api')->user()->entity_id;
            $assured->policy_type_id = 3;
            $assured->policy_no = $this->new_policy_number(3);
            $assured->age = floor((time() - strtotime($request->birth_date)) / 31556926);

            $assured->premium = 1400; //gross
            $assured->net_premium = 920; //net
            
            if(count($request->pets)>0){
                foreach($request->pets as $k=>$v){             

                    $from = \Carbon\Carbon::createFromFormat('Y-m-d', date("Y-m-d"));
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d', $v['birthdate']);
                    $months = $from->diffInMonths($to);
                    $years = $from->diffInYears($to);
                   
                    if($months <= 2){
                        return response([
                            'message'=>'The given data was invalid.',
                            'errors'=>[
                                        'birth_date'=> ["Pet ages 3 months and below is not allowed"]
                                    ]]);
                    }else if($years >= 12){
                        return response([
                            'message'=>'The given data was invalid.',
                            'errors'=>[
                                        'birth_date'=> ["Pet ages 12 year old and above is not allowed"]
                                    ]]);
                    }

                    $assured->premium += 600;
                    $assured->net_premium += 460;

                    //check for surcharges
                    if($years > 8){
                        $assured->premium += 1000;
                    }
                    if(!isset($request->file('documents')[$k])){
                        $assured->premium += 1000;
                    }
                    
                }
            }

            $assured->dst = $assured->net_premium * .125;
            $assured->vat = $assured->net_premium * .12;
            $assured->lgt = $assured->net_premium * .005;
            $assured->misc = $assured->premium - ($assured->net_premium + $assured->dst + $assured->vat + $assured->lgt);

            //Save id card image
            if($request->hasfile('id_card')) {
                $file = $request->file('id_card');
                //$extension = $file->getClientOriginalExtension();
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move('uploads/id_card/', $filename);
                $assured->id_card = 'uploads/id_card/' . $filename;
            }

            if($assured->save()){

                //Saving Beneficiaries
                if(count($request->beneficiaries)>0){
                    $assured->beneficiaries()->createMany($request->beneficiaries);
                }

                //Saving Pets
                if(count($request->pets)>0){
                    foreach($request->pets as $k=>$v){
                        $new_pet = new Pet();
                        $new_pet->name = $v['name'];
                        $new_pet->breed = $v['breed'];
                        $new_pet->gender = $v['gender'];
                        $new_pet->birthdate = $v['birthdate'];
                        $new_pet->age =  floor((time() - strtotime($v['birthdate'])) / 31556926);;
                        $new_pet->origin = $v['origin'];
                        $new_pet->document = isset($request->file('documents')[$k])?$this->save_document($request->file('documents')[$k]):null;
                        $assured->pets()->save($new_pet);
                    }
                }

                return response([
                    'message'=>'Record saved.',
                    'data'=>[
                                'policy_no'=> $assured['policy_no']
                            ],
                    ]);
            }

            
        }
        catch(\Exception $e){
            return $e->getMessage();
        }
  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_mom(Request $request)
    {

        $requestData = $request->all();
        $requestData['beneficiaries'] = isset($requestData['beneficiaries'])?json_decode($requestData['beneficiaries'],true):'';
        $request->replace($requestData);

        $request->validate([
            'id_card' => 'required|image',
            'firstname'=>'required',
            'surname'=>'required',
            'civil_status'=>'required|in:Single,Married,Widowed,Separated',
            'contact_no'=>'required',
            'address'=>'required',
            'email'=>'required|email',
            'birth_date'=>'required|date',
            'beneficiaries' => 'required|array|min:1',
            'beneficiaries.*.first_name' => 'required|distinct',
            'beneficiaries.*.last_name' => 'required',
            'beneficiaries.*.relation' => 'required',
            'ob_gyne'=>'required',
            'ob_email'=>'required|email',
        ]);

        try{

            $assured = new Assured($request->all());
            $assured->user_id = auth('api')->user()->id;
            $assured->agent_id = auth('api')->user()->id;
            $assured->entity_id = auth('api')->user()->entity_id;
            $assured->gender = "Female";
            $assured->policy_type_id = 4;
            $assured->policy_no = $this->new_policy_number(4);
            $assured->age = floor((time() - strtotime($request->birth_date)) / 31556926);

            if($assured->age<= 19 || $assured->age >= 46){
                return response([
                'message'=>'The given data was invalid.',
                'errors'=>[
                            'birth_date'=> ["Ages 19 below and 46 above is not allowed"]
                        ]]);
            }else{
                if($assured->age >= 20 & $assured->age <= 29) {
                    $assured->net_premium = 1400;
                    $assured->premium = 1788;
                    $assured->vat = 168;
                    $assured->dst = 175;
                    $assured->lgt = 7;
                    $assured->misc = 38;
                }else if($age >= 30 & $age <= 39){
                    $assured->net_premium = 1600;
                    $assured->premium = 2088;
                    $assured->vat = 192;
                    $assured->dst = 200;
                    $assured->lgt = 8;
                    $assured->misc = 88;
                }else if($age >= 40 & $age <= 45){
                    $assured->net_premium = 1800;
                    $assured->premium = 2288;
                    $assured->vat = 216;
                    $assured->dst = 225;
                    $assured->lgt = 9;
                    $assured->misc = 38;
                }
            }

            //Save id card image
            if($request->hasfile('id_card')) {
                $file = $request->file('id_card');
                //$extension = $file->getClientOriginalExtension();
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move('uploads/id_card/', $filename);
                $assured->id_card = 'uploads/id_card/' . $filename;
            }

            if($assured->save()){

                //Saving Beneficiaries
                if(count($request->beneficiaries)>0){
                    $assured->beneficiaries()->createMany($request->beneficiaries);
                }

                return response([
                    'message'=>'Record saved.',
                    'data'=>[
                                'policy_no'=> $assured['policy_no']
                            ],
                    ]);
            }

            
        }
        catch(\Exception $e){
            return $e->getMessage();
        }
  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_rider(Request $request)
    {

        $requestData = $request->all();
        $requestData['beneficiaries'] = isset($requestData['beneficiaries'])?json_decode($requestData['beneficiaries'],true):'';
        $request->replace($requestData);

        $request->validate([
            'id_card' => 'required|image',
            'firstname'=>'required',
            'surname'=>'required',
            'civil_status'=>'required|in:Single,Married,Widowed,Separated',
            'contact_no'=>'required',
            'address'=>'required',
            'gender'=>'required|in:Male,Female',
            'email'=>'required|email',
            'birth_date'=>'required|date',
            'beneficiaries' => 'required|array|min:1',
            'beneficiaries.*.first_name' => 'required|distinct',
            'beneficiaries.*.last_name' => 'required',
            'beneficiaries.*.relation' => 'required',
            'make'=>'required',
            'series'=>'required',
            'year_model'=>'required|integer',
            'engine_no'=>'required',
            'chassis_no'=>'required',
            'coverage'=>'required',
        ]);

        try{

            $assured = new Assured($request->all());
            $assured->user_id = auth('api')->user()->id;
            $assured->agent_id = auth('api')->user()->id;
            $assured->entity_id = auth('api')->user()->entity_id;
            $assured->policy_type_id = 5;
            $assured->policy_no = $this->new_policy_number(5);
            $assured->age = floor((time() - strtotime($request->birth_date)) / 31556926);

            $coverage = Coverage::with(['premiums' => function($q) {
                                            $q->where('min_person', 1);
                                        }])
                                    ->where('amount',"=",$assured->coverage)
                                    ->first();
            
            $assured->net_premium = $coverage->premiums[0]->net_premium;
            $assured->premium = $coverage->premiums[0]->gross_premium;
            $assured->lgt = $coverage->premiums[0]->lgt; 
            $assured->vat = $coverage->premiums[0]->vat; 
            $assured->dst = $coverage->premiums[0]->dst;
            $assured->misc = $coverage->premiums[0]->misc;

            //Save id card image
            if($request->hasfile('id_card')) {
                $file = $request->file('id_card');
                //$extension = $file->getClientOriginalExtension();
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move('uploads/id_card/', $filename);
                $assured->id_card = 'uploads/id_card/' . $filename;
            }

            if($assured->save()){

                //Saving Beneficiaries
                if(count($request->beneficiaries)>0){
                    $assured->beneficiaries()->createMany($request->beneficiaries);
                }

                return response([
                    'message'=>'Record saved.',
                    'data'=>[
                                'policy_no'=> $assured['policy_no']
                            ],
                    ]);
            }

            
        }
        catch(\Exception $e){
            return $e->getMessage();
        }
  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_game(Request $request)
    {

        $requestData = $request->all();
        $requestData['beneficiaries'] = isset($requestData['beneficiaries'])?json_decode($requestData['beneficiaries'],true):'';
        $request->replace($requestData);

        $request->validate([
            'id_card' => 'required|image',
            'firstname'=>'required',
            'surname'=>'required',
            'civil_status'=>'required|in:Single,Married,Widowed,Separated',
            'gender'=>'required|in:Male,Female',
            'contact_no'=>'required',
            'address'=>'required',
            'email'=>'required|email',
            'birth_date'=>'required|date',
            'beneficiaries' => 'required|array|min:1',
            'beneficiaries.*.first_name' => 'required|distinct',
            'beneficiaries.*.last_name' => 'required',
            'beneficiaries.*.relation' => 'required',
            'sports'=>'required',
        ]);

        try{

            $assured = new Assured($request->all());
            $assured->user_id = auth('api')->user()->id;
            $assured->agent_id = auth('api')->user()->id;
            $assured->entity_id = auth('api')->user()->entity_id;
            $assured->policy_type_id = 6;
            $assured->policy_no = $this->new_policy_number(6);
            $assured->age = floor((time() - strtotime($request->birth_date)) / 31556926);

            $assured->premium = 1788;
            $assured->vat = 165.6;
            $assured->dst = 6.9;
            $assured->lgt = 172.5;
            $assured->misc = 63;
            $assured->net_premium = 1380;

            //Save id card image
            if($request->hasfile('id_card')) {
                $file = $request->file('id_card');
                //$extension = $file->getClientOriginalExtension();
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move('uploads/id_card/', $filename);
                $assured->id_card = 'uploads/id_card/' . $filename;
            }

            if($assured->save()){

                //Saving Beneficiaries
                if(count($request->beneficiaries)>0){
                    $assured->beneficiaries()->createMany($request->beneficiaries);
                }

                return response([
                    'message'=>'Record saved.',
                    'data'=>[
                                'policy_no'=> $assured['policy_no']
                            ],
                    ]);
            }

            
        }
        catch(\Exception $e){
            return $e->getMessage();
        }
  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_covid(Request $request)
    {

        $requestData = $request->all();
        $requestData['beneficiaries'] = isset($requestData['beneficiaries'])?json_decode($requestData['beneficiaries'],true):'';
        $request->replace($requestData);

        $request->validate([
            'id_card' => 'required|image',
            'firstname'=>'required',
            'surname'=>'required',
            'civil_status'=>'required|in:Single,Married,Widowed,Separated',
            'contact_no'=>'required',
            'address'=>'required',
            'gender'=>'required|in:Male,Female',
            'email'=>'required|email',
            'birth_date'=>'required|date',
            'beneficiaries' => 'required|array|min:1',
            'beneficiaries.*.first_name' => 'required|distinct',
            'beneficiaries.*.last_name' => 'required',
            'beneficiaries.*.relation' => 'required',
            'option'=>'required|in:1,2',
            'month'=>'required|in:2,3,12',
        ]);

        try{

            $assured = new Assured($request->all());
            $assured->user_id = auth('api')->user()->id;
            $assured->agent_id = auth('api')->user()->id;
            $assured->entity_id = auth('api')->user()->entity_id;
            $assured->policy_type_id = 1;
            $assured->policy_no = $this->new_policy_number(7);
            $assured->age = floor((time() - strtotime($request->birth_date)) / 31556926);

            if($assured->month == 2){
                if($assured->option == 1){
                  $assured->premium = 870.00;
                  $assured->net_premium = 696.00;
                }
                else if ($assured->option == 2){
                  $assured->premium = 1163.00;
                  $assured->net_premium = 931.00;
                }
                else{
                  $assured->premium = '';
                }
                $assured->dst = $assured->net_premium * .125;
                $assured->vat = $assured->net_premium * .12;
                $assured->lgt = $assured->net_premium * .005;
              }
              else if($assured->month == 3){
                if($assured->option == 1){
                  $assured->premium = 1087.50;
                  $assured->net_premium = 870.00;
                }
                else if ($assured->option == 2){
                  $assured->premium = 1740.00;
                  $assured->net_premium = 1392.00;
                }
                else{
                  $assured->premium = '';
                }
                $assured->dst = $assured->net_premium * .125;
                $assured->vat = $assured->net_premium * .12;
                $assured->lgt = $assured->net_premium * .005;
              }
              else if ($assured->month == 12){
                  if($assured->option == 1){
                      $assured->premium = 6481.76;
                      $assured->net_premium = 6342.23;
                      $assured->vat = 126.85;
                      $assured->lgt = 12.68;
                      $assured->dst = 0.00;
                  }
                  else if ($assured->option == 2){
                      $assured->premium = 11504.98;
                      $assured->net_premium = 11237.76; 
                      $assured->vat = 224.75;
                      $assured->dst = 20.00;            
                      $assured->lgt = 22.47;    
                  }
              }

            //Save id card image
            if($request->hasfile('id_card')) {
                $file = $request->file('id_card');
                //$extension = $file->getClientOriginalExtension();
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move('uploads/id_card/', $filename);
                $assured->id_card = 'uploads/id_card/' . $filename;
            }

            if($assured->save()){

                //Saving Beneficiaries
                if(count($request->beneficiaries)>0){
                    $assured->beneficiaries()->createMany($request->beneficiaries);
                }

                return response([
                    'message'=>'Record saved.',
                    'data'=>[
                                'policy_no'=> $assured['policy_no']
                            ],
                    ]);
            }

            
        }
        catch(\Exception $e){
            return $e->getMessage();
        }
  
    }

    /**
     * Get auto generated claim number.
     *
     * @return \Illuminate\Http\Response
     */
    public function new_policy_number($policy_type_id)
    {
        $count = Assured::where('policy_type_id','=',$policy_type_id)
                        ->count(DB::raw('DISTINCT policy_no'));

        switch ($policy_type_id) {
            case 1:
                return 'PYA-' . sprintf('%010d', $count + 1);
                break;
            case 2:
                return 'KSB-' . sprintf('%010d', $count + 1);
                break;
            case 3:
                return 'PET-' . sprintf('%010d', $count + 1);
                break;
            case 4:
                return 'MOM-' . sprintf('%010d', $count + 1);
                break;
            case 5:
                return 'RDR-' . sprintf('%010d', $count + 1);
                break;
            case 6:
                return 'GME-' . sprintf('%010d', $count + 1);
                break;
            case 7:
                return 'HCO-' . sprintf('%010d', $count + 1);
                break;
            case 8:
                return 'SQP-' . sprintf('%010d', $count + 1);
                break;
            default:
                return '';
        }

    }  
    
    /**
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function save_document($file)
    {
        if($file) {
            // $extension = $file->getClientOriginalExtension();
            // $filename = time().'.'.$extension;
            $filename = time() .'_'. $file->getClientOriginalName();
            $file->move('uploads/pet_document/', $filename);
            return 'uploads/pet_document/' . $filename;
        }
    }

}
