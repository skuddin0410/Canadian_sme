<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Question;
use App\Models\Answer;
use Carbon\Carbon;

class QuestionsAnswersImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row)
        {   
            if($row[0] != "question"){
                $question = Question::create(['name' => $row[0]]);
                
                $answer = [
                  [ "name"=>$row[1],"question_id"=>$question->id, "is_correct"=> $row[5] ==1 ? 1:0 ,"created_at" => Carbon::Now(),"updated_at"=>Carbon::Now()],
                  [ "name"=>$row[2],"question_id"=>$question->id, "is_correct"=> $row[5] ==2 ? 1:0 ,"created_at" => Carbon::Now(),"updated_at"=>Carbon::Now() ],
                  [ "name"=>$row[3],"question_id"=>$question->id, "is_correct"=> $row[5] ==3 ? 1:0 ,"created_at" => Carbon::Now(),"updated_at"=>Carbon::Now() ],
                  [ "name"=>$row[4],"question_id"=>$question->id, "is_correct"=> $row[5] ==4 ? 1:0 ,"created_at" => Carbon::Now(),"updated_at"=>Carbon::Now() ]
                ];

                $answer = Answer::insert($answer);
            }
        }

    }
}
