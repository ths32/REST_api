<?php

namespace Api\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FlowRate extends Model {
	use SoftDeletes;
	
	public $table = 'flow_rate';
	protected $guarded = [];
	
	public function org() {
		return $this->hasOne('Api\Models\Tbo', 'id', 'tbo');
	}
	
	public function area() {
		return $this->hasOne('Api\Models\Tba', 'id', 'tba');
	}
	
	public function sensor() {
		return $this->hasOne('Api\Models\Tbs', 'imei', 'tbs');
	}
	
	public function irr() {
		return $this->hasOne('Api\Models\Tbi', 'id', 'tbi');
	}

}
