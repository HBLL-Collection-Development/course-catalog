<?php
/**
  * Get University data on colleges, courses, courses, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

class class_schedule {
  use common;

  var $year;
  var $semester;

  public function get_schedule($year = 2016, $semester = 1) {
    $this->year = $year;
    $this->semester = $semester;
    $header = "course-id\tunknown-id\tcourse-name-short\tcourse-credit-type\tcourse-number\tcourse-sub-number\tsection-number\tunknown\tcourse-type\tcourse-name-long\tteacher-name\tcredit-hours\tdays-taught\thours-taught-1\thours-taught-2\tlocation-taught\tsection-notes\tseats-available\twait-list";
    $this->write_file($year.$semester . '_class_schedule.tsv', $header);
    $departments = $this->get_semester_metadata();
    $this->get_sections($departments);
  }

  private function write_schedule($data) {
    $semester = $this->year.$this->semester;
    echo '<pre>';
    $data = preg_replace('/&<<<<[0-9]+?;/uiUmx', '', $data); // replace weird characters that look like tags (&<<<<225; and &<<<<160; for example)
    $data = strip_tags($data); // strip actual HTML/XML tags
    $data = preg_replace("#([0-9]{5})#uism", "\n$1", $data);
    $data = str_replace('#', "\t", $data);
    $data = str_replace('"', '', $data);
    $this->write_file($semester . '_class_schedule.tsv', $data, true);
  }

  private function get_sections($departments = null) {
    $semester = $this->year.$this->semester;
    foreach($departments as $department) {
      $department = urlencode(trim($department));
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "http://saasta.byu.edu/noauth/classSchedule/ajax/searchXML.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "BEGINTIME=&BLDG=&CATFILTER=&CREDITCOMP=&CREDITS=&CREDIT_TYPE=A&DAYFILTER=&DEPT=$department&DESCRIPTION=&ENDTIME=&INST=&SECTION_TYPE=&SEMESTER=$semester"
      ));

      if(!$result = curl_exec($curl)) {
        throw new Exception(curl_errno($curl) . ': ' . curl_error($curl));
      } else {
        curl_close($curl);
        $this->write_schedule($result);
      }
    }
  }

  private function get_semester_metadata() {
    $semester = $this->year.$this->semester;
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "http://saasta.byu.edu/noauth/classSchedule/ajax/getClassesByYearterm.php",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "YEARTERM=$semester",
      CURLOPT_HTTPHEADER => array(
        "accept: */*",
        "accept-encoding: gzip, deflate",
        "accept-language: en-US,en;q=0.8",
        "cache-control: no-cache",
        "content-type: application/x-www-form-urlencoded; charset=UTF-8",
        "dnt: 1",
        "origin: http//saasta.byu.edu",
        "referer: http//saasta.byu.edu/noauth/classSchedule/index.php",
        "x-requested-with: XMLHttpRequest"
      ),
    ));

    if(!$result = curl_exec($curl)) {
      throw new Exception(curl_errno($curl) . ': ' . curl_error($curl));
    } else {
      preg_match_all("#<name>.*?<!\[CDATA\[(.*?)\]\].*?</name>#uism", $result, $department_names);
      return $department_names[1];
    }
  }

}

?>
