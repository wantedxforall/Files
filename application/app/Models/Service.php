<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusBadge($status){
        $html = '';
        if($this->status == 1){
            $html = '<span class="badge badge--success">'.trans('Active').'</span>';
        }elseif($this->status == 0){
            $html = '<span class="badge badge--warning">'.trans('Inactive').'</span>';
        }else{
            $html = '<span class="badge badge--danger">'.trans('Ban').'</span>';
        }

        return $html;
    }
}
