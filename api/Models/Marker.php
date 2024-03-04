<?php

namespace Api\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Marker extends Model {
	use SoftDeletes;
	
	public $table = 'marker';
	protected $guarded = [];
}
