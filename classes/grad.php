<?php
/**
  * Get University data on colleges, departments, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-06
  * @since 2016-03-06
  *
  */

class grad implements course_catalog {
  use common;

  public function colleges() {
    $colleges = new grad_colleges;
    $colleges->get_colleges();
  }

  public function departments() {
    $departments = new grad_departments;
    $departments->get_departments();
  }

  public function programs() {
    $programs = new grad_programs;
    $programs->get_programs();
  }

  public function courses() {
    $courses = new grad_courses;
    $courses->get_courses();
  }

  public function faculty() {
    $faculty = new grad_faculty;
    $faculty->get_faculty();
  }

}

?>
