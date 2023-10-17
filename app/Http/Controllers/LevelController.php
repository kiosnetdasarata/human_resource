<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Commission;

class LevelController extends Controller
{
    public function getLevels()
    {
        return response()->json([
            'status' => 'success',
            'data' => Level::all(),
        ]);
    }

    public function getCommissions($level)
    {
        return response()->json([
            'status' => 'success',
            'data' => Commission::where('level_id', $level),
        ]);
    }
}
