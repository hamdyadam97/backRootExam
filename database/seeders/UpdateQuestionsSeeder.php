<?php
namespace Database\Seeders;

use App\Models\Questionanswers;
use App\Models\Questions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\User;

class UpdateQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $objects=Questionanswers::all();
        foreach ($objects as $key => $object) {
            $object->exam_id=1;
            $object->save();
        }
    }
}
