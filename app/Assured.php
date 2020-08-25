<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Assured extends Model
{
    //protected $connection = 'products_server';

    protected $fillable = [
        'policy_no',
        'policy_type_id',
        'card_no',
        'firstname',
        'middlename',
        'surname',
        'gender',
        'age',
        'birth_date',
        'birth_place',
        'address',
        'city_id',
        'postal_code',
        'contact_no',
        'email',
        'occupation',
        'civil_status',
        'physical_deform',
        'physical_deform_desc',
        'hazard_undertaking',
        'hazard_undertaking_desc',
        'employer',
        'employer_contact_no',
        'employer_address',
        'employer_email',
        'since',
        'no_of_pets',
        'clinic',
        'clinic_contact_no',
        'clinic_address',
        'clinic_email',
        'no_of_children',
        'term_of_pregnancy',
        'type_of_pregnancy',
        'no_of_pregnancy',
        'ob_gyne',
        'ob_contact_no',
        'ob_email',
        'ob_address',
        'sports',
        'club_affiliate',
        'month',
        'option',
        'make',
        'series',
        'year_model',
        'plate_no',
        'mv_file',
        'engine_no',
        'displacement',
        'chassis_no',
        'color',
        'or_no',
        'cr_no',
        'mortgagee',
        'use_for',
        'coverage',
        'net_premium',
        'premium',
        'dst',
        'vat',
        'lgt',
        'misc',
        'med_exp',
        'mediphone',
        'ape',
        'remarks',
        'id_card',
        'agent_id',
        'entity_id',
        'user_id',
        'effectivity_date',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            $model->user_id = Auth::user()->id;
            $model->birth_date = ($model->birth_date ? date("Y-m-d", strtotime($model->birth_date)):null) ;
            // $model->ape_schedule = ($model->ape_schedule ? date("Y-m-d", strtotime($model->ape_schedule)):null) ;
            // $model->ape_date = ($model->ape_date ? date("Y-m-d", strtotime($model->ape_date)):null) ;
        });

        static::updating(function($model)
        {
            //$model->user_id = Auth::user()->id;
            $model->birth_date = ($model->birth_date ? date("Y-m-d", strtotime($model->birth_date)):null) ;
            // $model->ape_schedule = ($model->ape_schedule ? date("Y-m-d", strtotime($model->ape_schedule)):null) ;
            // $model->ape_date = ($model->ape_date ? date("Y-m-d", strtotime($model->ape_date)):null) ;
            $model->policy_no = ($model->policy_no =="null"?null:$model->policy_no);
            $model->policy_type_id = ($model->policy_type_id =="null"?null:$model->policy_type_id);
            $model->card_no = ($model->card_no =="null"?null:$model->card_no);
            $model->firstname = ($model->firstname =="null"?null:$model->firstname);
            $model->middlename = ($model->middlename =="null"?null:$model->middlename);
            $model->surname = ($model->surname =="null"?null:$model->surname);
            $model->gender = ($model->gender =="null"?null:$model->gender);
            $model->age = ($model->age =="null"?null:$model->age);
            $model->birth_place = ($model->birth_place =="null"?null:$model->birth_place);
            $model->address = ($model->address =="null"?null:$model->address);
            $model->city_id = ($model->city_id =="null"?null:$model->city_id);
            $model->postal_code = ($model->postal_code =="null"?null:$model->postal_code);
            $model->contact_no = ($model->contact_no =="null"?null:$model->contact_no);
            $model->email = ($model->email =="null"?null:$model->email);
            $model->occupation = ($model->occupation =="null"?null:$model->occupation);
            $model->civil_status = ($model->civil_status =="null"?null:$model->civil_status);
            $model->physical_deform = ($model->physical_deform =="null"?null:$model->physical_deform);
            $model->physical_deform_desc = ($model->physical_deform_desc =="null"?null:$model->physical_deform_desc);
            $model->hazard_undertaking = ($model->hazard_undertaking =="null"?null:$model->hazard_undertaking);
            $model->hazard_undertaking_desc = ($model->hazard_undertaking_desc =="null"?null:$model->hazard_undertaking_desc);
            $model->employer = ($model->employer =="null"?null:$model->employer);
            $model->employer_contact_no = ($model->employer_contact_no =="null"?null:$model->employer_contact_no);
            $model->employer_address = ($model->employer_address =="null"?null:$model->employer_address);
            $model->employer_email = ($model->employer_email =="null"?null:$model->employer_email);
            $model->since = ($model->since =="null"?null:$model->since);
            $model->no_of_pets = ($model->no_of_pets =="null"?null:$model->no_of_pets);
            $model->clinic = ($model->clinic =="null"?null:$model->clinic);
            $model->clinic_contact_no = ($model->clinic_contact_no =="null"?null:$model->clinic_contact_no);
            $model->clinic_address = ($model->clinic_address =="null"?null:$model->clinic_address);
            $model->clinic_email = ($model->clinic_email =="null"?null:$model->clinic_email);
            $model->no_of_children	 = ($model->no_of_children	 =="null"?null:$model->no_of_children	);
            $model->term_of_pregnancy = ($model->term_of_pregnancy =="null"?null:$model->term_of_pregnancy);
            $model->type_of_pregnancy = ($model->type_of_pregnancy =="null"?null:$model->type_of_pregnancy);
            $model->no_of_pregnancy = ($model->no_of_pregnancy =="null"?null:$model->no_of_pregnancy);
            $model->ob_gyne = ($model->ob_gyne =="null"?null:$model->ob_gyne);
            $model->ob_contact_no = ($model->ob_contact_no =="null"?null:$model->ob_contact_no);
            $model->ob_email = ($model->ob_email =="null"?null:$model->ob_email);
            $model->ob_address = ($model->ob_address =="null"?null:$model->ob_address);
            $model->month = ($model->month =="null"?null:$model->month);
            $model->option = ($model->option =="null"?null:$model->option);
            $model->make = ($model->make =="null"?null:$model->make);
            $model->series = ($model->series =="null"?null:$model->series);
            $model->year_model = ($model->year_model =="null"?null:$model->year_model);
            $model->plate_no = ($model->plate_no =="null"?null:$model->plate_no);
            $model->mv_file = ($model->mv_file =="null"?null:$model->mv_file);
            $model->engine_no = ($model->engine_no =="null"?null:$model->engine_no);
            $model->displacement = ($model->displacement =="null"?null:$model->displacement);
            $model->chassis_no = ($model->chassis_no =="null"?null:$model->chassis_no);
            $model->color = ($model->color =="null"?null:$model->color);
            $model->or_no = ($model->or_no =="null"?null:$model->or_no);
            $model->cr_no = ($model->cr_no =="null"?null:$model->cr_no);
            $model->mortgagee = ($model->mortgagee =="null"?null:$model->mortgagee);
            $model->use_for = ($model->use_for =="null"?null:$model->use_for);
            $model->coverage = ($model->coverage =="null"?null:$model->coverage);
            $model->premium = ($model->premium =="null"?null:$model->premium);
            $model->dst = ($model->dst =="null"?null:$model->dst);
            $model->vat = ($model->vat =="null"?null:$model->vat);
            $model->lgt = ($model->lgt =="null"?null:$model->lgt);
            $model->misc = ($model->misc =="null"?null:$model->misc);
            $model->mediphone = ($model->mediphone =="null"?null:$model->mediphone);
            $model->ape = ($model->ape =="null"?null:$model->ape);
            $model->sports = ($model->sports =="null"?null:$model->sports);
            $model->club_affiliate = ($model->club_affiliate =="null"?null:$model->club_affiliate);
            $model->remarks = ($model->remarks =="null"?null:$model->remarks);
            $model->id_card = ($model->id_card =="null"?null:$model->id_card);
            $model->agent_id = ($model->agent_id =="null"?null:$model->agent_id);
            $model->entity_id = ($model->entity_id =="null"?null:$model->entity_id);
            $model->user_id = ($model->user_id =="null"?null:$model->user_id);
        
        });
    }

    public function getSecurityKey()
    {
        return encrypt($this->attributes['policy_no']);
    }

 
    public function policy_type(){
        return $this->belongsTo('App\PolicyType');
    }

    public function beneficiaries(){
        //return $this->hasMany('App\Beneficiary');//
        return $this->morphMany('App\Beneficiary', 'beneficiaryable');
    }

    public function cards(){
        return $this->belongsTo('App\Card');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function agent(){
        return $this->belongsTo("App\User",'agent_id');
    }

    public function city(){
        return $this->belongsTo("App\City");
    }

    public function entity(){
        return $this->belongsTo("App\Entity");
    }

    public function pets(){
        return $this->morphMany("App\Pet",'petable');
    }

    public function payments(){
        return $this->belongsToMany('App\Payment');
    }

    public function club(){
        return $this->belongsTo("App\Club");
    }
}
