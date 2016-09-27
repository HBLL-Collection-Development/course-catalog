<?php
/**
  * Get University data on colleges, departments, programs, and courses
  *
  * @author Jared Howland <jared_howland@byu.edu>
  * @version 2016-03-04
  * @since 2016-02-29
  *
  */

trait common {

  /**
   * Use CURL to grab data from course catalogs
   *
   * Undergraduate catalog posts query strings
   * Graduate catalog does not post query strings
   * If that changes, this function will also need to change the if…else logic
   *
   * @access protected
   * @param array $query An associative array of all query values
   * @return string Contents of requested page
   **/
  protected function get_data($url, $query = NULL) {
    $ch = curl_init();
    $options = array(CURLOPT_COOKIESESSION => true,
                     CURLOPT_COOKIEJAR => config::get('cookie_jar'),
                     CURLOPT_COOKIEFILE => config::get('cookie_jar'),
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_URL => $url
                    );
    if($query) {
      $options[CURLOPT_POST] = true;
      $options[CURLOPT_POSTFIELDS] = http_build_query($query);
    }
    curl_setopt_array($ch, $options);
    if(!$result = curl_exec($ch)) {
      throw new Exception(curl_errno($ch) . ': ' . curl_error($ch));
    } else {
      return $result;
    }
  }

  /**
   * Cleans ugly data from course catalogs
   *
   * @access protected
   * @param string $data Data to be cleaned
   * @return string Cleaned data
   **/
  protected function get_clean_data($data) {
    $search = array('CREDITS:', 'DESCRIPTION:', '&amp;', '&#039', '&#160;', "';", '&amp;#160;', '&amp;#039;');
    $replace = array('', '', '&', "'", ' ', "'", '', "'");
    return trim(str_replace($search, $replace, $data));
  }

  /**
   * Converts URL path to stubs to be used as unique identifiers for colleges,
   * departments, programs, courses, and faculty
   *
   * It takes an URL structure (/path/to/resource) and converts all '/' (except
   * for the first one) into a tab to make them tab-separated values
   *
   * Some URL structures in a data set are different from the rest of the data
   * Those exceptions are handled in the calling function rather than here
   *
   * @access protected
   * @param string $url URL path to convert to tab-separated stubs
   * @return string Stubs
   **/
  protected function get_stubs($url) {
    return str_replace('/', "\t", substr($url, 1));
  }

  /**
   * Parses variable credit hour strings
   *
   * @access protected
   * @param string $hours Credit hours for the course
   * @return array Array with the minimum and maximum number of credit hours available
   **/
  protected function get_credit_hours($hours) {
    $hours = explode('-', $hours);
    $min = $hours[0];
    $max = empty($hours[1]) ? $min : $hours[1];
    return array('min' => $min, 'max' => $max);
  }

  /**
   * Writes data to appropriate file
   *
   * @access protected
   * @param string $file_name Name of file inside directory defined
   *                          in the config file ('data_directory')
   * @param string $data String containing all data to write to file
   *                     Will overwrite any existing data
   * @return null
   **/
  protected function write_file($file_name, $data, $append = false) {
    $rights = $append ? 'a' : 'w';
    $file_name = '.' . config::get('data_directory') . $file_name;
    if(!$handle = fopen($file_name, $rights)) {
      echo "Cannot open file '$file_name'";
      exit;
    }
    if(fwrite($handle, $data) === FALSE) {
      echo "Cannot write to file '$file_name'";
      exit;
    }
    fclose($handle);
  }
}

?>
