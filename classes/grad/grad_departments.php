<?php
/**
  * Get University data on colleges, departments, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

class grad_departments extends grad {
  use common;

  public function get_departments() {
    $this->write_departments($this->get_data(config::get('graduate_catalog_url')));
  }

  private function write_departments($data) {
    // Scrape data for relevant information
    // WARNING: very finicky and highly dependent on code in the
    //          graduate Course Catalog website
    preg_match("#<h2>Departments</h2>.*?<select.*?Select a Department.*?</select>#uism", $data, $departments);
    preg_match_all("#<option value=\"([A-Za-z0-9].*?)::(.*?)\">\s*(.*?)\s*</option>#uism", $departments[0], $department);
    // TSV file header
    $departments_file = "college-stub\tdepartments-stub\tcollege-name-short\tdepartment-name\tdepartment-url\n";
    for($i = 0; $i < count($department[0]); $i++) {
      $id                = $this->get_clean_data($department[1][$i]);
      $url               = $this->get_clean_data($department[2][$i]);
      $name              = $this->get_clean_data($department[3][$i]);
      $departments_file .= "\t$id\t\t$name\t$url\n";
    }
    $this->write_file('grad_departments.tsv', $departments_file);
  }

}

?>
