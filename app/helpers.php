<?php
use App\Models\Question;
use App\Models\Category;
use App\Models\Giveaway;
use App\Models\Quiz;
use App\Models\Setting;
use App\Models\User;


if (!function_exists('getQuestionLists')) {
    function getQuestionLists($table_id,$table_type)
    {
       $questionList = Question::with(["answers"])->where('table_id',$table_id)->where('table_type',$table_type)->orderBy("created_at","DESC")->get();

       return $questionList;
    }
}

if (!function_exists('getCategory')) {
    function getCategory()
    {
       $category = Category::orderBy("created_at","DESC")->get();
       return $category;
    }
}

if (!function_exists('getExperts')) {
    function getExpert($string)
    { 
      if(strlen($string) > 150){
         return substr($string, 0,150)."...";
      }
      return $string;
    }
}

if (!function_exists('dateFormat')) {
    function dateFormat($date)
    { 
      return Carbon\Carbon::parse($date)->toFormattedDateString();
    }
}

if (!function_exists('dateTimeFormat')) {
    function dateTimeFormat($date)
    { 
      return Carbon\Carbon::parse($date)->toDayDateTimeString();
    }
}

if (!function_exists('userDateFormat')) {
    function userDateFormat($date)
    { 
      return Carbon\Carbon::parse($date)->toDateString();
    }
}

if (!function_exists('createSlug')) {
  function createSlug($str, $delimiter = '-')
  {
    
      if ($str != '') {
        $unwanted_array = [
          'ś' => 's',
          'ą' => 'a',
          'ć' => 'c',
          'ç' => 'c',
          'ę' => 'e',
          'ł' => 'l',
          'ń' => 'n',
          'ó' => 'o',
          'ź' => 'z',
          'ż' => 'z',
          'Ś' => 's',
          'Ą' => 'a',
          'Ć' => 'c',
          'Ç' => 'c',
          'Ę' => 'e',
          'Ł' => 'l',
          'Ń' => 'n',
          'Ó' => 'o',
          'Ź' => 'z',
          'Ż' => 'z'
        ]; // Polish letters for example
        $str = strtr($str, $unwanted_array);
        $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('utf-8', 'us-ascii//IGNORE', $str))))), $delimiter));
        return $slug;
      }
  }
}

if (!function_exists('questionForGiveawayOrQuiz')) {
    function questionForGiveawayOrQuiz($table_id,$type)
    { 
       if($type == "giveaways"){
           $giveaway = Giveaway::where('id',$table_id)->first();
           if(!empty($giveaway)){
            return $giveaway->name;
           }
       }

       if($type == "quizzes"){
           $giveaway = Quiz::where('id',$table_id)->first();
           if(!empty($giveaway)){
            return $giveaway->name;
           }
       }

    }
}

if (!function_exists('getKeyValue')) {
    function getKeyValue($key)
    {
       $setting = Setting::with('photo')->where('key',$key)->first();
       return $setting;
    }
}

if (!function_exists('blogUser')) {
  function blogUser()
  {
    $users = User::with("roles")
    ->whereHas("roles", function ($q) {
        $q->whereIn("name", ["Content Manager"]);
    })->orderBy('created_at', 'DESC')->get();
    return $users;
  }
}
