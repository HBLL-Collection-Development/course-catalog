<?php
/**
  * Get University data on colleges, departments, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

class undergrad_courses extends undergrad {
  use common;

  public function get_courses($page_number = 0) {
    $page_count = 1;
    $header = "college-stub\tdepartment-stub\tprogram-stub\tdepartment-name\tcourse-credit-hours\tcourse-description\tcourse-number\tcourse-name\tcourse-url\n";
    $this->write_file('undergrad_courses.tsv', $header);
    for($t = 0; $t <= $page_count; $t++) {
      $query = array('view_name' => 'courses',
                     'view_display_id' => 'block_1',
                     'page' => $page_number);
      $courses = json_decode($this->get_data($query), true);
      $data = $courses[1]['data'];
      preg_match_all("#.*?<a href=\"(.*?)\" rel=\"(.*?)\">(.*?)-(.*?)</a>#uism", $data, $course_names, PREG_PATTERN_ORDER);
      preg_match_all("#<a title=\"Go to last page\" href=\"/views/ajax\?cd=All&amp;page=(.*?)\">#ui", $data, $pages);
      $page_count = $pages[1][0];
      $this->write_courses($course_names);
      $page_number++;
    }
  }

  private function write_courses($data) {
    $courses = null;
    $num_lines = count($data[1]);
    for($i = 0; $i <= $num_lines; $i++) {
      $url           = $this->get_clean_data($data[1][$i]);
      $description   = $this->get_clean_data($data[2][$i]);
      $course_number = $this->get_clean_data($data[3][$i]);
      $course_name   = $this->get_clean_data($data[4][$i]);
      $description   = explode("\n", $description);
      $department    = $this->get_clean_data($description[0]);
      $credit_hours  = $this->get_clean_data($description[1]);
      $description   = $this->get_clean_data($description[2]);
      // Some courses only have a college and no department so leave
      // department-stub column empty if there are only 2 stubs instead of 3
      $stubs_count   = substr_count($url, '/');
      $stubs         = ($stubs_count == 3) ? $this->get_stubs($url) : str_replace('/', "\t\t", substr($url, 1));
      $courses      .= ($i == $num_lines - 1) ? "$stubs\t$department\t$credit_hours\t$description\t$course_number\t$course_name\t$url" : "$stubs\t$department\t$credit_hours\t$description\t$course_number\t$course_name\t$url\n";
    }
    $this->write_file('undergrad_courses.tsv', $courses, true);
  }

}

?>
