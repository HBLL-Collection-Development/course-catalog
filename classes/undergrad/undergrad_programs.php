<?php
/**
  * Get University data on colleges, departments, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

class undergrad_programs extends undergrad {
  use common;

  public function get_programs() {
    // Query needed to get department information from undergraduate catalog
    // If this no longer returns data, view the XHR data when navigating
    // the undergradaute catalog to find relevant query strings
    $query = array('view_name' => 'programs',
                   'view_display_id' => 'block_1');
    // Get PHP array data from JSON in data
    $programs = json_decode($this->get_data(config::get('undergrad_catalog_url'), $query), true);
    // Write data to file
    $this->write_programs($programs);
  }

  private function write_programs($data) {
    // Grab only the relevant data
    $data = $data[1]['data'];
    // Scrape data for relevant information
    // WARNING: very finicky and highly dependent on code in the
    //          Undergraduate Course Catalog website
    preg_match_all("#<span class=\"field-content\"><a href=\"(.*?)\" rel=\"(.*?) / (.*?)\\n(.*?)\">(.*?)</a></span>#uism", $data, $program_names);
    // TSV file header
    $programs = "college-stub\tdepartment-stub\tprogram-stub\tcollege-name-short\tdepartment-name\tprogram-name\tprogram-url\n";
    // Loop through all departments
    for($i = 0; $i < count($program_names[1]); $i++) {
      $url                = $this->get_clean_data($program_names[1][$i]);
      $college_name_short = $this->get_clean_data($program_names[2][$i]);
      $department_name    = $this->get_clean_data($program_names[3][$i]);
      $name               = $this->get_clean_data($program_names[5][$i]);
      $stubs              = $this->get_stubs($url);
      $programs          .= "$stubs\t$college_name_short\t$department_name\t$name\t$url\n";
    }
    $this->write_file('undergrad_programs.tsv', $programs);
  }

}

?>
