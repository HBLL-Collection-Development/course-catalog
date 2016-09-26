<?php
/**
  * Get University data on colleges, departments, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

class undergrad implements course_catalog {
  use common;

  public function colleges() {
    $colleges = new undergrad_colleges;
    $colleges->get_colleges();
  }

  public function departments() {
    $departments = new undergrad_departments;
    $departments->get_departments();
  }

  public function programs() {
    $programs = new undergrad_programs;
    $programs->get_programs();
  }

  public function courses() {
    $courses = new undergrad_courses;
    $courses->get_courses();
  }

  public function faculty() {
    $faculty = new undergrad_faculty;
    $faculty->get_faculty();
  }

}

?>
