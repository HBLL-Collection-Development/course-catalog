<?php
/**
  * Get University data on colleges, programs, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

class grad_programs extends grad {
  use common;

  public function get_programs() {
    $this->write_programs($this->graduate_catalog);
  }

  private function write_programs($data) {
    // Scrape data for relevant information
    // WARNING: very finicky and highly dependent on code in the
    //          graduate Course Catalog website
    preg_match("#<h2>Programs</h2>.*?<select.*?Select a Program.*?</select>#uism", $graduate_catalog, $programs);
    preg_match_all("#<option value=\"([A-Za-z0-9].*?)::(.*?)\">\s*(.*?)\s*</option>#uism", $programs[0], $program);
    // TSV file header
    $programs_file = "college-stub\tdepartment-stub\tprogram-stub\tcollege-name-short\tprogram-name\tprogram-url\n";
    for($i = 0; $i < count($program[0]); $i++) {
      $id                = $this->get_clean_data($program[1][$i]);
      $url               = $this->get_clean_data($program[2][$i]);
      $name              = $this->get_clean_data($program[3][$i]);
      $programs_file .= "\t\t$id\t\t$name\t$url\n";
    }
    $this->write_file('grad_programs.tsv', $programs_file);
  }

}

?>
