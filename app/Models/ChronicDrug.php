<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChronicDrug extends Model
{
    use HasFactory;
    protected $fillable = ['drug_id' , 'user_id' , 'created_at' , 'updated_at'];

    public function user(){
        $this -> belognsTo(User::class);
    }
}
