<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Member;
use App\Models\Provider;
use Illuminate\Http\Request;
use DB;

class GlobalController extends BaseController
{
	public function index(Request $request)
	{
		$data['ctr_company'] = DB::table('companies')->where('is_archived',0)->count();
		$data['ctr_member'] = DB::table('members')->where('is_archived',0)->count();
		$data['ctr_provider'] = DB::table('providers')->where('is_archived',0)->count();

		return $data;
	}
}