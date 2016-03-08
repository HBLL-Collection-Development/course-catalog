<?php
/**
  * Get University data on colleges, departments, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

class grad_faculty extends grad {
  use common;

  public function get_faculty() {
    $this->write_faculty($this->get_data(config::get('graduate_catalog_url')));
  }

  private function write_faculty($data) {
    echo '<pre>';
    // Scrape data for relevant information
    // WARNING: very finicky and highly dependent on code in the
    //          graduate Course Catalog website
    preg_match("#<h2>Departments</h2>.*?<select.*?Select a Department.*?</select>#uism", $data, $departments);
    preg_match_all("#<option value=\"([A-Za-z0-9].*?)::(.*?)\">\s*(.*?)\s*</option>#uism", $departments[0], $department);
    preg_match("#https*://.*?/#ui", config::get('graduate_catalog_url'), $faculty_urls_root);
    // TSV file header
    $faculty_file = "college-stub\tdepartment-stub\tfaculty-stub\tfaculty-name\tfaculty-rank\tdepartment-name\tfaculty-url\tfaculty-degree\tfaculty-school\tfaculty-degree-year\tfaculty-discipline\n";
    for($i = 0; $i < count($department[0]); $i++) {
      $url = $this->get_clean_data($department[2][$i]);
      $url = substr($faculty_urls_root[0], 0, -1) . $url;
      $course_page = $this->get_data($url);
      preg_match("#<link rel=\"shortlink\" href=\"/node/(.*?)\" />#uism", $course_page, $node);
      $faculty_json = $this->get_data('http://graduatestudies.byu.edu/quicktabs/ajax/dep_quicktab/4/view/dep_faculty/default/5/node%252F' . $node[1]);
      $faculty_data = json_decode($faculty_json, true);
      $faculty_data = $faculty_data[1]['data'];
      preg_match_all("#<table.*?>(.*?)</table>#uism", $faculty_data, $department_faculty);
      preg_match_all("#<tr.*?><td.*?>(.*?)</td.*?><td.*?>(.*?)</td.*?><td.*?>(.*?)</td.*?><td.*?>(.*?)</td.*?><td.*?>(.*?)</td.*?>.*?</tr.*?>#uism", $department_faculty[0][0], $faculty_member);
      $department_stub = $this->get_clean_data($department[1][$i]);
      $department_name = $this->get_clean_data($department[3][$i]);
      for($j = 0; $j < count($faculty_member[0]); $j++) {
        $name            = $this->get_clean_data(strip_tags($faculty_member[1][$j]));
        $rank            = $this->get_clean_data(strip_tags($faculty_member[2][$j]));
        $degree          = $this->get_clean_data(strip_tags($faculty_member[3][$j]));
        $school          = $this->get_clean_data(strip_tags($faculty_member[4][$j]));
        $year            = $this->get_clean_data(strip_tags($faculty_member[5][$j]));
        $discipline      = $this->get_clean_data(strip_tags($faculty_member[6][$j]));
        $faculty_file   .= "\t$department_stub\t\t$name\t$rank\t$department_name\t\t$degree\t$school\t$year\t$discipline\n";
      }
    }
    $this->write_file('grad_faculty.tsv', $faculty_file);
  }

}

?>
