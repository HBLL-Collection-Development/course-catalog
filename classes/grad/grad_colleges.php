<?php
/**
  * Get University data on colleges, departments, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

class grad_colleges extends grad {
  use common;

  public function get_colleges() {
    $this->write_colleges($this->graduate_catalog);
  }

  private function write_colleges($data) {
    // Scrape data for relevant information
    // WARNING: very finicky and highly dependent on code in the
    //          graduate Course Catalog website
    preg_match("#<h2>Colleges and Schools</h2>.*?<select.*?Select a College.*?</select>#uism", $data, $colleges);
    preg_match_all("#<option value=\"([A-Za-z0-9].*?)::(.*?)\">\s*(.*?)\s*</option>#uism", $colleges[0], $college);
    // TSV file header
    $colleges_file = "college-stub\tcollege-name\tcollege-name-short\tcollege-description\tcollege-url\n";
    for($i = 0; $i < count($college[0]); $i++) {
      $id             = $this->get_clean_data($college[1][$i]);
      $url            = $this->get_clean_data($college[2][$i]);
      $name           = $this->get_clean_data($college[3][$i]);
      $colleges_file .= "$id\t$name\t\t\t$url\n";
    }
    $this->write_file('grad_colleges.tsv', $colleges_file);
  }

}

?>
