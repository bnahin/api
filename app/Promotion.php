<?php namespace App;
use App\Helpers\RatingHelper;

/**
 * Class Promotion
 * @package App
 *
 * @SWG\Definition(
 *     type="object",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="cid", type="integer"),
 *     @SWG\Property(property="grantor", type="integer", description="CID of grantor, 11111 = system generated or conducted outside of VATUSA"),
 *     @SWG\Property(property="to", type="integer", description="Rating based off array where 1=OBS, S1, S2, S3, C1, C2, C3, I1, I2, I3, SUP, ADM"),
 *     @SWG\Property(property="from", type="integer", description="Rating based off array where 1=OBS, S1, S2, S3, C1, C2, C3, I1, I2, I3, SUP, ADM"),
 *     @SWG\Property(property="created_at", type="string", description="Date rating issued"),
 *     @SWG\Property(property="exam", type="string", description="Date of exam"),
 *     @SWG\Property(property="examiner", type="integer", description="CERT ID of examiner"),
 *     @SWG\Property(property="position", type="string", description="Position worked"),
 * )
 */
class Promotion extends Model {
    protected $table = 'promotions';
    protected $hidden = ['updated_at'];

    public function User() {
        $this->belongsTo('\App\User', 'cid', 'cid');
    }

    public static function process($cid, $grantor, $to, $from = null, $exam = "0000-00-00 00:00:00", $examiner = 0, $position = "n/a") {
        $p = new Promotion();
        $p->cid = $cid;
        $p->grantor = $grantor;
        $p->from = (!$from) ? User::find($cid)->rating : $from;
        $p->to = $to;
        $p->exam = $exam;
        $p->examiner = ($examiner>0) ? $examiner : $grantor;
        $p->position = $position;
        $p->save();

        return $p;
    }
}

