<?php namespace App;

use App\DCC\Traits\ModelInstance;
use App\Dcc\Traits\Presenter\InternalSpecPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CompanySpec extends Model
{
    use ModelInstance, InternalSpecPresenter;

    protected $fillable = [
        'name','spec_no'
    ];

    protected static $logAttributes = [
        'name','spec_no'
    ];

    protected $appends = [
        'spec_name', 'originator_departments', 'spec_id', "internal_show", "internal_destroy", "internal_edit", "revision_summary", "highlight", "cc_email"
    ];

    protected $hidden = [
        'updated_at', 'created_at'
    ];

    public function originator()
    {
        return $this->hasMany(\App\Originator::class);
    }

    public function companySpecRevision()
    {
        return $this->hasOne(CompanySpecRevision::class);
    }

    public function companySpecCategory()
    {
        return $this->hasOne(CompanySpecCategory::class);
    }

    public function cc()
    {
        return $this->hasMany(CC::class);
    }

    /**
     * check if request instance already exist in the database
     *
     * @param $request
     * @return mixed
     */
    public static function isExist(Request $request)
    {
        $spec = self::instance($request);
        return self::where($spec)->first();
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }

    public function setNSpecNoAttribute($value)
    {
        $this->attributes['spec_no'] = sprintf("%03d", $value);
    }

}
