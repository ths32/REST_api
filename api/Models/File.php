<?php

namespace Api\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class File extends Model {
	use SoftDeletes;

	public $table = 'file';
	protected $guarded = [];
}
