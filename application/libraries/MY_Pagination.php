<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @name MY_Pagination.php
 * @version 1.0
 * @author Joost van Veen www.accentinteractive.nl
 * @created: Sun Jul 27 16:27:26 GMT 2008 16:27:26
 *
 * Extends CI's pagination class (http://codeigniter.com/user_guide/libraries/pagination.html)
 * It sets some variables for configuration of the pagination class dynamically,
 * depending on the URI, so we don't have to substract the offset from the URI,
 * or set $config['base_url'] and $config['uri_segment'] manually in the controller
 *
 * Here is what is set by this extension class:
 * 1. $this->offset - the current offset
 * 2. $this->uri_segment - the URI segment to be used for pagination
 * 3. $this->base_url - the base url to be used for pagination
 * (where $this refers to the pagination class)
 *
 * The way this works is simple:
 * If there we use pagination, it must ALWAYS follow the following syntax and be
 * located at the END of the URI:
 * PAGINATION_SELECTOR/offset
 *
 * The PAGINATION_SELECTOR is a special string which we know will ONLY be in the
 * URI when paging is set. Let's say the PAGINATION_SELECTOR is 'Page' (since most
 * coders never use any capitals in the URI, most of the times any string with
 * a single capital character in it will suffice). 
 *
 * Example use (in controller):
 * // Initialize pagination
 * $config['total_rows'] = $this->db->count_all_results('my_table');
 * $config['per_page'] = 10; // You'd best set this in a config file, but hey
 * $this->pagination->initialize($config);
 * $this->data['pagination'] = $this->pagination->create_links();
 *
 * // Retrieve paginated results, using the dynamically determined offset
 * $this->db->limit($config['per_page'], $this->pagination->offset);
 * $query = $this->db->get('my_table');
 *
 */
class MY_Pagination extends CI_Pagination
{

    var $offset = 0;

    var $pagination_selector = 'Page';

    var $index_page;

    function MY_Pagination ()
    {
        parent::__construct();
        log_message('debug', "MY_Pagination Class Initialized");
        
        $this->index_page = config_item('index_page') != '' ? config_item('index_page') . '/' : '';
        $this->_set_pagination_offset();
    }

    /**
     * Set dynamic pagination variables in $CI->data['pagvars']
     *
     */
    function _set_pagination_offset ()
    {
        
        // Instantiate the CI super object so we have access to the uri class
        $CI = & get_instance();
        
        // Store pagination offset if it is set
        if (strstr($CI->uri->uri_string(), $this->pagination_selector)) {
            
            // Get the segment offset for the pagination selector
            $segments = $CI->uri->segment_array();
            
            // Loop through segments to retrieve pagination offset
            foreach ($segments as $key => $value) {
                
                // Find the pagination_selector and work from there
                if ($value == $this->pagination_selector) {
                    
                    // Store pagination offset
                    $this->offset = $CI->uri->segment($key + 1);
                    
                    // Store pagination segment
                    $this->uri_segment = $key + 1;
                    
                    // Set base url for paging. This only works if the
                    // pagination_selector and paging offset are AT THE END of
                    // the URI!
                    $uri = $CI->uri->uri_string();
                    $pos = strpos($uri, $this->pagination_selector);
                    $this->base_url = config_item('base_url') . $this->index_page . substr($uri, 0, $pos + strlen($this->pagination_selector));
                }
            
            }
        
        }
        else { // Pagination selector was not found in URI string. So offset is 0
            $this->offset = 0;
            $this->uri_segment = 0;
            $this->base_url = config_item('base_url') . $this->index_page . $CI->uri->uri_string() . '/' . $this->pagination_selector;
        
        }
    
    }

}
