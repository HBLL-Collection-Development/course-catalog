<?php
/**
  * Get University data on colleges, departments, programs, and courses
  * http://catalog.byu.edu/
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-02-29
  * @since 2016-02-29
  *
  */

class data {
  private $curl_handle;

  public function __construct() {
    echo '<pre>';
    $this->curl_handle = curl_init();
    // $this->get_colleges();
    // $this->get_departments();
    // $this->get_programs();
    // $this->get_courses();
    // $this->get_faculty();
    $this->get_graduate_school_data();
  }

  // private function get_json($page_name, $page_number = NULL) {
  //   switch ($page_name) {
  //     case 'colleges':    $data_query = $this->colleges_query(); break;
  //     case 'departments': $data_query = $this->departments_query(); break;
  //     case 'programs':    $data_query = $this->programs_query(); break;
  //     case 'courses':     $data_query = $this->courses_query($page_number); break;
  //     case 'faculty':     $data_query = $this->faculty_query($page_number); break;
  //   }
  //   $ch = $this->curl_handle;
  //   curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
  //   curl_setopt($ch, CURLOPT_COOKIEJAR, config::get('cookie_jar'));
  //   curl_setopt($ch, CURLOPT_COOKIEFILE, config::get('cookie_jar'));
  //   curl_setopt($ch, CURLOPT_URL, config::get('undergrad_courses_url'));
  //   curl_setopt($ch, CURLOPT_POST, 1);
  //   curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_query));
  //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //
  //   if(!$result = curl_exec($ch)) {
  //     throw new Exception(curl_errno($ch) . ': ' . curl_error($ch));
  //   } else {
  //     return $result;
  //   }
  // }

  // private function get_html() {
  //   $ch = $this->curl_handle;
  //   curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
  //   curl_setopt($ch, CURLOPT_COOKIEJAR, config::get('cookie_jar'));
  //   curl_setopt($ch, CURLOPT_COOKIEFILE, config::get('cookie_jar'));
  //   curl_setopt($ch, CURLOPT_URL, config::get('graduate_courses_url'));
  //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //
  //   if(!$result = curl_exec($ch)) {
  //     throw new Exception(curl_errno($ch) . ': ' . curl_error($ch));
  //   } else {
  //     return $result;
  //   }
  // }

  private function get_graduate_school_data() {
    $graduate_catalog = $this->get_html();
    preg_match("#<h2>Departments</h2>.*?<select.*?Select a Department.*?</select>#uism", $graduate_catalog, $departments);
    preg_match("#<h2>Programs</h2>.*?<select.*?Select a Program.*?</select>#uism", $graduate_catalog, $programs);
    preg_match("#<h2>Colleges and Schools</h2>.*?<select.*?Select a College.*?</select>#uism", $graduate_catalog, $colleges);
    preg_match_all("#<option value=\"([A-Za-z0-9].*?)::(.*?)\">\s*(.*?)\s*</option>#uism", $departments[0], $department);
    preg_match_all("#<option value=\"([A-Za-z0-9].*?)::(.*?)\">\s*(.*?)\s*</option>#uism", $programs[0], $program);
    preg_match_all("#<option value=\"([A-Za-z0-9].*?)::(.*?)\">\s*(.*?)\s*</option>#uism", $colleges[0], $college);
    $this->get_grad_departments($department);
    $this->get_grad_programs($program);
    $this->get_grad_colleges($college);
  }

  private function get_grad_departments($departments) {
    $i=0;
    foreach($departments[0] as $department) {
      $id = $departments[1][$i];
      $url = $departments[2][$i];
      $name = $departments[3][$i];
      // echo "$id\t$url\t$name\n";
      $i++;
    }
  }

  private function get_grad_programs($programs) {
    $i=0;
    foreach($programs[0] as $program) {
      $id = $programs[1][$i];
      $url = $programs[2][$i];
      $name = $programs[3][$i];
      echo "\t\t$id\t\t\t$name\t$url\n";
      $i++;
    }
  }

  private function get_grad_colleges($colleges) {
    $i=0;
    foreach($colleges[0] as $college) {
      $id = $colleges[1][$i];
      $url = $colleges[2][$i];
      $name = $colleges[3][$i];
      // echo "$id\t$url\t$name\n";
      $i++;
    }
  }

