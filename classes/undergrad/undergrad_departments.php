<?php
/**
  * Get University data on colleges, departments, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

class undergrad_departments extends undergrad {
  use common;

  public function get_departments() {
    // Query needed to get department information from undergraduate catalog
    // If this no longer returns data, view the XHR data when navigating
    // the undergradaute catalog to find relevant query strings
    $query = array('view_name' => 'departments',
                   'view_display_id' => 'block_1');
    // Get PHP array data from JSON in data
    $departments = json_decode($this->get_data(config::get('undergrad_catalog_url'), $query), true);
    // Write data to file
    $this->write_departments($departments);
  }

  private function write_departments($data) {
    // Grab only the relevant data
    $data = $data[1]['data'];
    // Scrape data for relevant information
    // WARNING: very finicky and highly dependent on code in the
    //          Undergraduate Course Catalog website
    preg_match_all("#<span class=\"field-content\"><a href=\"(.*?)\" rel=\"(.*?)\">(.*?)</a></span>#ui", $data, $department_names);
    // TSV file header
    $departments = "college-stub\tdepartment-stub\tcollege-name-short\tdepartment-name\tdepartment-url\n";
    // Manually add missing departments (departments with only graduate programs)
    $departments.= "law-school\tlaw-school\tLaw School\tLaw School\t/law-school\n";
    // Loop through all departments
    for($i = 0; $i < count($department_names[1]); $i++) {
      $url          = $this->get_clean_data($department_names[1][$i]);
      $college_name = $this->get_clean_data($department_names[2][$i]);
      $name         = $this->get_clean_data($department_names[3][$i]);
      $stubs        = $this->get_stubs($url);
      $departments .= "$stubs\t$college_name\t$name\t$url\n";
    }
    $this->write_file('undergrad_departments.tsv', $departments);
  }

}

?>
