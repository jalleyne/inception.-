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
 * Utility service to rotate image. The successfully
 * rotated image is retured as <code>data:image:jpeg;base64,</code>.
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */
class ImageEditRequestHandler {

    
    public function rotateImageResponder($request_data){
    	
    	/* */
    	$imgstr = str_ireplace('data:image/jpeg;base64,','',$request_data['img']);
    	
    	/* */
    	$img = new SimpleImage();
    	$img->fromString($imgstr);
    	$img->rotate(intval($request_data['angle']));
    	
    	if( isset($request_data['maxwidth']) ){
    		$img->resizeToWidth((int)$request_data['maxwidth']);
    	}
    	
    	/* */
    	return array(
					'img' => 'data:image/jpeg;base64,'.(string)$img 
				);
    }
}