  // private function get_colleges() {
  //   $colleges = json_decode($this->get_json('colleges'), true);
  //   $data = $colleges[1]['data'];
  //   preg_match_all("#<div class=\"field-content\">([A-Z].*?)</div>#ui", $data, $college_names);
  //   preg_match_all("#<span class=\"field-content\"><a href=\"(.*?)\">(.*?)</a></span>#ui", $data, $college_metadata);
  //   preg_match_all("#<span class=\"field-content\">([A-Z].*?)</span>#uism", $data, $college_descriptions);
  //   $college_names          = $college_names[1];
  //   $college_urls           = $college_metadata[1];
  //   $college_informal_names = $college_metadata[2];
  //   $college_descriptions   = $college_descriptions[1];
  //   $i = 0;
  //   foreach($college_names as $college) {
  //     $name          = $this->clean_data($college_names[$i]);
  //     $url           = $this->clean_data($college_urls[$i]);
  //     $stub          = $this->get_stubs($url);
  //     $informal_name = $this->clean_data($college_informal_names[$i]);
  //     $description   = str_replace("\n", '', $this->clean_data($college_descriptions[$i]));
  //     $description   = str_replace("\t", '', $this->clean_data($description));
  //     $college       = $stub . "\t" . $name . "\t" . $informal_name . "\t" . $url . "\t" . $description . "\n";
  //     echo $college;
  //     $i++;
  //   }
  // }
  //
  // private function get_departments() {
  //   $departments = json_decode($this->get_json('departments'), true);
  //   $data = $departments[1]['data'];
  //   preg_match_all("#<span class=\"field-content\"><a href=\"(.*?)\" rel=\"(.*?)\">(.*?)</a></span>#ui", $data, $department_names);
  //   $department_url          = $department_names[1];
  //   $department_college_name = $department_names[2];
  //   $department_name         = $department_names[3];
  //   $i = 0;
  //   foreach($department_names[1] as $department) {
  //     $url          = $this->clean_data($department_url[$i]);
  //     $college_name = $this->clean_data($department_college_name[$i]);
  //     $name         = $this->clean_data($department_name[$i]);
  //     $stubs        = $this->get_stubs($url);
  //     $department   = $stubs . "\t" . $college_name . "\t" . $name . "\t" . $url . "\n";
  //     echo $department;
  //     $i++;
  //   }
  //   // Manually add missing departments
  //   echo "law-school\tlaw-school\tLaw School\tLaw School\t/law-school";
  // }
  //
  // private function get_programs() {
  //   $programs = json_decode($this->get_json('programs'), true);
  //   $data = $programs[1]['data'];
  //   preg_match_all("#<span class=\"field-content\"><a href=\"(.*?)\" rel=\"(.*?) / (.*?)\\n(.*?)\">(.*?)</a></span>#uism", $data, $program_names);
  //   // print_r($program_names);die();
  //   $program_url             = $program_names[1];
  //   $program_college_name    = $program_names[2];
  //   $program_department_name = $program_names[3];
  //   $program_name            = $program_names[5];
  //   $i = 0;
  //   foreach($program_names[1] as $program) {
  //     $url             = $this->clean_data($program_url[$i]);
  //     $college_name    = $this->clean_data($program_college_name[$i]);
  //     $college_name    = $this->clean_data($college_name);
  //     $department_name = $this->clean_data($program_department_name[$i]);
  //     $program_degree  = $this->clean_data($program_name[$i]);
  //     $stubs           = $this->get_stubs($url);
  //     $program_final   = $stubs . "\t" . $college_name . "\t" . $department_name . "\t" . $program_degree . "\t" . $url . "\n";
  //     echo $program_final;
  //     $i++;
  //   }
  // }

