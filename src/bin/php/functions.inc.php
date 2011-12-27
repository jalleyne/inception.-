<?php
/**
 * Copyright 2011 Jovan Alleyne <me@jalleyne.ca>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */



/**
* User defined functions
*/


/*
 *
 */
function pprint_r($val){
	echo "<pre>";
	print_r($val);
	echo "</pre>";
	return TRUE;
}

/*
 *
 */
function long_date($date){
	return date("F j, Y",strtotime($date));
}


/*
 *
 */
function hst($val){
	return 0.13*(float)$val;
}

/*
 *
 */
function parse_uri_parts($uri){
	return array_merge(
				array(),
				array_filter(explode('/',$uri))
			);
}

function get_uri_value($uri,$index){
	$parts =  parse_uri_parts($uri);
	return $parts[$index];
}

/*
 *
 */
function process_form_post($uri,$content_type="application/x-www-form-urlencoded"){
	if( isset($_POST) && count($_POST) ){
		
		/* */
		$api = new HTTPRequest();
		$api->base_uri = WS_URI;

		/* */
		$data = array_merge(array(),$_POST);

		/* */
		if( isset($data['response_redirect']) )
			unset($data['response_redirect']);

		/* */
		return $api->post(
						$uri,
						$data,
						'json',
						$content_type
					);
	}
	return NULL;
}


/*
 *
 */
function route_as_inception_request($uri){	
	/* Instanciate InceptionRESTfulDataApplication class */
	$i = new InceptionRESTfulDataApplication($uri);

	/* Load Route map from file system */
	$i->loadRouteMap(ROUTE_FILE_PATH);

	/* Parse loaded XML Route map */
	if( $map=$i->parseRouteMap() ){

		/* Read request data based on request method */
		$i->readRequestData();

		/* Determine if user can access resource based on Route definition */
		if( $i->canAccess() ){

			/* Validate request parameters */
			if( $errors = $i->validateRequest() ){

				/* */
				$response = new HTTPRequestError(
									'ResourceParameterException',
									'Required parameters not included.'
								);
				$response->data = $errors;
				return $response;
			}
			else{
				/* Delegate request to mapped handler */
				return $i->delegateRequest();
			}
		}
		/* Respond with access denied */
		else {
			return new HTTPRequestError(
								'AuthenticationException',
								'Access to the requested resource has been denied.'
							);
		}
	}

	/* Route was found but no method mapped to $_SERVER['REQUEST_METHOD'] */
	else if( $map === FALSE ){
		/* */
		return new HTTPRequestError(
							'ResourceHandlerException',
							'The request [:'.$i->uri.'] does not support '.
							'the request method [:'.$_SERVER['REQUEST_METHOD'].'].'
						);

	}

	/* No Route found in mapping data */
	else {
		/* */
		return new HTTPRequestError(
							'ResourceHandlerException',
							'The request [:'.$i->uri.'] does not exist.'
						);
	}
}

/*
 *
 */
function as_json($response){
	/* */
	return json_decode(
			(string)($response),TRUE);
}


/*
 *
 */
function get_user(){
	/* */
	$api = new HTTPRequest();
	$api->base_uri = WS_URI;

	/* */
	$r = $api->get(
					'/me/'
				);
	
	/* */
	if( isset($r['data']) )
		return $r['data'];
	else return NULL;
}



/*
 *
 */
$queued_scripts = array();

function queue_js($scripts){
	global $queued_scripts;
	if( is_array($scripts) )
		$queued_scripts = array_merge($queued_scripts,$scripts);
	else $queued_scripts[] = $scripts;
}

function print_queued_scripts(){
	global $queued_scripts;
	foreach ( $queued_scripts as $script )
		echo "<script src=\"$script\"></script>";
} 

/*
 *
 */
$queued_inline_js = array();

function queue_inline_js($script){
	global $queued_inline_js;
	$queued_inline_js[] = $script;
}

function print_queued_inline_js(){
	global $queued_inline_js;
	foreach ( $queued_inline_js as $script )
		echo PHP_EOL."<script type=\"text/javascript\">".$script."</script>".PHP_EOL;
}


/*
 *
 */


/**
 *
 */
function write_html_select($data,$label,$value=NULL,$select=NULL){
	foreach( $data as $opt ){
		$opt_val	= $opt[$value?$value:$label];
		$opt_label 	= $opt[$label];
		$selected	= $select==$opt_val?' selected="selected"':'';
		echo '<option value="'.$opt_val.'"'.$selected.'>'.$opt_label.'</option>';
	}
}





/**
 *
 */
