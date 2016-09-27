<?php
/**
  * Get University data on colleges, courses, courses, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

class grad_courses extends grad {
  use common;

  public function get_courses() {
    $this->write_courses($this->get_data('http://graduatestudies.byu.edu/content/courses-department'));
  }

  private function write_courses($data) {
    // Scrape data for relevant information
    // WARNING: very finicky and highly dependent on code in the
    //          graduate Course Catalog website
    preg_match("#<select.*?Select Courses to view by department.*?</select>#uism", $data, $courses);
    preg_match_all("#<option value=\"([A-Za-z0-9].*?)::(.*?)\">\s*(.*?)\s*</option>#uism", $courses[0], $course_urls);
    preg_match("#https*://.*?/#ui", config::get('graduate_catalog_url'), $course_urls_root);
    // TSV file header
    $courses_file = "college-stub\tdepartment-stub\tprogram-stub\tdepartment-name\tmin-credit-hours\tmax-credit-hours\tcourse-description\tcourse-number\tcourse-name\tcourse-url\n";
    for($i = 0; $i < count($course_urls[2]); $i++) {
      $url = substr($course_urls_root[0], 0, -1) . $this->get_clean_data($course_urls[2][$i]);
      $course_page = $this->get_data($url);
      preg_match_all("#.*?Department Reference:.*?<a href=\"(.*?)\">(.*?)</a>#uism", $course_page, $department);
      $department_url = $department[1][0];
      $department_stubs = explode('/', $department_url);
      $department_stub  = end($department_stubs);
      $department_name = $department[2][0];
      preg_match_all("#<div class=\"field field-name-field-coursebydep-title field-type-text field-label-hidden\"><div class=\"field-items\"><div class=\"field-item .*?\">(.*?)</div></div></div><div class=\"field field-name-field-coursebydep-course-number field-type-text field-label-hidden\"><div class=\"field-items\"><div class=\"field-item .*?\">(.*?)</div></div></div>(<div class=\"field field-name-field-coursebydep-course-suffix field-type-text field-label-hidden\"><div class=\"field-items\"><div class=\"field-item even\">(.*?)</div></div></div>)*(<div class=\"field field-name-field-coursebydep-name field-type-text field-label-hidden\"><div class=\"field-items\"><div class=\"field-item .*?\">(.*?)</div></div></div>)*(<div class=\"field field-name-field-coursebydep-cred field-type-text field-label-hidden\"><div class=\"field-items\"><div class=\"field-item.*?\">(.*?)</div></div></div>)*(<div class=\"field field-name-field-coursebydep-description field-type-text-long field-label-hidden\"><div class=\"field-items\"><div class=\"field-item.*?\">(.*?)</div></div></div>)*#uism", $course_page, $course_list);
      for($j = 0; $j < count($course_list[0]); $j++) {
        $course_prefix       = $this->get_clean_data($course_list[1][$j]);
        $course_number       = $this->get_clean_data(str_replace("</div></div></div><div class=\"field field-name-field-coursebydep-course-suffix field-type-text field-label-hidden\"><div class=\"field-items\"><div class=\"field-item even\">", ' ', $course_list[2][$j]));
        $course_letter       = $this->get_clean_data($course_list[4][$j]);
        $course_name         = str_replace('.', '', $this->get_clean_data($course_list[6][$j]));
        $course_credit_hours = $this->get_credit_hours($this->get_clean_data($course_list[8][$j]));
        $min_credit_hours    = $course_credit_hours['min'];
        $max_credit_hours    = $course_credit_hours['max'];
        $course_description  = $this->get_clean_data($course_list[10][$j]);
        $courses_file       .= "\t$department_stub\t\t$department_name\t$min_credit_hours\t$max_credit_hours\t$course_description\t$course_prefix $course_number$course_letter\t$course_name\t\n";
      }
    }
    $this->write_file('grad_courses.tsv', $courses_file);
  }

}

?>