  // private function get_courses($page_number = 0) {
  //   $page_count = 1;
  //   for($t = 0; $t <= $page_count; $t++) {
  //     $courses = json_decode($this->get_json('courses', $page_number), true);
  //     $data = $courses[1]['data'];
  //     preg_match_all("#.*?<a href=\"(.*?)\" rel=\"(.*?)\">(.*?)-(.*?)</a>#uism", $data, $matches, PREG_PATTERN_ORDER);
  //     preg_match_all("#<a title=\"Go to last page\" href=\"/views/ajax\?cd=All&amp;page=(.*?)\">#ui", $data, $pages);
  //     $page_count = $pages[1][0];
  //     $i = 0;
  //     foreach($matches[1] as $match) {
  //       $url           = $this->clean_data($matches[1][$i]);
  //       $description   = $this->clean_data($matches[2][$i]);
  //       $course_number = $this->clean_data($matches[3][$i]);
  //       $course_name   = $this->clean_data($matches[4][$i]);
  //       $description   = explode("\n", $description);
  //       $department    = $this->clean_data($description[0]);
  //       $credit_hours  = $this->clean_data($description[1]);
  //       $description   = $this->clean_data($description[2]);
  //       $stubs_count   = substr_count($url, '/');
  //       if($stubs_count == 3) {
  //         $stubs = $this->get_stubs($url);
  //       } else {
  //         $stubs = str_replace('/', "\t\t", substr($url, 1));
  //       }
  //       $course = $stubs . "\t" . $department . "\t" . $credit_hours . "\t" . $description . "\t" . $course_number . "\t" . $course_name . "\t" . $url . "\n";
  //       echo $course;
  //       $i++;
  //     }
  //     $page_number++;
  //   }
  // }

  // private function get_faculty($page_number = 0) {
  //   $page_count = 1;
  //   for($t = 0; $t <= $page_count; $t++) {
  //     $faculty = json_decode($this->get_json('faculty', $page_number), true);
  //     $data = $faculty[1]['data'];
  //     preg_match_all("#<a href=\"(.*?)\">(.*?)</a>.*?<em class=\"field-content\">(.*?)</em>.*?<div class=\"views-field views-field-field-owner\">.*?<div class=\"field-content\">(.*?)</div>#uism", $data, $matches, PREG_PATTERN_ORDER);
  //     preg_match_all("#<li class=\"pager-last last\"><a title=\"Go to last page\" href=\"/views/ajax\?keys=.*?page=(.*?)\">#uism", $data, $pages);
  //     $page_count = $pages[1][0];
  //     $i = 0;
  //     foreach($matches[1] as $match) {
  //       $url        = $this->clean_data($matches[1][$i]);
  //       $name       = $this->clean_data($matches[2][$i]);
  //       $rank       = $this->clean_data($matches[3][$i]);
  //       $department = $this->clean_data($matches[4][$i]);
  //       $stubs_count   = substr_count($url, '/');
  //       if($stubs_count == 3) {
  //         $stubs = $this->get_stubs($url);
  //       } else {
  //         $stubs = str_replace('/', "\t\t", substr($url, 1));
  //       }
  //       $faculty    = $stubs . "\t" . $name . "\t" . $rank . "\t" . $department . "\t" . $url . "\n";
  //       echo $faculty;
  //       $i++;
  //     }
  //     $page_number++;
  //   }
  // }

  // private function clean_data($data) {
  //   $search = array('CREDITS:', 'DESCRIPTION:', '&amp;', '&#039', '&#160;', "';", '&amp;#160;', '&amp;#039;');
  //   $replace = array('', '', '&', "'", ' ', "'", '', "'");
  //   return trim(str_replace($search, $replace, $data));
  // }
  //
  // private function get_stubs($url) {
  //   return str_replace('/', "\t", substr($url, 1));
  // }

  // private function colleges_query() {
  //   return array('view_name' => 'colleges',
  //                'view_display_id' => 'block_1');
  // }
  //
  // private function departments_query() {
  //   return array('view_name' => 'departments',
  //                'view_display_id' => 'block_1');
  // }
  //
  // private function programs_query() {
  //   return array('view_name' => 'programs',
  //                'view_display_id' => 'block_1');
  // }
  //
  // private function courses_query($page_number = 0) {
  //   return array('view_name' => 'courses',
  //                'view_display_id' => 'block_1',
  //                'page' => $page_number);
  // }

  // private function faculty_query($page_number = 0) {
  //   return array('keys' => '',
  //                'view_name' => 'faculty_search',
  //                'view_display_id' => 'block',
  //                'page' => $page_number);
  // }
}
?>
