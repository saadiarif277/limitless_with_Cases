<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LawFirm extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'law_firms';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'law_firm_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address_line_1',
        'address_line_2',
        'city',
        'state_id',
        'zip_code',
    ];

    /**
     * Get the state that the law firm belongs to.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    /**
     * Get the user that the law firm belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
