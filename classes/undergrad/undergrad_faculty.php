<?php
/**
  * Get University data on colleges, departments, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

class undergrad_faculty extends undergrad {
  use common;

  public function get_faculty($page_number = 0) {
    $page_count = 1;
    $header = "college-stub\tdepartment-stub\tfaculty-stub\tfaculty-name\tfaculty-rank\tdepartment-name\tfaculty-url\tfaculty-degree\tfaculty-school\tfaculty-degree-year\tfaculty-discipline\n";
    $this->write_file('undergrad_faculty.tsv', $header);
    for($t = 0; $t <= $page_count; $t++) {
      // Query needed to get college information from undergraduate catalog
      // If this no longer returns data, view the XHR data when navigating
      // the undergradaute catalog to find relevant query strings
      $query = array('keys' => '',
                     'view_name' => 'faculty_search',
                     'view_display_id' => 'block',
                     'page' => $page_number);
      // Get PHP array data from JSON in data
      $faculty = json_decode($this->get_data(config::get('undergrad_catalog_url'), $query), true);
      $data = $faculty[1]['data'];
      // Scrape data for relevant information
      // WARNING: very finicky and highly dependent on code in the
      //          Undergraduate Course Catalog website
      preg_match_all("#<a href=\"(.*?)\">(.*?)</a>.*?<em class=\"field-content\">(.*?)</em>.*?<div class=\"views-field views-field-field-owner\">.*?<div class=\"field-content\">(.*?)</div>#uism", $data, $faculty_names, PREG_PATTERN_ORDER);
      preg_match_all("#<li class=\"pager-last last\"><a title=\"Go to last page\" href=\"/views/ajax\?keys=.*?page=(.*?)\">#uism", $data, $pages);
      $page_count = $pages[1][0];
      $this->write_faculty($faculty_names);
      $page_number++;
    }
  }

  private function write_faculty($data) {
    $faculty = null;
    $num_lines = count($data[1]);
    for($i = 0; $i <= $num_lines; $i++) {
      $url         = $this->get_clean_data($data[1][$i]);
      $name        = $this->get_clean_data($data[2][$i]);
      $rank        = $this->get_clean_data($data[3][$i]);
      $department  = $this->get_clean_data($data[4][$i]);
      // Some courses only have a college and no department so leave
      // department-stub column empty if there are only 2 stubs instead of 3
      $stubs_count = substr_count($url, '/');
      $stubs       = ($stubs_count == 3) ? $this->get_stubs($url) : str_replace('/', "\t\t", substr($url, 1));
      if(!empty($name)) {
        $faculty    .= ($i == $num_lines - 1) ? "$stubs\t$name\t$rank\t$department\t$url\t\t\t\t\n" : "$stubs\t$name\t$rank\t$department\t$url\t\t\t\t\n";
      }
    }
    $this->write_file('undergrad_faculty.tsv', $faculty, true);
  }

}

?>
