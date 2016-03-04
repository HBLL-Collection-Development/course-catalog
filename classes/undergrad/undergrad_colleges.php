<?php
/**
  * Get University data on colleges, departments, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

class undergrad_colleges extends undergrad {
  use common;

  public function get_colleges() {
    // Query needed to get college information from undergraduate catalog
    // If this no longer returns data, view the XHR data when navigating
    // the undergradaute catalog to find relevant query strings
    $query = array('view_name' => 'colleges',
                   'view_display_id' => 'block_1');
    // Get PHP array data from JSON in data
    $colleges = json_decode($this->get_data($query), true);
    // Write data to file
    $this->write_colleges($colleges);
  }

  private function write_colleges($data) {
    // Grab only the relevant data
    $data = $data[1]['data'];
    // Scrape data for relevant information
    // WARNING: very finicky and highly dependent on code in the
    //          Undergraduate Course Catalog website
    preg_match_all("#<div class=\"field-content\">([A-Z].*?)</div>#ui", $data, $college_names);
    preg_match_all("#<span class=\"field-content\"><a href=\"(.*?)\">(.*?)</a></span>#ui", $data, $college_metadata);
    preg_match_all("#<span class=\"field-content\">([A-Z].*?)</span>#uism", $data, $college_descriptions);
    // TSV file header
    $colleges = "college-stub\tcollege-name\tcollege-name-short\tcollege-description\tcollege-url\n";
    for($i = 0; $i < count($college_names[1]); $i++) {
      $name        = $this->get_clean_data($college_names[1][$i]);
      $url         = $this->get_clean_data($college_metadata[1][$i]);
      $stubs       = $this->get_stubs($url);
      $name_short  = $this->get_clean_data($college_metadata[2][$i]);
      $description = str_replace("\n", '', $this->get_clean_data($college_descriptions[1][$i]));
      $description = str_replace("\t", '', $this->get_clean_data($description));
      $colleges   .= "$stubs\t$name\t$name_short\t$description\t$url\n";
    }
    $this->write_file('undergrad_colleges.tsv', $colleges);
  }

}

?>
