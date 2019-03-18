<?php

namespace App\Http\Controllers\Api;

use App\Models\O2oActivity;
use Illuminate\Http\Request;
use App\Transformers\ActivityTransformer;

class ActivitiesController extends Controller
{
    public function index(O2oActivity $activity)
    {
        $query = $activity->query();
        $query->where('is_del', 0);
        $activities = $query->get();
        return $this->response->collection($activities, new ActivityTransformer());
    }
}
