<?php
/**
  * Get University data on colleges, departments, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

interface course_catalog {
  public function colleges();
  public function departments();
  public function programs();
  public function courses();
  public function faculty();
}

?>