function build_multipart_body($data,$boundary=NULL){
	/* */
	$boundary = !$boundary?generate_multipart_boundary():$boundary;
	$boundary = '--'.$boundary;
	/* */
	$request_body = $boundary.PHP_EOL;
	/* */
	$request_values = array();
	foreach( $data as $k=>$v )
		$values = build_multipart_value($request_values,$k,$v,$boundary);
		
	/* */
	foreach( $_FILES as $k=>$file ){
		/* */
		if( is_array($file['error']) )
			$k .= '[]';
		/* */
		/* */
		if( is_array($file['error']) ) $filename = $file['name'][0];
		else $filename = $file['name'];
		
		$request_values[] = "Content-Disposition: form-data; name=\"$k\"; filename=\"$filename\" ";
			
		/* */
		$request_values[] = "Content-Type: application/octet-stream".PHP_EOL;
		/* */
		if( is_array($file['error']) ) $tmp_name = $file['tmp_name'][0];
		else $tmp_name = $file['tmp_name'];
			
		$request_values[] = @file_get_contents($tmp_name);
		
		/* */	
		$request_values[] = $boundary;
	}
		
	/* */
	$request_body .= implode(PHP_EOL,$request_values);
	/* */
	$request_body .= '--'.PHP_EOL;
	
	/* */
	return "Content-Length: ".strlen($request_body).PHP_EOL.$request_body;
}


function build_multipart_value(&$request,$key,$value,$boundary,$nest=0){
	if( is_array($value) ){
		/* */
		foreach( $value as $k=>$v)
			build_multipart_value($request,$key,$v,$boundary,$nest+1);
	}
	else {
		/* */
		for($i=0;$i<$nest;$i++)
			$key .= '[]';	
		/* */
		$request[] = "Content-Disposition: form-data; name=\"$key\"".PHP_EOL;
		$request[] = $value;
		$request[] = $boundary;
	}
}

/**
 *
 */
function generate_multipart_boundary(){
	return '---------------------------'.base64_encode(md5(microtime().rand(0,getrandmax())));
}




/**
 *
 */
function write_ini_file($assoc_arr, $path, $has_sections=TRUE) { 
	
	
    $content = ""; 
    if ($has_sections) { 
        foreach ($assoc_arr as $key=>$elem) { 
            $content .= "[".$key."]\n"; 
            foreach ($elem as $key2=>$elem2) { 
                if(is_array($elem2)) 
                { 
                    for($i=0;$i<count($elem2);$i++) 
                    { 
                        $content .= $key2."[] = \"".$elem2[$i]."\"\n"; 
                    } 
                } 
                else if($elem2=="") $content .= $key2." = \n"; 
                else $content .= $key2." = \"".$elem2."\"\n"; 
            } 
        } 
    } 
    else { 
        foreach ($assoc_arr as $key=>$elem) { 
            if(is_array($elem)) 
            { 
                for($i=0;$i<count($elem);$i++) 
                { 
                    $content .= $key2."[] = \"".$elem[$i]."\"\n"; 
                } 
            } 
            else if($elem=="") $content .= $key2." = \n"; 
            else $content .= $key2." = \"".$elem."\"\n"; 
        } 
    } 

    if (!$handle = fopen($path, 'w')) { 
        return false; 
    } 
    if (!fwrite($handle, $content)) { 
        return false; 
    } 
    fclose($handle); 
    return true; 
}



function generate_ini_settings_fields($config){
	/* */
	$fields = array();
	foreach( $config as $section=>$settings ){
		
		/* */
		$section_label = str_replace('_',' ',$section);
		
		/* */
		$fields[] = "<fieldset>";
		$fields[] = "<legend>$section_label</legend>";
		/* */
		$fields[] = "<ol>";
		/* */
		foreach( $settings as $key=>$value )
			write_ini_settings_input($key,$value,$fields,$section);

		/* */
		$fields[] = "</ol>";
		$fields[] = "</fieldset>";
	}
	/* */
	return implode(PHP_EOL,$fields);
}


function write_ini_settings_input($name,$value,&$form=array(),$section=NULL){
	/* */
	if( $section ){
		$field_name = $section."[$name]";
	}
	/* */
	$label 	= str_replace('_',' ',$name);
	$id 	= 'settings_'.str_replace(' ','_',$label);
	/* */
	$form[] = "	<li>";
	$form[] = "<label title=\"This variable is displayed in the title of the browser among other places on the site\" for=\"$id\">$name</label>";
	
	$form[] = "<textarea name=\"$field_name\" id=\"$id\">$value</textarea>";
	//$form[] = "<input id=\"siteName\" class=\"required\" type=\"text\" size=\"60\" value=\"$value\" name=\"siteName\">";
	$form[] = "	</li>";
	
	return $form;
}